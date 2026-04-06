<?php
/**
 * Stripe Payment Handler
 * Handles backend payment verification and processing
 * 
 * IMPORTANT: Set your Stripe Secret Key in .env file
 * STRIPE_SECRET_KEY=sk_live_xxxxx
 */

header('Content-Type: application/json');
require_once 'config.php';

// Verify Stripe secret key is configured
if (empty(STRIPE_SECRET_KEY)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Stripe is not properly configured. Please contact support.'
    ]);
    exit;
}

class StripePaymentHandler {
    private $secretKey;
    private $apiBase = 'https://api.stripe.com/v1';
    
    public function __construct($secretKey) {
        $this->secretKey = $secretKey;
    }
    
    /**
     * Verify Payment Intent
     * Confirms that payment was successful with Stripe
     */
    public function verifyPaymentIntent($paymentIntentId) {
        try {
            $response = $this->makeRequest('payment_intents/' . $paymentIntentId, 'GET');
            
            if ($response['status'] === 'succeeded') {
                return [
                    'success' => true,
                    'amount' => $response['amount'] / 100, // Convert from cents to dollars
                    'currency' => $response['currency'],
                    'status' => $response['status']
                ];
            } else if ($response['status'] === 'processing') {
                return [
                    'success' => false,
                    'status' => 'processing',
                    'message' => 'Payment is being processed. Please try again in a moment.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Payment verification failed.'
                ];
            }
        } catch (Exception $e) {
            logSecurityEvent('stripe_verification_error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error verifying payment: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Create Payment Intent
     * Initiates a payment for the given amount
     */
    public function createPaymentIntent($amountInDollars, $description) {
        try {
            $amountInCents = round($amountInDollars * 100);
            
            $data = [
                'amount' => $amountInCents,
                'currency' => 'usd',
                'description' => $description,
                'automatic_payment_methods[enabled]' => 'true'
            ];
            
            $response = $this->makeRequest('payment_intents', 'POST', $data);
            
            return [
                'success' => true,
                'client_secret' => $response['client_secret'],
                'payment_intent_id' => $response['id']
            ];
        } catch (Exception $e) {
            logSecurityEvent('stripe_intent_error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error creating payment: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Refund Payment
     * Refunds a previously successful payment
     */
    public function refundPayment($paymentIntentId, $amountInDollars = null) {
        try {
            $data = [];
            
            if ($amountInDollars) {
                $data['amount'] = round($amountInDollars * 100);
            }
            
            $data['payment_intent'] = $paymentIntentId;
            
            $response = $this->makeRequest('refunds', 'POST', $data);
            
            if ($response['status'] === 'succeeded') {
                logSecurityEvent('payment_refunded', ['payment_intent' => $paymentIntentId, 'amount' => $amountInDollars]);
                return [
                    'success' => true,
                    'refund_id' => $response['id']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Refund could not be processed.'
                ];
            }
        } catch (Exception $e) {
            logSecurityEvent('stripe_refund_error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error processing refund: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Make HTTP Request to Stripe API
     */
    private function makeRequest($endpoint, $method = 'GET', $data = []) {
        $url = $this->apiBase . '/' . $endpoint;
        
        $options = [
            'http' => [
                'header' => [
                    "Authorization: Bearer " . $this->secretKey,
                    "Content-Type: application/x-www-form-urlencoded"
                ],
                'method' => $method,
                'timeout' => 10
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true
            ]
        ];
        
        if ($method === 'POST' && !empty($data)) {
            $options['http']['content'] = http_build_query($data);
        }
        
        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new Exception('Failed to connect to Stripe API');
        }
        
        $decoded = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid response from Stripe API');
        }
        
        if (isset($decoded['error'])) {
            throw new Exception('Stripe Error: ' . $decoded['error']['message']);
        }
        
        return $decoded;
    }
}

// Handle incoming requests
$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? $input['action'] ?? null;

$stripeHandler = new StripePaymentHandler(STRIPE_SECRET_KEY);

switch ($action) {
    case 'verify':
        // Verify payment intent
        if (empty($input['payment_intent_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Payment intent ID required']);
            exit;
        }
        
        $result = $stripeHandler->verifyPaymentIntent($input['payment_intent_id']);
        echo json_encode($result);
        exit;
    
    case 'create_intent':
        // Create payment intent
        if (empty($input['amount']) || empty($input['description'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Amount and description required']);
            exit;
        }
        
        $result = $stripeHandler->createPaymentIntent($input['amount'], $input['description']);
        echo json_encode($result);
        exit;
    
    case 'refund':
        // Refund payment (admin only)
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }
        
        if (empty($input['payment_intent_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Payment intent ID required']);
            exit;
        }
        
        $amount = $input['amount'] ?? null;
        $result = $stripeHandler->refundPayment($input['payment_intent_id'], $amount);
        echo json_encode($result);
        exit;
    
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        exit;
}
?>

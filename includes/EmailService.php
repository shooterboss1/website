<?php
/**
 * Email Service Class
 * Handles order confirmations, password resets, and notifications
 */

class EmailService {
    private $host;
    private $port;
    private $username;
    private $password;
    private $fromAddress;
    private $fromName;
    
    public function __construct() {
        $this->host = MAIL_HOST;
        $this->port = MAIL_PORT;
        $this->username = MAIL_USERNAME;
        $this->password = MAIL_PASSWORD;
        $this->fromAddress = MAIL_FROM_ADDRESS;
        $this->fromName = BRAND_NAME;
    }
    
    /**
     * Send Order Confirmation Email
     */
    public function sendOrderConfirmation($customerEmail, $customerName, $orderId, $items, $totalAmount) {
        $subject = BRAND_NAME . " - Order Confirmation #$orderId";
        
        $itemsList = '';
        foreach ($items as $item) {
            $itemsList .= "
                <tr>
                    <td style='padding:10px; border-bottom:1px solid #ddd;'>{$item['product_name']}</td>
                    <td style='padding:10px; border-bottom:1px solid #ddd; text-align:center;'>{$item['quantity']}</td>
                    <td style='padding:10px; border-bottom:1px solid #ddd; text-align:right;'>\${$item['price']}</td>
                </tr>
            ";
        }
        
        $html = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; border: 1px solid #ddd; }
                    .header { background: #000; color: #fff; padding: 20px; text-align: center; }
                    .content { padding: 20px; }
                    .footer { background: #f9f9f9; padding: 15px; text-align: center; border-top: 1px solid #ddd; font-size: 12px; }
                    table { width: 100%; }
                    .btn { display: inline-block; padding: 10px 20px; background: #000; color: #fff; text-decoration: none; border-radius: 4px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>" . BRAND_NAME . "</h1>
                        <p>Order Confirmation</p>
                    </div>
                    <div class='content'>
                        <p>Hi <strong>$customerName</strong>,</p>
                        <p>Thank you for your order! Your order has been confirmed and will be processed shortly.</p>
                        
                        <h3>Order Details</h3>
                        <p><strong>Order ID:</strong> #$orderId</p>
                        <p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>
                        
                        <h3>Items Ordered</h3>
                        <table>
                            <thead>
                                <tr style='background: #f0f0f0;'>
                                    <th style='padding:10px; text-align:left;'>Product</th>
                                    <th style='padding:10px; text-align:center;'>Qty</th>
                                    <th style='padding:10px; text-align:right;'>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                $itemsList
                            </tbody>
                            <tfoot>
                                <tr style='font-weight: bold; font-size: 16px;'>
                                    <td colspan='2' style='padding:10px; text-align:right;'>Total:</td>
                                    <td style='padding:10px; text-align:right;'>\$" . number_format($totalAmount, 2) . "</td>
                                </tr>
                            </tfoot>
                        </table>
                        
                        <p style='margin-top: 20px;'>You'll receive a shipping confirmation email once your order ships.</p>
                        <p style='text-align: center; margin-top: 30px;'>
                            <a href='" . BASE_URL . "' class='btn'>Track Your Order</a>
                        </p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " " . BRAND_NAME . ". All rights reserved.</p>
                        <p>If you have any questions, please contact: <a href='mailto:" . ADMIN_EMAIL . "'>" . ADMIN_EMAIL . "</a></p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        return $this->send($customerEmail, $customerName, $subject, $html);
    }
    
    /**
     * Send Admin Notification (New Order)
     */
    public function sendAdminNotification($orderId, $customerEmail, $customerName, $totalAmount) {
        $subject = "[NEW ORDER] Order #$orderId from $customerName";
        
        $html = "
            <html>
            <body style='font-family: Arial; color: #333;'>
                <h2>New Order Received!</h2>
                <p><strong>Order ID:</strong> #$orderId</p>
                <p><strong>Customer:</strong> $customerName ($customerEmail)</p>
                <p><strong>Amount:</strong> \$" . number_format($totalAmount, 2) . "</p>
                <p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>
                <p><a href='" . BASE_URL . "admin.php'>View in Admin Panel</a></p>
            </body>
            </html>
        ";
        
        return $this->send(ADMIN_EMAIL, BRAND_NAME, $subject, $html);
    }
    
    /**
     * Send Password Reset Email
     */
    public function sendPasswordResetEmail($recipientEmail, $recipientName, $resetLink) {
        $subject = BRAND_NAME . " - Password Reset Request";
        
        $html = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .btn { display: inline-block; padding: 12px 24px; background: #000; color: #fff; text-decoration: none; border-radius: 4px; margin: 20px 0; }
                </style>
            </head>
            <body>
                <h2>Password Reset Request</h2>
                <p>Hi <strong>$recipientName</strong>,</p>
                <p>We received a request to reset your password. Click the button below to reset it:</p>
                <p><a href='$resetLink' class='btn'>Reset Password</a></p>
                <p style='color: #666; font-size: 12px;'>
                    This link will expire in 24 hours.<br>
                    If you didn't request this, please ignore this email.
                </p>
            </body>
            </html>
        ";
        
        return $this->send($recipientEmail, $recipientName, $subject, $html);
    }
    
    /**
     * Send Shipping Notification
     */
    public function sendShippingNotification($customerEmail, $customerName, $orderId, $trackingNumber, $trackingUrl) {
        $subject = BRAND_NAME . " - Your Order #$orderId Has Shipped!";
        
        $html = "
            <html>
            <body style='font-family: Arial; color: #333;'>
                <h2>Your Order Has Shipped!</h2>
                <p>Hi <strong>$customerName</strong>,</p>
                <p>Great news! Your order #$orderId is on its way.</p>
                
                <h3>Tracking Information</h3>
                <p><strong>Tracking Number:</strong> $trackingNumber</p>
                <p><a href='$trackingUrl' target='_blank'>Track Your Shipment</a></p>
                
                <p>You'll receive another notification when your package is delivered.</p>
            </body>
            </html>
        ";
        
        return $this->send($customerEmail, $customerName, $subject, $html);
    }
    
    /**
     * Core Send Method Using PHP Mail
     * For production, consider using SMTP or a service like SendGrid
     */
    private function send($toEmail, $toName, $subject, $htmlContent) {
        try {
            // Validate email
            if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                logSecurityEvent('invalid_email', ['email' => $toEmail]);
                return false;
            }
            
            // Set headers
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: " . $this->fromName . " <" . $this->fromAddress . ">\r\n";
            $headers .= "Reply-To: " . $this->fromAddress . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
            
            // Send email using PHP mail function
            if (mail($toEmail, $subject, $htmlContent, $headers)) {
                logSecurityEvent('email_sent', ['to' => $toEmail, 'subject' => $subject]);
                return true;
            } else {
                logSecurityEvent('email_failed', ['to' => $toEmail, 'subject' => $subject]);
                return false;
            }
        } catch (Exception $e) {
            logSecurityEvent('email_exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
?>

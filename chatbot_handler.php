<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($message)) {
        echo json_encode(['response' => 'I didn\'t catch that. Could you please repeat?']);
        exit;
    }

    $lowerMessage = strtolower($message);
    $response = "";

    // Basic Logic for Mock AI (Can be replaced with Gemini/OpenAI API call)
    if (strpos($lowerMessage, 'hello') !== false || strpos($lowerMessage, 'hi') !== false) {
        $response = "Hello! Welcome to What's Real Shall Prosper. How can I assist you with our premium collection today?";
    } elseif (strpos($lowerMessage, 'shipping') !== false) {
        $response = "We offer standard shipping (3-5 business days) and express shipping (1-2 business days). Shipping is free on orders over $150!";
    } elseif (strpos($lowerMessage, 'return') !== false || strpos($lowerMessage, 'refund') !== false) {
        $response = "You can return any unworn items within 30 days of purchase. Please visit our 'Returns' page for a prepaid label.";
    } elseif (strpos($lowerMessage, 'men') !== false) {
        $response = "Our Men's collection features premium hoodies, shirts, and accessories. You can view them at men.php!";
    } elseif (strpos($lowerMessage, 'women') !== false || strpos($lowerMessage, 'ladies') !== false) {
        $response = "Explore our Women's collection for elegant and modern styles. Check them out at women.php!";
    } elseif (strpos($lowerMessage, 'size') !== false) {
        $response = "Our items generally run true to size. You can find a detailed size guide on each product page.";
    } elseif (strpos($lowerMessage, 'who are you') !== false) {
        $response = "I am the What's Real Shall Prosper Digital Concierge.";
    } elseif (strpos($lowerMessage, 'brand') !== false || strpos($lowerMessage, 'about') !== false) {
        $response = "What's Real Shall Prosper represents the future of fashion. Premium quality, modern style.";
    } else {
        // Default "AI" response
        $responses = [
            "That's an interesting question! Let me check that for you.",
            "What's Real Shall Prosper is dedicated to premium quality. Is there a specific product you're looking for?",
            "I'm here to help! You can browse our New Arrivals to see the latest trends.",
            "Could you tell me more about what you're looking for?"
        ];
        $response = $responses[array_rand($responses)];
    }

    echo json_encode(['response' => $response]);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>


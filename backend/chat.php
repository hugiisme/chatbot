<?php
    session_start();
    require_once __DIR__ . '/../vendor/autoload.php';

    // use Dotenv\Dotenv;

    // Load environment variables
    // $dotenv = Dotenv::createImmutable(__DIR__ . '/../'); // thêm "/../" để escape ra ngoài vì .env nằm ngoài backend
    // $dotenv->load();

    // Retrieve API key
    // $apiKey = $_ENV['GEMINI_API_KEY'];
    
    // Retrieve API key from environment variables
    $apiKey = getenv('GEMINI_API_KEY');

    if (!$apiKey) {
        echo json_encode(['error' => 'GEMINI_API_KEY not found in environment variables']);
        exit;
    }

    // Initialize conversation history
    if (!isset($_SESSION['conversation_history'])) {
        $_SESSION['conversation_history'] = [];
    }

    // Retrieve user message from POST data
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['message'])) {
        echo json_encode(['error' => 'No message received from the frontend']);
        exit;
    }

    $userMessage = $data['message'];
    
    // // Add user message to conversation history
    // $_SESSION['conversation_history'][] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

    // System message
    $systemMessage = "Bạn tên là Hugi - AI chatbot thân thiện hỗ trợ trẻ tự kỉ trong việc giải quyết các vấn đề đời sống. Hãy trả lời chính xác câu hỏi được đặt ra, khi không biết, hãy nói 'Xin lỗi, mình không biết'\n\n"; // Note the newline characters!

    // Combine system message and user message *only for the first turn*
    if (empty($_SESSION['conversation_history'])) {
        $combinedMessage = $systemMessage . $userMessage;
        $_SESSION['conversation_history'][] = ['role' => 'user', 'parts' => [['text' => $combinedMessage]]];
    } else {
        // Subsequent turns: just add the user message
        $_SESSION['conversation_history'][] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];
    }

    $modelName = 'gemini-2.0-flash';
    $googleApiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$apiKey}";
    //               https://[service].[domain]/[version]/[resource]?key=[API_key]&[parameters]

    // Prepare the payload with the conversation history
    $payload = json_encode([
        'contents' => $_SESSION['conversation_history'],
        'generationConfig' => [ 
            'temperature' => 1,
            'topP' => 0.95,
            'topK' => 40,
            'maxOutputTokens' => 8192,
        ],
    ]);

    // Initialize cURL
    $ch = curl_init($googleApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for errors in the cURL request
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        echo json_encode(['error' => 'Request to Google API failed: ' . $error]);
        exit;
    }

    // Close cURL session
    curl_close($ch);

    // Decode the API response
    $responseData = json_decode($response, true);

    // Log the complete API response for debugging
    file_put_contents('response_log.txt', print_r($responseData, true), FILE_APPEND);

    // Check for errors in the API response
    if (isset($responseData['error'])) {
        echo json_encode(['error' => 'Google AI API error: ' . $responseData['error']['message'], 'details' => $responseData]);
        exit; 
    }

    // Extract and return the bot's response
    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        $botResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];

        // Add bot response to conversation history
        $_SESSION['conversation_history'][] = ['role' => 'model', 'parts' => [['text' => $botResponse]]];

        echo json_encode(['response' => $botResponse]);
    } else {
        echo json_encode(['error' => 'No response found in Google AI API response', 'details' => $responseData]);
    }
?>
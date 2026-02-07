<?php
// web/realtime_sdp.php
// Browser-friendly proxy for OpenAI Realtime SDP exchange (avoids CORS + keeps API key server-side).

ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);
ob_end_clean();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(200);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	header('Content-Type: application/json; charset=UTF-8');
	echo json_encode(['success' => false, 'error' => 'Method not allowed']);
	exit;
}

$raw = file_get_contents('php://input') ?: '';
$body = json_decode($raw, true);
if (!is_array($body)) {
	// allow form-encoded fallback
	$body = $_POST;
}

$sdp = (string)($body['sdp'] ?? '');
$model = (string)($body['model'] ?? 'gpt-4o-realtime-preview-2024-12-17');
if ($sdp === '') {
	http_response_code(400);
	header('Content-Type: application/json; charset=UTF-8');
	echo json_encode(['success' => false, 'error' => 'Missing sdp']);
	exit;
}

$openaiKey = getenv('OPENAI_API_KEY') ?: '';
if ($openaiKey === '') {
	http_response_code(500);
	header('Content-Type: application/json; charset=UTF-8');
	echo json_encode(['success' => false, 'error' => 'OpenAI API key not configured']);
	exit;
}

$url = 'https://api.openai.com/v1/realtime?model=' . rawurlencode($model);
$ch = curl_init($url);
curl_setopt_array($ch, [
	CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => $sdp,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_TIMEOUT => 25,
	CURLOPT_HTTPHEADER => [
		'Authorization: Bearer ' . $openaiKey,
		'Content-Type: application/sdp',
	],
]);
$answerSdp = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// OpenAI Realtime can return 201 with a valid SDP answer. Treat any 2xx as success.
if ($httpCode < 200 || $httpCode >= 300 || !$answerSdp) {
	http_response_code(502);
	header('Content-Type: application/json; charset=UTF-8');
	echo json_encode([
		'success' => false,
		'error' => 'Realtime SDP exchange failed',
		'http_code' => $httpCode,
		'details' => mb_substr((string)$answerSdp, 0, 400),
	]);
	exit;
}

header('Content-Type: application/sdp');
echo $answerSdp;



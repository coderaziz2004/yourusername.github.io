<?php
// web/heygen_session_stop.php
// Best-effort server-side stop endpoint (some deployments prefer stopping via SDK client-side).
// If you configure HEYGEN_STOP_SESSION_PATH, we'll call it; otherwise we just return ok.

ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);
ob_end_clean();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(200);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['success' => false, 'error' => 'Method not allowed']);
	exit;
}

$apiKey = getenv('LIVEAVATAR_API_KEY') ?: (getenv('HEYGEN_API_KEY') ?: '');
if ($apiKey === '') {
	http_response_code(500);
	echo json_encode(['success' => false, 'error' => 'LIVEAVATAR_API_KEY not configured']);
	exit;
}

$raw = file_get_contents('php://input') ?: '';
$body = json_decode($raw, true);
if (!is_array($body)) $body = $_POST;

$sessionId = trim((string)($body['session_id'] ?? ''));
$stopPath = getenv('LIVEAVATAR_STOP_PATH') ?: (getenv('HEYGEN_STOP_SESSION_PATH') ?: '');

// If we don't know the stop endpoint, we still return success to allow frontend cleanup.
if ($stopPath === '' || $sessionId === '') {
	echo json_encode([
		'success' => true,
		'stopped' => false,
		'note' => ($sessionId === '') ? 'No session_id provided; frontend should stop via SDK' : 'HEYGEN_STOP_SESSION_PATH not set; frontend should stop via SDK',
	]);
	exit;
}

$apiBase = rtrim(getenv('LIVEAVATAR_API_BASE') ?: 'https://api.liveavatar.com', '/');
$url = $apiBase . $stopPath;

$ch = curl_init($url);
curl_setopt_array($ch, [
	CURLOPT_POST => true,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_TIMEOUT => 20,
	CURLOPT_HTTPHEADER => [
		'X-API-KEY: ' . $apiKey,
		'Content-Type: application/json',
		'Accept: application/json',
	],
	CURLOPT_POSTFIELDS => json_encode(['session_id' => $sessionId]),
]);
$resp = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode < 200 || $httpCode >= 300) {
	http_response_code(502);
	echo json_encode([
		'success' => false,
		'error' => 'HeyGen stop request failed',
		'http_code' => $httpCode,
		'details' => mb_substr((string)$resp, 0, 500),
	]);
	exit;
}

echo json_encode(['success' => true, 'stopped' => true]);


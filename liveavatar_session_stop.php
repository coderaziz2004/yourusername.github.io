<?php
// web/liveavatar_session_stop.php
// Best-effort stop for LiveAvatar sessions to release concurrency/credits.

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

$raw = file_get_contents('php://input') ?: '';
$body = json_decode($raw, true);
if (!is_array($body)) $body = $_POST;

$sessionToken = trim((string)($body['session_token'] ?? $body['token'] ?? ''));
$sessionId = trim((string)($body['session_id'] ?? ''));

if ($sessionToken === '' && $sessionId === '') {
	http_response_code(400);
	echo json_encode(['success' => false, 'error' => 'Missing session_token or session_id']);
	exit;
}

$base = rtrim(getenv('LIVEAVATAR_API_BASE') ?: 'https://api.liveavatar.com', '/');
$stopPath = getenv('LIVEAVATAR_STOP_PATH') ?: '/v1/sessions/stop';
$url = $base . $stopPath;

$payload = new stdClass();
if ($sessionId !== '') $payload = ['session_id' => $sessionId];

$headers = [
	'Content-Type: application/json',
	'Accept: application/json',
];
if ($sessionToken !== '') $headers[] = 'Authorization: Bearer ' . $sessionToken;

$ch = curl_init($url);
curl_setopt_array($ch, [
	CURLOPT_POST => true,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_TIMEOUT => 20,
	CURLOPT_HTTPHEADER => $headers,
	CURLOPT_POSTFIELDS => json_encode($payload),
]);
$resp = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErrNo = curl_errno($ch);
$curlErr = $curlErrNo ? curl_error($ch) : null;
curl_close($ch);

if ($httpCode < 200 || $httpCode >= 300) {
	// Non-fatal; still allow frontend cleanup.
	echo json_encode([
		'success' => false,
		'http_code' => $httpCode,
		'curl_errno' => $curlErrNo,
		'curl_error' => $curlErr,
		'details' => mb_substr((string)$resp, 0, 400),
	]);
	exit;
}

echo json_encode(['success' => true]);


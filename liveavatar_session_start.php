<?php
// web/liveavatar_session_start.php
// Starts a LiveAvatar session and returns LiveKit room URL + room token.
// This follows the LiveAvatar quickstart pattern: token -> start -> join LiveKit.

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
if ($sessionToken === '') {
	http_response_code(400);
	echo json_encode(['success' => false, 'error' => 'Missing session_token']);
	exit;
}

$base = rtrim(getenv('LIVEAVATAR_API_BASE') ?: 'https://api.liveavatar.com', '/');
$startPath = getenv('LIVEAVATAR_START_PATH') ?: '/v1/sessions/start';
$url = $base . $startPath;

$payload = new stdClass();
// Some deployments require session_id on start; include if provided.
if ($sessionId !== '') {
	$payload = ['session_id' => $sessionId];
}

$ch = curl_init($url);
curl_setopt_array($ch, [
	CURLOPT_POST => true,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_TIMEOUT => 25,
	CURLOPT_HTTPHEADER => [
		'Authorization: Bearer ' . $sessionToken,
		'Content-Type: application/json',
		'Accept: application/json',
	],
	CURLOPT_POSTFIELDS => json_encode($payload),
]);
$resp = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErrNo = curl_errno($ch);
$curlErr = $curlErrNo ? curl_error($ch) : null;
curl_close($ch);

if ($httpCode < 200 || $httpCode >= 300) {
	http_response_code(502);
	echo json_encode([
		'success' => false,
		'error' => 'LiveAvatar start session failed',
		'http_code' => $httpCode,
		'curl_errno' => $curlErrNo,
		'curl_error' => $curlErr,
		'details' => mb_substr((string)$resp, 0, 700),
	]);
	exit;
}

$decoded = json_decode($resp ?: '{}', true) ?: [];
$data = $decoded['data'] ?? $decoded;

$livekit = null;
if (is_array($data)) {
	$livekit = $data['livekit'] ?? $data['live_kit'] ?? $data['liveKit'] ?? null;
}

$roomUrl =
	(is_array($data) ? ($data['room_url'] ?? $data['livekit_url'] ?? $data['url'] ?? null) : null) ??
	(is_array($livekit) ? ($livekit['room_url'] ?? $livekit['url'] ?? $livekit['livekit_url'] ?? null) : null) ??
	(is_array($data) && is_array($data['room'] ?? null) ? (($data['room']['url'] ?? $data['room']['room_url'] ?? null)) : null);

$roomToken =
	(is_array($data) ? ($data['room_token'] ?? $data['token'] ?? $data['livekit_token'] ?? null) : null) ??
	(is_array($data) ? ($data['livekit_client_token'] ?? $data['livekit_agent_token'] ?? null) : null) ??
	(is_array($livekit) ? ($livekit['room_token'] ?? $livekit['token'] ?? $livekit['livekit_token'] ?? null) : null) ??
	(is_array($data) && is_array($data['room'] ?? null) ? (($data['room']['token'] ?? $data['room']['room_token'] ?? null)) : null);

if (!$roomUrl || !$roomToken) {
	http_response_code(502);
	echo json_encode([
		'success' => false,
		'error' => 'LiveAvatar start response missing room_url/room_token',
		'details' => mb_substr((string)$resp, 0, 700),
		'debug_keys' => [
			'data_keys' => is_array($data) ? array_keys($data) : null,
			'livekit_keys' => is_array($livekit) ? array_keys($livekit) : null,
		],
	]);
	exit;
}

echo json_encode([
	'success' => true,
	'room_url' => $roomUrl,
	'room_token' => $roomToken,
]);


<?php
// web/heygen_session_create.php
// Back-compat wrapper: use LiveAvatar session token flow.
// Prefer calling /web/liveavatar_session_create.php directly.

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

// Re-map legacy env var names if present
$apiKey = getenv('LIVEAVATAR_API_KEY') ?: (getenv('HEYGEN_API_KEY') ?: '');
$avatarId = getenv('LIVEAVATAR_AVATAR_ID') ?: (getenv('HEYGEN_AVATAR_ID') ?: '');
if ($apiKey === '') {
	http_response_code(500);
	echo json_encode(['success' => false, 'error' => 'LIVEAVATAR_API_KEY not configured']);
	exit;
}
if ($avatarId === '') {
	http_response_code(500);
	echo json_encode(['success' => false, 'error' => 'LIVEAVATAR_AVATAR_ID not configured']);
	exit;
}

$mode = strtoupper(trim(getenv('LIVEAVATAR_MODE') ?: 'FULL'));
if ($mode !== 'FULL' && $mode !== 'CUSTOM') $mode = 'FULL';

$voiceId = getenv('LIVEAVATAR_VOICE_ID') ?: (getenv('HEYGEN_VOICE_ID') ?: '');
$contextId = getenv('LIVEAVATAR_CONTEXT_ID') ?: '';
$language = getenv('LIVEAVATAR_LANGUAGE') ?: 'en';

$persona = ['language' => $language];
if ($voiceId !== '') $persona['voice_id'] = $voiceId;
if ($contextId !== '') $persona['context_id'] = $contextId;

$payload = [
	'mode' => $mode,
	'avatar_id' => $avatarId,
	'avatar_persona' => $persona,
];

$base = rtrim(getenv('LIVEAVATAR_API_BASE') ?: 'https://api.liveavatar.com', '/');
$path = getenv('LIVEAVATAR_TOKEN_PATH') ?: '/v1/sessions/token';
$url = $base . $path;

$ch = curl_init($url);
curl_setopt_array($ch, [
	CURLOPT_POST => true,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_TIMEOUT => 20,
	CURLOPT_HTTPHEADER => [
		'X-API-KEY: ' . $apiKey,
		'Accept: application/json',
		'Content-Type: application/json',
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
		'error' => 'LiveAvatar session token request failed',
		'http_code' => $httpCode,
		'curl_errno' => $curlErrNo,
		'curl_error' => $curlErr,
		'details' => mb_substr((string)$resp, 0, 700),
	]);
	exit;
}

$decoded = json_decode($resp ?: '{}', true) ?: [];
$data = $decoded['data'] ?? $decoded;
$sessionToken = (is_array($data) ? ($data['session_token'] ?? null) : null) ?? ($decoded['session_token'] ?? null);
$sessionId = (is_array($data) ? ($data['session_id'] ?? null) : null) ?? ($decoded['session_id'] ?? null);

if (!$sessionToken) {
	http_response_code(502);
	echo json_encode(['success' => false, 'error' => 'LiveAvatar session_token missing in response', 'details' => mb_substr((string)$resp, 0, 700)]);
	exit;
}

echo json_encode(['success' => true, 'session_token' => $sessionToken, 'session_id' => $sessionId, 'token' => $sessionToken]);


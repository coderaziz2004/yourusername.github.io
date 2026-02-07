<?php
// web/demo_chat.php
// Minimal server-side chat proxy for the Try Now page (avoids CORS, keeps OPENAI_API_KEY server-side).

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
if (!is_array($body)) {
	$body = $_POST;
}

$text = trim((string)($body['text'] ?? ''));
$history = $body['history'] ?? null; // optional array of {role, content}
$expertName = trim((string)($body['expert_name'] ?? 'Andrej Karpathy'));
$expertBio = trim((string)($body['expert_bio'] ?? ''));
$expertTags = $body['expert_tags'] ?? null; // optional array

if ($text === '') {
	http_response_code(400);
	echo json_encode(['success' => false, 'error' => 'Missing text']);
	exit;
}

$openaiKey = getenv('OPENAI_API_KEY') ?: '';
if ($openaiKey === '') {
	http_response_code(500);
	echo json_encode(['success' => false, 'error' => 'OpenAI API key not configured']);
	exit;
}

if (!is_array($expertTags)) $expertTags = [];

$system = "You are {$expertName}. Use the persona below to answer in first person.\n\n" .
	"BIO:\n{$expertBio}\n\n" .
	"TAGS:\n" . json_encode($expertTags) . "\n\n" .
	"Rules:\n- Be concise, practical, and high-signal.\n- Prefer concrete advice, checklists, and tradeoffs.\n- If context is missing, ask 1-2 clarifying questions.\n";

$messages = [
	['role' => 'system', 'content' => $system],
];

if (is_array($history)) {
	$trimmed = array_slice($history, -16);
	foreach ($trimmed as $m) {
		$role = $m['role'] ?? '';
		$content = trim((string)($m['content'] ?? ''));
		if (!in_array($role, ['user', 'assistant'], true)) continue;
		if ($content === '') continue;
		$messages[] = ['role' => $role, 'content' => $content];
	}
}

$messages[] = ['role' => 'user', 'content' => $text];

$model = getenv('DEMO_LLM_MODEL') ?: 'gpt-4o-mini';
$maxTokens = (int)(getenv('LLM_MAX_TOKENS') ?: 600);

$payload = json_encode([
	'model' => $model,
	'messages' => $messages,
	'temperature' => 0.3,
	'max_tokens' => $maxTokens,
]);

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
	CURLOPT_POST => true,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_TIMEOUT => 25,
	CURLOPT_HTTPHEADER => [
		'Authorization: Bearer ' . $openaiKey,
		'Content-Type: application/json',
	],
	CURLOPT_POSTFIELDS => $payload,
]);

$resp = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
	http_response_code(502);
	echo json_encode([
		'success' => false,
		'error' => 'OpenAI API error',
		'http_code' => $httpCode,
		'details' => mb_substr((string)$resp, 0, 500),
	]);
	exit;
}

$decoded = json_decode($resp ?: '{}', true) ?: [];
$answer = trim((string)($decoded['choices'][0]['message']['content'] ?? ''));
if ($answer === '') $answer = '…';

echo json_encode(['success' => true, 'text' => $answer]);



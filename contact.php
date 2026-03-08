<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

// Honeypot: silently accept bot submissions.
if (!empty($_POST['_gotcha'])) {
    echo json_encode(['ok' => true]);
    exit;
}

function clean_field(string $key): string {
    $value = $_POST[$key] ?? '';
    return trim((string)$value);
}

$name = clean_field('name');
$company = clean_field('company');
$email = clean_field('email');
$phone = clean_field('phone');
$message = clean_field('message');
$consent = clean_field('consent');

if ($name === '' || $email === '' || $message === '' || $consent === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Missing required fields']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Invalid email address']);
    exit;
}

if (preg_match('/[\r\n]/', $name . $email)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Invalid characters']);
    exit;
}

$to = 'info@b-smart-services.com';
$subject = 'New contact request - B-Smart Services';
$body = "New contact request\n\n"
    . "Name: {$name}\n"
    . "Company: {$company}\n"
    . "Email: {$email}\n"
    . "Phone: {$phone}\n"
    . "Message:\n{$message}\n\n"
    . "Submitted: " . gmdate('Y-m-d H:i:s') . " UTC\n"
    . "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";

$headers = [
    'From: B-Smart Services <info@b-smart-services.com>',
    "Reply-To: {$email}",
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
];

$ok = mail($to, $subject, $body, implode("\r\n", $headers));

if (!$ok) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Mail send failed']);
    exit;
}

echo json_encode(['ok' => true]);

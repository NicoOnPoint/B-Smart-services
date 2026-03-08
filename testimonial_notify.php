<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!empty($_POST['_gotcha'])) {
    echo json_encode(['ok' => true]);
    exit;
}

function clean_field(string $key): string {
    $value = $_POST[$key] ?? '';
    return trim((string)$value);
}

$name = clean_field('name');
$role = clean_field('role');
$email = clean_field('email');
$message = clean_field('message');
$approveUrl = clean_field('approve_url');
$rejectUrl = clean_field('reject_url');

if ($name === '' || $message === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Missing required fields']);
    exit;
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Invalid email address']);
    exit;
}

if (preg_match('/[\r\n]/', $name . $email . $role)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Invalid characters']);
    exit;
}

$to = 'info@b-smart-services.com';
$subject = 'New testimonial pending approval - B-Smart Services';
$body = "A new testimonial was submitted and is pending approval.\n\n"
    . "Name: {$name}\n"
    . "Role/Company: {$role}\n"
    . "Email: " . ($email !== '' ? $email : '[not provided]') . "\n"
    . "Message:\n{$message}\n";

if ($approveUrl !== '' || $rejectUrl !== '') {
    $body .= "\nModeration links:\n";
    if ($approveUrl !== '') {
        $body .= "Approve: {$approveUrl}\n";
    }
    if ($rejectUrl !== '') {
        $body .= "Reject: {$rejectUrl}\n";
    }
    $body .= "\n";
} else {
    $body .= "\n";
}

$body .= "Submitted: " . gmdate('Y-m-d H:i:s') . " UTC\n"
    . "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";

$headers = [
    'From: B-Smart Services <info@b-smart-services.com>',
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
];

if ($email !== '') {
    $headers[] = "Reply-To: {$email}";
}

$ok = mail($to, $subject, $body, implode("\r\n", $headers));

if (!$ok) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Mail send failed']);
    exit;
}

echo json_encode(['ok' => true]);

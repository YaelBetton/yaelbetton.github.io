<?php
// Configuration
$recipient_email = "yaelbetton@gmail.com";
$subject_prefix = "[Portfolio] ";
$sender_email = $recipient_email;
$redirect_success = "index.html?mail=ok";
$redirect_error = "index.html?mail=error";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html");
    exit;
}

$name = trim((string) filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$email = trim((string) filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$subject = trim((string) filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$message = trim((string) filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$honeypot = trim((string) filter_input(INPUT_POST, 'website', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

if ($honeypot !== '') {
    header("Location: $redirect_success");
    exit;
}

if ($name === '' || $email === '' || $subject === '' || $message === '') {
    header("Location: $redirect_error");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: $redirect_error");
    exit;
}

if (strlen($message) > 3000) {
    header("Location: $redirect_error");
    exit;
}

$email_subject = $subject_prefix . $subject;
$email_body = "Nouveau message depuis le portfolio\n\n";
$email_body .= "Nom: $name\n";
$email_body .= "Email: $email\n";
$email_body .= "Sujet: $subject\n\n";
$email_body .= "Message:\n$message\n";

$headers = array(
    "From: Portfolio <$sender_email>",
    "Reply-To: $name <$email>",
    "Content-Type: text/plain; charset=UTF-8",
    "X-Mailer: PHP/" . phpversion()
);
$headers_string = implode("\r\n", $headers);

$additional_params = "-f $sender_email";
$sent = mail($recipient_email, $email_subject, $email_body, $headers_string, $additional_params);

if ($sent) {
    header("Location: $redirect_success");
    exit;
}

header("Location: $redirect_error");
exit;
?>
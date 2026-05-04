<?php
header('Content-Type: application/json; charset=utf-8');

$file = __DIR__ . "/registrations.txt";

$email = trim($_POST["email"] ?? "");
$firstname = trim($_POST["firstname"] ?? "");
$lastname = trim($_POST["lastname"] ?? "");
$congressName = trim($_POST["congress-name"] ?? "");
$message = trim($_POST["message"] ?? "");

if ($email === "" || $firstname === "" || $lastname === "" || $congressName === "" || $message === "") {
    echo json_encode(["status" => "error"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error"]);
    exit;
}

// Check maximum 100 words again on backend


if (strlen($message) > 300) {
    echo json_encode(["status" => "error"]);
    exit;
}

// Create file if it does not exist
if (!file_exists($file)) {
    file_put_contents($file, "");
}

// Lock file to avoid simultaneous write conflict
$handle = fopen($file, "c+");

if (!$handle) {
    echo json_encode(["status" => "error"]);
    exit;
}

flock($handle, LOCK_EX);

// Read existing file
$contents = stream_get_contents($handle);
$lines = explode(PHP_EOL, $contents);

foreach ($lines as $line) {
    $line = trim($line);

    if ($line === "") {
        continue;
    }

    // Each line starts with email, then comma, then message
    $parts = explode(",", $line, 2);
    $existingEmail = strtolower(trim($parts[0]));

    if ($existingEmail === strtolower($email)) {
        flock($handle, LOCK_UN);
        fclose($handle);

        echo json_encode(["status" => "duplicate"]);
        exit;
    }
}

// Clean message to one line
$message = preg_replace('/\s+/', ' ', $message);

// CSV-like format: email + comma + paragraph + CR/LF
$newLine = $email . "," . $firstname . "," . $lastname . "," . $congressName . "," . $message . "\r\n";

fseek($handle, 0, SEEK_END);
fwrite($handle, $newLine);

flock($handle, LOCK_UN);
fclose($handle);

echo json_encode(["status" => "success"]);
exit;
?>
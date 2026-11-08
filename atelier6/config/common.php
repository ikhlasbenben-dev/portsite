<?php
declare(strict_types=1);
// Load a local ignored .env file for development; Vercel supplies these values itself.
$localEnv = dirname(__DIR__, 2) . '/.env';
if (is_file($localEnv)) {
    foreach (file($localEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        if (getenv(trim($key)) === false) putenv(trim($key) . '=' . trim($value, " \t\n\r\0\x0B\"'"));
    }
}
require_once __DIR__ . '/db.php';

function jsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function cloudinaryUpload(string $filePath): string
{
    $cloud = getenv('CLOUDINARY_CLOUD_NAME') ?: '';
    $key = getenv('CLOUDINARY_API_KEY') ?: '';
    $secret = getenv('CLOUDINARY_API_SECRET') ?: '';
    if ($cloud === '' || $key === '' || $secret === '') {
        throw new RuntimeException('Configuration Cloudinary manquante.');
    }
    $timestamp = time();
    $folder = 'atelier6/stagiaires';
    $signature = sha1("folder=$folder&timestamp=$timestamp" . $secret);
    $curl = curl_init("https://api.cloudinary.com/v1_1/" . rawurlencode($cloud) . "/image/upload");
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => [
            'file' => new CURLFile($filePath),
            'api_key' => $key,
            'timestamp' => $timestamp,
            'folder' => $folder,
            'signature' => $signature,
        ],
        CURLOPT_TIMEOUT => 30,
    ]);
    $body = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    curl_close($curl);
    if ($body === false || $status < 200 || $status >= 300) {
        error_log('Cloudinary upload failed: ' . ($error ?: 'HTTP ' . $status));
        throw new RuntimeException('Échec de l’envoi de la photo.');
    }
    $response = json_decode($body, true);
    if (!is_array($response) || empty($response['secure_url'])) {
        throw new RuntimeException('Réponse Cloudinary invalide.');
    }
    return $response['secure_url'];
}

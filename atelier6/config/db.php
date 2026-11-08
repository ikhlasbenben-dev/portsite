<?php
declare(strict_types=1);

function getDatabase(): PDO
{
    $host = getenv('DB_HOST') ?: '';
    $port = getenv('DB_PORT') ?: '3306';
    $name = getenv('DB_NAME') ?: '';
    $user = getenv('DB_USER') ?: '';
    $password = getenv('DB_PASSWORD') ?: '';
    foreach (['DB_HOST' => $host, 'DB_NAME' => $name, 'DB_USER' => $user, 'DB_PASSWORD' => $password] as $key => $value) {
        if ($value === '') {
            throw new RuntimeException("Missing environment variable: $key");
        }
    }

    $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $projectRoot = dirname(__DIR__, 2);
    $caPath = getenv('DB_SSL_CA') ?: 'certs/ca.pem';
    // Resolve relative paths from the deployed project root, not the function's working directory.
    if ($caPath[0] !== '/') {
        $caPath = $projectRoot . '/' . ltrim($caPath, '/\\');
    }
    if (!is_file($caPath)) {
        throw new RuntimeException('Certificat CA Aiven introuvable. Ajoutez certs/ca.pem et configurez DB_SSL_CA.');
    }
    $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
    return new PDO($dsn, $user, $password, $options);
}

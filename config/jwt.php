<?php

/**
 * Base64Url encode helper.
 */
function base64url_encode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * Base64Url decode helper.
 */
function base64url_decode(string $data): string|false
{
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $padlen = 4 - $remainder;
        $data .= str_repeat('=', $padlen);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

/**
 * Mengambil JWT secret key dari environment atau default fallback.
 */
function jwt_secret(): string
{
    return $_ENV['JWT_SECRET'] ?? getenv('JWT_SECRET') ?: 'giat_secret_key_change_in_production';
}

/**
 * Generate (encode) token JWT dengan algoritma HS256.
 *
 * @param array $payload Data klaim payload.
 * @param string|null $secret Secret key opsional (default dari jwt_secret()).
 * @param int $ttl Waktu aktif token dalam detik (default: 3600 / 1 jam; 0 jika tidak ada auto exp).
 * @return string JWT token format string header.payload.signature.
 */
function jwt_encode(array $payload, ?string $secret = null, int $ttl = 3600): string
{
    $secret = $secret ?? jwt_secret();

    $header = [
        'alg' => 'HS256',
        'typ' => 'JWT',
    ];

    if (!isset($payload['iat'])) {
        $payload['iat'] = time();
    }

    if ($ttl > 0 && !isset($payload['exp'])) {
        $payload['exp'] = $payload['iat'] + $ttl;
    }

    $headerEncoded = base64url_encode((string) json_encode($header, JSON_UNESCAPED_SLASHES));
    $payloadEncoded = base64url_encode((string) json_encode($payload, JSON_UNESCAPED_SLASHES));

    $signature = hash_hmac('sha256', "{$headerEncoded}.{$payloadEncoded}", $secret, true);
    $signatureEncoded = base64url_encode($signature);

    return "{$headerEncoded}.{$payloadEncoded}.{$signatureEncoded}";
}

/**
 * Validasi dan decode token JWT HS256.
 *
 * @param string $token JWT token.
 * @param string|null $secret Secret key opsional.
 * @return array|null Array payload jika valid, null jika tanda tangan salah atau expired.
 */
function jwt_decode(string $token, ?string $secret = null): ?array
{
    $secret = $secret ?? jwt_secret();
    $parts = explode('.', $token);

    if (count($parts) !== 3) {
        return null;
    }

    list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;

    $headerJson = base64url_decode($headerEncoded);
    $payloadJson = base64url_decode($payloadEncoded);
    $signature = base64url_decode($signatureEncoded);

    if ($headerJson === false || $payloadJson === false || $signature === false) {
        return null;
    }

    $header = json_decode($headerJson, true);
    if (!is_array($header) || ($header['alg'] ?? '') !== 'HS256') {
        return null;
    }

    $validSignature = hash_hmac('sha256', "{$headerEncoded}.{$payloadEncoded}", $secret, true);
    if (!hash_equals($validSignature, $signature)) {
        return null;
    }

    $payload = json_decode($payloadJson, true);
    if (!is_array($payload)) {
        return null;
    }

    if (isset($payload['exp']) && is_numeric($payload['exp']) && time() >= $payload['exp']) {
        return null; // Token kedaluwarsa
    }

    return $payload;
}

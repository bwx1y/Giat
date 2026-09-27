<?php

class JwtHelper
{
    /**
     * Encode array data ke dalam format Base64URL safe.
     */
    private static function base64UrlEncode(string $data): string
    {
        return $data
                |> base64_encode(...)
                |> (fn($x) => strtr($x, '+/', '-_'))
                |> (fn($x) => rtrim($x, '='));
    }

    /**
     * Generate Token JWT
     *
     * @param array $payload Data user/claims yang ingin dimasukkan ke token
     * @param int $expiryInSeconds Durasi berlaku token (default 1 jam / 3600 detik)
     * @return array Token JWT
     */
    public static function generateToken(array $payload, int $expiryInSeconds = 3600): array
    {
        $secretKey = $_ENV['JWT_SECRET'] ?? 'default_secret_key_change_me';

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $issuedAt = time();
        $expirationTime = $issuedAt + $expiryInSeconds;

        $payload['iat'] = $issuedAt;
        $payload['exp'] = $expirationTime;

        $base64UrlHeader  = self::base64UrlEncode(json_encode($header));
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));

        $signature = hash_hmac(
            'sha256',
            $base64UrlHeader . "." . $base64UrlPayload,
            $secretKey,
            true
        );

        $base64UrlSignature = self::base64UrlEncode($signature);
        $jwtToken = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        return [
            'key'      => $jwtToken,
            'provider' => "Bearer",
            'exp'      => $expirationTime
        ];
    }

    /**
     * Validate & Decode Token JWT
     */
    public static function validateToken(string $jwt): array|false
    {
        $secretKey = $_ENV['JWT_SECRET'] ?? 'default_secret_key_change_me';
        $tokenParts = explode('.', $jwt);

        if (count($tokenParts) !== 3) {
            return false;
        }

        [$base64Header, $base64Payload, $base64Signature] = $tokenParts;

        $signature = hash_hmac(
            'sha256',
            $base64Header . "." . $base64Payload,
            $secretKey,
            true
        );

        $expectedSignature = self::base64UrlEncode($signature);

        if (!hash_equals($expectedSignature, $base64Signature)) {
            return false;
        }

        $payload = strtr($base64Payload, '-_', '+/')
                |> base64_decode(...)
                |> (fn($x) => json_decode($x, true));

        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }
}
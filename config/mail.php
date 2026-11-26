<?php

if (!function_exists('kirimEmailMailpit')) {
    function kirimEmailMailpit(
        string $to,
        string $subject,
        string $htmlBody,
        string $from = 'noreply@giat.test',
        string $fromName = 'GIAT Smart Multi-Event',
        ?string $host = null,
        ?int $port = null
    ): bool {
        $host = $host ?? (getenv('MAIL_HOST') ?: '127.0.0.1');
        $port = $port ?? (int) (getenv('MAIL_PORT') ?: 1025);

        $socket = @fsockopen($host, $port, $errno, $errstr, 10);
        if (!$socket) {
            throw new RuntimeException("Gagal terhubung ke Mailpit ($host:$port): $errstr ($errno)");
        }

        // Membaca response SMTP 
        $bacaResponse = function () use ($socket): string {
            $response = '';
            while ($line = fgets($socket, 515)) {
                $response .= $line;
                if (isset($line[3]) && $line[3] === ' ') {
                    break;
                }
            }
            return $response;
        };

        $kirimPerintah = function (string $cmd, int $expectedCode) use ($socket, $bacaResponse): void {
            fwrite($socket, $cmd . "\r\n");
            $res = $bacaResponse();
            if ((int) substr($res, 0, 3) !== $expectedCode) {
                throw new RuntimeException("SMTP Error pada '$cmd': $res");
            }
        };

        try {
            $greeting = $bacaResponse();
            if ((int) substr($greeting, 0, 3) !== 220) {
                throw new RuntimeException("Greeting Mailpit tidak valid: $greeting");
            }

            $kirimPerintah('EHLO localhost', 250);
            $kirimPerintah("MAIL FROM:<$from>", 250);
            $kirimPerintah("RCPT TO:<$to>", 250);
            $kirimPerintah('DATA', 354);

            $headers = [
                "From: $fromName <$from>",
                "To: <$to>",
                "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
                "Date: " . date('r'),
                "MIME-Version: 1.0",
                "Content-Type: text/html; charset=UTF-8",
            ];

            // Normalisasi newline ke CRLF & escape titik di awal baris 
            $body = str_replace(["\r\n", "\r"], "\n", $htmlBody);
            $body = str_replace("\n", "\r\n", $body);
            $body = preg_replace('/^\./m', '..', $body);

            $data = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";
            $kirimPerintah($data, 250);

            $kirimPerintah('QUIT', 221);
            return true;
        } finally {
            if (is_resource($socket)) {
                fclose($socket);
            }
        }
    }
}
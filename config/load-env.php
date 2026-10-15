<?php
/**
 * Fungsi natif untuk meload file .env
 * @throws Exception
 */
function loadEnv($filePath): void
{
    if (!file_exists($filePath)) {
        throw new Exception("File .env tidak ditemukan di: " . $filePath);
    }

    // Baca file baris per baris, abaikan baris kosong atau komentar (#)
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // Lewati jika baris adalah komentar
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        // Pisahkan key dan value berdasarkan tanda sama dengan (=)
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);

            $name = trim($name);
            $value = trim($value);

            // Hilangkan tanda kutip ganda atau tunggal jika ada di awal/akhir value
            if (preg_match('/^"(.*)"$/', $value, $matches) || preg_match("/^'(.*)'$/", $value, $matches)) {
                $value = $matches[1];
            }

            // Masukkan ke environment variable PHP jika belum ada
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

try {
    loadEnv(__DIR__ . '/../.env');
} catch (Exception $e) {
    die($e->getMessage());
}
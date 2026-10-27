<?php

// ponytail: single-file rotating log, upgrade to syslog/monolog if multiple sinks needed

function log_max_size_bytes(): int
{
    $val = trim((string)($_ENV['LOG_MAX_SIZE'] ?? '5M'));
    if (preg_match('/^(\d+)\s*(k|m|g)?b?$/i', $val, $m)) {
        $num = (int)$m[1];
        $unit = strtoupper($m[2] ?? '');
        return match ($unit) {
            'G' => $num * 1024 * 1024 * 1024,
            'M' => $num * 1024 * 1024,
            'K' => $num * 1024,
            default => $num,
        };
    }
    return 5 * 1024 * 1024;
}

function write_log(string $message, string $level = 'INFO', array $context = []): void
{
    $dir = dirname(__DIR__) . '/log';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $logFile = $dir . '/app.log';
    $maxBytes = log_max_size_bytes();

    clearstatcache(true, $logFile);
    if (file_exists($logFile) && filesize($logFile) >= $maxBytes) {
        $backup = $dir . '/' . time() . '.log';
        if (file_exists($backup)) {
            unlink($backup);
        }
        rename($logFile, $backup);
    }

    $time = date('Y-m-d H:i:s');
    $level = strtoupper($level);
    $extra = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES) : '';
    $entry = "[$time] [$level] {$message}{$extra}" . PHP_EOL;

    file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);

    if (php_sapi_name() === 'cli-server') {
        file_put_contents('php://stdout', $entry);
    }
}

function logger(string $message, string $level = 'INFO', array $context = []): void
{
    write_log($message, $level, $context);
}

set_error_handler(static function (int $errno, string $errstr, string $errfile, int $errline): bool {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    $level = match ($errno) {
        E_USER_ERROR, E_RECOVERABLE_ERROR => 'ERROR',
        E_WARNING, E_USER_WARNING => 'WARNING',
        E_NOTICE, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED => 'NOTICE',
        default => 'DEBUG',
    };
    write_log("$errstr in $errfile:$errline", $level);
    return false;
});

set_exception_handler(static function (Throwable $e): void {
    write_log(get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(), 'ERROR', [
        'trace' => $e->getTraceAsString(),
    ]);
    if (ini_get('display_errors')) {
        echo "\nFatal error: Uncaught " . $e . "\n";
    }
});

register_shutdown_function(static function (): void {
    $err = error_get_last();
    if ($err !== null && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        write_log("Fatal: {$err['message']} in {$err['file']}:{$err['line']}", 'CRITICAL');
    }
});


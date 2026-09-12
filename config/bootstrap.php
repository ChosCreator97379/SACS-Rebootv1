<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefix = 'Sacs\\';
    $baseDirectory = dirname(__DIR__) . '/src/';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDirectory . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

$environmentFile = dirname(__DIR__) . '/.env';
if (is_file($environmentFile)) {
    foreach (file($environmentFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        $_ENV[$name] = trim($value, "\\\"'");
    }
}

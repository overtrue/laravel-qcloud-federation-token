<?php

require __DIR__.'/../vendor/autoload.php';

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if (! (error_reporting() & $severity)) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

try {
    $sourcePath = realpath(__DIR__.'/../src');

    if ($sourcePath === false) {
        throw new RuntimeException('Unable to resolve the source directory.');
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourcePath, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($files as $file) {
        if (! $file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $className = 'Overtrue\\LaravelQcloudFederationToken\\'.str_replace(
            [DIRECTORY_SEPARATOR, '.php'],
            ['\\', ''],
            substr($file->getPathname(), strlen($sourcePath) + 1)
        );

        if (! class_exists($className) && ! interface_exists($className) && ! trait_exists($className)) {
            throw new RuntimeException(sprintf('Unable to autoload %s.', $className));
        }
    }
} finally {
    restore_error_handler();
}

echo "Runtime compatibility check passed.\n";

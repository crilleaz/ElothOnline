<?php
declare(strict_types=1);

return (static function (): array {
    $envGetter = static function (string $envName): ?string {
        $value = getenv($envName);
        if (!is_string($value)) {
            $value = null;
        }

        return $value;
    };

    return [
        'dbHost' => $envGetter('DB_HOST') ?? '127.0.0.1',
        'dbName' => $envGetter('DB_NAME') ?? 'db',
        'dbUser' => $envGetter('DB_USER') ?? 'user',
        'dbPass' => $envGetter('DB_PASS') ?? 'password',
    ];
})();

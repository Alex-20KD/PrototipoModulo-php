<?php

namespace App\Support;

use RuntimeException;

class SecurityConfig
{
    /**
     * Obtiene los proxies confiables parseados.
     */
    public static function getTrustedProxies(): array
    {
        return self::parseAndValidate(env('TRUSTED_PROXIES', ''), 'TRUSTED_PROXIES');
    }

    /**
     * Obtiene los orígenes CORS permitidos parseados.
     */
    public static function getCorsOrigins(): array
    {
        return self::parseAndValidate(env('CORS_ALLOWED_ORIGINS', ''), 'CORS_ALLOWED_ORIGINS');
    }

    /**
     * Parsea una cadena separada por comas y aborta si se detectan comodines.
     */
    public static function parseAndValidate(string $value, string $name): array
    {
        if (trim($value) === '') {
            return [];
        }

        $list = array_filter(array_map('trim', explode(',', $value)));

        foreach ($list as $item) {
            if ($item === '*' || $item === '**') {
                throw new RuntimeException("{$name} no admite comodines");
            }
        }

        return array_values($list);
    }
}

<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Cors extends BaseConfig
{
    public array $default = [
        'allowedOrigins'         => ['http://localhost:3000'],
        'allowedOriginsPatterns' => [],
        'allowedHeaders'         => ['Authorization', 'Content-Type'],
        'allowedMethods'         => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'supportsCredentials'    => true,
    ];
}

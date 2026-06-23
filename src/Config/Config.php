<?php

namespace App\Config;

class Config
{
    private static ?Config $instance = null;
    private array $config = [];

    private function __construct()
    {
        $this->loadConfig();
    }

    public static function getInstance(): Config
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfig(): void
    {
        // Cargar configuración base
        $baseConfig = require dirname(__DIR__, 2) . '/app/config/config.php';
        
        // Determinar entorno
        $env = getenv('APP_ENV') ?: 'development';
        $envFile = dirname(__DIR__, 2) . "/config/environments/{$env}.php";
        
        if (file_exists($envFile)) {
            $envConfig = require $envFile;
            $this->config = array_merge($this->config, $envConfig);
        }
    }

    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }

        return $value;
    }

    public function set(string $key, $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        while (count($keys) > 1) {
            $k = array_shift($keys);
            if (!isset($config[$k]) || !is_array($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config[array_shift($keys)] = $value;
    }

    public function all(): array
    {
        return $this->config;
    }
}

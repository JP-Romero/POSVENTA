<?php
/**
 * Get a configuration value using the modern Config class
 * 
 * @param string $key Configuration key
 * @param mixed $default Default value if key doesn't exist
 * @return mixed
 */
function getConfig(string $key, $default = null)
{
    try {
        $config = \App\Config\Config::getInstance();
        return $config->get($key, $default);
    } catch (\Exception $e) {
        return $default;
    }
}
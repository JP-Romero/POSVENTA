<?php
<<<<<<< HEAD

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
=======
/**
 * Helper de Configuración
 * Funciones para obtener valores de la tabla configuracion
 */

// Cache estático para evitar múltiples consultas
static $configCache = null;

/**
 * Obtiene un valor específico de la configuración o todos los valores
 * 
 * @param string $key Clave de configuración (nombre de la columna)
 * @param mixed $default Valor por defecto si no existe
 * @return mixed Valor de la configuración o default
 */
function getConfig($key = null, $default = null) {
    global $configCache;
    
    // Si no hay cache, cargar configuración desde BD
    if ($configCache === null) {
        try {
            $db = new Database();
            $db->query('SELECT * FROM configuracion LIMIT 1');
            $result = $db->single();
            
            if ($result) {
                // Convertir objeto/array a array asociativo
                $configCache = (array) $result;
            } else {
                $configCache = [];
            }
        } catch (Exception $e) {
            // Si falla la BD, retornar array vacío
            $configCache = [];
        }
    }
    
    // Si no se pasa key, retornar todo el array
    if ($key === null) {
        return $configCache;
    }
    
    // Retornar valor específico o default
    if (isset($configCache[$key])) {
        return $configCache[$key];
    }
    
    return $default;
}

/**
 * Limpia el cache de configuración (útil después de actualizar configuración)
 */
function clearConfigCache() {
    global $configCache;
    $configCache = null;
}
>>>>>>> a6741f722e4afd6f45cdb32385cdef28de967fb2

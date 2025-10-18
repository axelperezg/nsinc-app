<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuration extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    /**
     * Cache time in seconds (1 hour)
     */
    const CACHE_TIME = 3600;

    /**
     * Obtener el valor de una configuración por su clave
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = "config_{$key}";

        return Cache::remember($cacheKey, self::CACHE_TIME, function () use ($key, $default) {
            $config = self::where('key', $key)->first();

            if (!$config) {
                return $default;
            }

            return self::castValue($config->value, $config->type);
        });
    }

    /**
     * Establecer el valor de una configuración
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function set(string $key, $value): bool
    {
        $config = self::where('key', $key)->first();

        if (!$config) {
            return false;
        }

        $config->value = $value;
        $saved = $config->save();

        // Limpiar cache
        Cache::forget("config_{$key}");

        return $saved;
    }

    /**
     * Convertir el valor según su tipo
     *
     * @param string|null $value
     * @param string $type
     * @return mixed
     */
    protected static function castValue($value, string $type)
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_decode($value, true),
            'array' => json_decode($value, true) ?? [],
            default => $value,
        };
    }

    /**
     * Obtener el valor convertido según el tipo
     *
     * @return mixed
     */
    public function getTypedValue()
    {
        return self::castValue($this->value, $this->type);
    }

    /**
     * Limpiar toda la cache de configuraciones
     */
    public static function clearCache(): void
    {
        $configs = self::all();

        foreach ($configs as $config) {
            Cache::forget("config_{$config->key}");
        }
    }

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Limpiar cache al guardar
        static::saved(function ($config) {
            Cache::forget("config_{$config->key}");
        });

        // Limpiar cache al eliminar
        static::deleted(function ($config) {
            Cache::forget("config_{$config->key}");
        });
    }
}

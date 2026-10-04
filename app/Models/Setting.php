<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        $val = $setting->value;
        if ($val === 'true') return true;
        if ($val === 'false') return false;

        $decoded = json_decode($val, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $val;
    }

    public static function set(string $key, mixed $value): void
    {
        if (is_bool($value)) {
            $storeValue = $value ? 'true' : 'false';
        } elseif (is_array($value) || is_object($value)) {
            $storeValue = json_encode($value);
        } else {
            $storeValue = (string) $value;
        }

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $storeValue]
        );
    }
}

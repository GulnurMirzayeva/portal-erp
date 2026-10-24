<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    /**
     * Tənzimləmə dəyərini açar adına görə əldə edir.
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Tənzimləmə dəyərini yeniləyir və ya yaradır.
     */
    public static function set(string $key, $value, ?string $description = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'description' => $description ?? $key,
            ]
        );
    }

    /**
     * PIN tələb olunub-olunmadığını qaytarır.
     */
    public static function isPinRequired(): bool
    {
        return (bool) static::get('require_pin', '0');
    }

    /**
     * Bir cihazdan yalnız 1 işçiyə icazə verilib-verilmədiyini yoxlayır.
     */
    public static function isSingleDeviceEnforced(): bool
    {
        return static::get('allow_device_reuse', '0') === '0';
    }
}

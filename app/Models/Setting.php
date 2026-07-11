<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function setValue(string $key, string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function isMaintenanceMode(): bool
    {
        return (bool) static::getValue('maintenance_mode', '0');
    }

    public static function maintenanceMessage(): string
    {
        return static::getValue('maintenance_message', 'Mfumo upo katika matengenezo. Tutarudi hivi punde.');
    }
}

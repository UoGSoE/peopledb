<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteOption extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    public static function findByKey(string $key, $default = null): ?string
    {
        $option = self::where('key', $key)->first();

        return $option ? $option->value : $default;
    }
}

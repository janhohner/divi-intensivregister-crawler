<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Model for data_requests table
 *
 * @property      int    $id
 * @property      string $key
 * @property      int    $value
 * @property      Carbon $created_at
 * @property      Carbon $updated_at
 *
 * @mixin \Eloquent
 */
class DataRequest extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    protected $hidden = [];

    protected $casts = [];

    public static function incrementKey(string $key)
    {
        DB::table('data_requests')->where('key', '=', $key)->increment('value', 1, [
            'updated_at' => Carbon::now(),
        ]);
    }
}

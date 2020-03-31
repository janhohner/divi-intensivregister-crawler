<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Model for clinics table
 *
 * @property      int                       $id
 * @property      string                    $clinic_identifier
 * @property      string                    $address
 * @property      string                    $state
 * @property      Carbon                    $last_submit_at
 * @property      Carbon                    $created_at
 * @property      Carbon                    $updated_at
 *
 * @property-read ClinicStatus[]|Collection $statuses
 *
 * @mixin \Eloquent
 */
class Clinic extends Model
{
    protected $fillable = [
        'clinic_identifier',
        'address',
        'state',
        'last_submit_at',
    ];

    protected $hidden = [];

    protected $casts = [];

    protected $dates = [
        'last_submit_at',
    ];

    /**
     * @return HasMany
     */
    public function statuses()
    {
        return $this->hasMany(ClinicStatus::class, 'clinic_id', 'id');
    }

    /**
     * @param string $identifier
     * @return Model|static|null
     */
    public static function getByClinicIdentifier(string $identifier) {
        return static::where('clinic_identifier', '=', $identifier)
            ->first();
    }
}

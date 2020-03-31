<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Model for map_clinics table
 *
 * @property      int                          $id
 * @property      string                       $clinic_identifier
 * @property      string                       $name
 * @property      string                       $state
 * @property      string                       $lat
 * @property      string                       $lon
 * @property      Carbon                       $last_submit_at
 * @property      Carbon                       $created_at
 * @property      Carbon                       $updated_at
 *
 * @property-read MapClinicStatus[]|Collection $statuses
 *
 * @mixin \Eloquent
 */
class MapClinic extends Model
{
    protected $fillable = [
        'clinic_identifier',
        'name',
        'state',
        'lat',
        'lon',
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
        return $this->hasMany(MapClinicStatus::class, 'map_clinic_id', 'id');
    }

    /**
     * @param \Carbon\Carbon $importDate
     * @return bool
     */
    public static function checkIfNewerThanExistingData(\Carbon\Carbon $importDate): bool
    {
        /** @var MapClinic $youngestEntry */
        $youngestEntry = static::orderBy('last_submit_at', 'desc')->first();
        if (! $youngestEntry) {
            return true;
        }

        return $youngestEntry->last_submit_at->lessThan($importDate);
    }
}

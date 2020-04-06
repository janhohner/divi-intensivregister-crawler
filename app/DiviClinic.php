<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Model for divi_clinics table
 *
 * @property      int                         $id
 * @property      string                      $divi_id
 * @property      string                      $ik_number
 * @property      string                      $description
 * @property      string                      $street
 * @property      string                      $street_number
 * @property      string                      $postcode
 * @property      string                      $city
 * @property      string                      $state
 * @property      string                      $latitude
 * @property      string                      $longitude
 * @property      Carbon                      $last_submit_at
 * @property      Carbon                      $created_at
 * @property      Carbon                      $updated_at
 *
 * @property-read DiviClinicData[]|Collection $data
 * @property-read DiviClinicWard[]|Collection $wards
 *
 * @mixin \Eloquent
 */
class DiviClinic extends Model
{
    protected $fillable = [
        'divi_id',
        'ik_number',
        'description',
        'street',
        'street_number',
        'postcode',
        'city',
        'state',
        'latitude',
        'longitude',
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
    public function data()
    {
        return $this->hasMany(DiviClinicData::class, 'divi_clinics_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function wards()
    {
        return $this->hasMany(DiviClinicWard::class, 'divi_clinics_id', 'id');
    }

    /**
     * @param string $diviId
     * @return DiviClinic|Model|null
     */
    public static function findByDiviId(string $diviId)
    {
        return static::firstWhere('divi_id', '=', $diviId);
    }

    public function mapForOutput(): array
    {
        return [
            'id' => $this->id,
            'divi_id' => $this->divi_id,
            'ik_number' => $this->ik_number,
            'description' => $this->description,
            'street' => $this->street,
            'street_number' => $this->street_number,
            'postcode' => $this->postcode,
            'city' => $this->city,
            'state' => $this->state,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'last_submit_at' => $this->last_submit_at->toISOString(),
            'data' => $this->data->map(function (DiviClinicData $data) {
                return $data->mapForOutput();
            })->all(),
            'wards' => $this->wards->map(function (DiviClinicWard $ward) {
                return $ward->mapForOutput();
            })->all(),
        ];
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Model for divi_clinic_wards table
 *
 * @property      int                             $id
 * @property      int                             $divi_clinics_id
 * @property      string                          $divi_id
 * @property      string                          $description
 * @property      string                          $organisation_tag
 * @property      bool                            $ards_network_member
 * @property      Carbon                          $last_submit_at
 * @property      Carbon                          $created_at
 * @property      Carbon                          $updated_at
 *
 * @property-read DiviClinic                      $clinic
 * @property-read DiviClinicWardData[]|Collection $data
 *
 * @mixin \Eloquent
 */
class DiviClinicWard extends Model
{
    protected $fillable = [
        'divi_clinics_id',
        'divi_id',
        'description',
        'organisation_tag',
        'last_submit_at',
    ];

    protected $hidden = [];

    protected $casts = [];

    protected $dates = [
        'last_submit_at',
    ];

    /**
     * @return BelongsTo
     */
    public function clinic()
    {
        return $this->belongsTo(DiviClinic::class, 'divi_clinics_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function data()
    {
        return $this->hasMany(DiviClinicWardData::class, 'divi_clinic_wards_id', 'id');
    }

    /**
     * @param string $diviId
     * @return DiviClinicWard|Model|null
     */
    public static function findByDiviId(string $diviId)
    {
        return static::firstWhere('divi_id', '=', $diviId);
    }
}

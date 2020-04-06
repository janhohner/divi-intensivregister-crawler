<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Model for divi_clinic_wards_data table
 *
 * @property      int            $id
 * @property      int            $divi_clinic_wards_id
 * @property      string         $ecmo_cases_year
 * @property      string         $beds_planned_capacity
 * @property      Carbon         $submitted_at
 * @property      Carbon         $created_at
 * @property      Carbon         $updated_at
 *
 * @property-read DiviClinicWard $ward
 *
 * @mixin \Eloquent
 */
class DiviClinicWardData extends Model
{
    protected $fillable = [
        'divi_clinic_wards_id',
        'ecmo_cases_year',
        'beds_planned_capacity',
        'ards_network_member',
        'submitted_at',
    ];

    protected $hidden = [];

    protected $casts = [];

    protected $dates = [
        'submitted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function ward()
    {
        return $this->belongsTo(DiviClinicWard::class, 'divi_clinic_wards_id', 'id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Model for divi_clinic_data table
 *
 * @property      int        $id
 * @property      int        $divi_clinics_id
 * @property      string     $low_care
 * @property      string     $high_care
 * @property      string     $ecmo
 * @property      string     $covid19_cases
 * @property      Carbon     $submitted_at
 * @property      Carbon     $created_at
 * @property      Carbon     $updated_at
 *
 * @property-read DiviClinic $clinic
 *
 * @mixin \Eloquent
 */
class DiviClinicData extends Model
{
    protected $fillable = [
        'divi_clinics_id',
        'low_care',
        'high_care',
        'ecmo',
        'covid19_cases',
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
    public function clinic()
    {
        return $this->belongsTo(DiviClinic::class, 'divi_clinics_id', 'id');
    }
}

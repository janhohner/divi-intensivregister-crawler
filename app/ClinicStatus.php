<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Model for clinic_statuses table
 *
 * @property      int    $id
 * @property      int    $clinic_id
 * @property      string $icu_low_care
 * @property      string $icu_high_care
 * @property      string $ecmo
 * @property      Carbon $submitted_at
 * @property      Carbon $created_at
 * @property      Carbon $updated_at
 *
 * @property-read Clinic $statuses
 */
class ClinicStatus extends Model
{
    protected $fillable = [
        'clinic_id',
        'icu_low_care',
        'icu_high_care',
        'ecmo',
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
        return $this->belongsTo(Clinic::class, 'clinic_id', 'id');
    }
}

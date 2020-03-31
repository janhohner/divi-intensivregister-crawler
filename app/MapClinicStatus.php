<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Model for clinic_statuses table
 *
 * @property      int       $id
 * @property      int       $map_clinic_id
 * @property      string    $covid19_cases
 * @property      Carbon    $submitted_at
 * @property      Carbon    $created_at
 * @property      Carbon    $updated_at
 *
 * @property-read MapClinic $clinic
 */
class MapClinicStatus extends Model
{
    protected $fillable = [
        'map_clinic_id',
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
        return $this->belongsTo(MapClinic::class, 'map_clinic_id', 'id');
    }
}

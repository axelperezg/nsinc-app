<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'fechaInicio',
        'fechaFinal',
        'campaign_id',
    ];

    protected $casts = [
        'fechaInicio' => 'date',
        'fechaFinal' => 'date',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}

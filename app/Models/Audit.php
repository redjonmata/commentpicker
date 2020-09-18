<?php

namespace App\Models;

use OwenIt\Auditing\Models\Audit as OwenItAudit;

class Audit extends OwenItAudit
{

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y H:i:s'
    ];

    /**
     * Accessors
     */
    public function getCreatedAtAttribute()
    {
        return date('d/m/Y H:i:s', strtotime($this->attributes['created_at']));
    }

    /**
     * Audit relations
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
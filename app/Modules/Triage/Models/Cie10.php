<?php

namespace App\Modules\Triage\Models;

use Illuminate\Database\Eloquent\Model;

class Cie10 extends Model
{
    protected $table = 'triage_cie10';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'description',
    ];
}

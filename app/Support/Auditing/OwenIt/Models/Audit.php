<?php

namespace OwenIt\Auditing\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $table = 'audits';

    protected $guarded = [];
}

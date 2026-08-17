<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'country', 'phone', 'whatsapp', 'work_type', 'experience', 'status'])]
class VolunteerApplication extends Model
{
    //
}

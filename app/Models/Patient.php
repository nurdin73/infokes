<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'nik', 'address', 'code', 'gender', 'birthday'];

    public function medicals()
    {
        return $this->hasMany(MedicalRegistration::class, 'patient_id', 'id');
    }

    public function medical()
    {
        return $this->hasOne(MedicalRegistration::class, 'patient_id', 'id')->latestOfMany();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRegistration extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id', 'no_registration', 'service', 'status', 'note'];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    public function payment()
    {
        return $this->belongsTo(PaymentMedicalRegistration::class, 'id', 'medical_registration_id');
    }
}

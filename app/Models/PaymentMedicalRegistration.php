<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMedicalRegistration extends Model
{
    use HasFactory;

    protected $fillable = ['no', 'medical_registration_id', 'payment_type', 'price', 'card_no', 'paid_by'];

    public function items()
    {
        return $this->hasMany(ItemService::class, 'payment_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tracking_number',
        'document_type',
        'purpose',
        'attachment', 
        'completed_document',   
        'remarks',      
        'status',
        'admin_remarks',
        'fee',
        'certificate_content',
        'pickup_date',
        'issued_at',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'issued_at' => 'datetime',
        'fee' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
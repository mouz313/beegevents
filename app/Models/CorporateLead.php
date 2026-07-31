<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['company_name', 'contact_person', 'email', 'phone', 'requirement_notes', 'status'])]
class CorporateLead extends Model
{
    use HasFactory;
}

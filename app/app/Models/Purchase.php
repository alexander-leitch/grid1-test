<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Cards;

class Purchase extends Model
{
    use HasFactory;
    public function cards()
    {
      return $this->hasOne(Cards::class, 'purchase_id');
    }
}

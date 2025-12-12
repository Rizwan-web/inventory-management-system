<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSequence extends Model
{
    use HasFactory;

    protected $table = 'invoice_sequences';
    protected $guarded = [];

    /**
     * Get the next invoice number for the current year
     */
    public static function getNextNumber()
    {
        $year = date('Y');
        
        $sequence = self::firstOrCreate(
            ['year' => $year],
            ['last_number' => 0]
        );
        
        $sequence->last_number += 1;
        $sequence->save();
        
        return $sequence->last_number;
    }
}

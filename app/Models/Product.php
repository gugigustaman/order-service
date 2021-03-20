<?php

namespace App\Models;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Stock deduction
     * @param  integer $qty the deduction number
     * @return mixed      void or exception (insufficient stock)
     */
    public function deductStock($qty) {
    	if ($this->stock < $qty) {
    		throw new CustomException(1504, 400);
    	}

    	$this->stock -= $qty;
    	$this->save();
    }
}
<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quiz extends Model
{
    use HasFactory;


    public function question(){
        return $this->hasMany(Question::class);
    }

    public static function boot() {
        parent::boot();
        static::deleting(function($user) {
             $user->question()->delete();
        });
    }
}

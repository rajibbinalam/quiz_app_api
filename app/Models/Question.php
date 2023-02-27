<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;


    public static function boot() {
        parent::boot();
        static::deleting(function($user) {
             $user->question_option()->delete();
        });
    }



    public function quiz(){
        return $this->belongsTo(Quiz::class);
    }
    public function question_option(){
        return $this->hasMany(QuestionOption::class);
    }
}

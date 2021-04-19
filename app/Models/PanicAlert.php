<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanicAlert extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function user ()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function return_for_api()
    {
        return (object)[
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'panic_type' => $this->panic_type,
            'details' => $this->details,
            'created_at' => $this->created_at,
            'created_by' => $this->user->return_for_api()
        ];
    }
}

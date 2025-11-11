<?php

namespace Alhawari\Rateify\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = ['user_id', 'rateable_id', 'rateable_type', 'value'];

    public function rateable()
    {
        return $this->morphTo();
    }
}

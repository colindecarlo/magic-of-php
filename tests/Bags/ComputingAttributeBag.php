<?php

namespace Magic\Tests\Bags;

use Carbon\Carbon;
use Magic\AttributeBag;

class ComputingAttributeBag extends AttributeBag
{
    public function getAgeAttribute()
    {
        return Carbon::now()->diff($this->birthday)->y;
    }
}

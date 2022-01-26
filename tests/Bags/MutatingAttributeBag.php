<?php

namespace Magic\Tests\Bags;

use Carbon\Carbon;
use Magic\AttributeBag;

class MutatingAttributeBag extends AttributeBag
{
    public function getNameAttribute($name)
    {
        return strtoupper($name);
    }

    public function setBirthdayAttribute($birthday)
    {
        return Carbon::parse($birthday);
    }
}

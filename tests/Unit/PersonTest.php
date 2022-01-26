<?php

namespace Magic\Tests\Unit;

use Carbon\Carbon;
use Magic\AttributeBag;
use Magic\Person;
use Magic\Tests\Bags\ComputingAttributeBag;
use Magic\Tests\Bags\MutatingAttributeBag;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    /** @test */
    public function it_makes_a_clone_of_itself_with_a_different_id()
    {
        $person = new Person('Colin DeCarlo', '1981-03-31');
        $prestige = clone $person;

        $this->assertEquals($person->name, $prestige->name);
        $this->assertEquals($person->dateOfBirth, $prestige->dateOfBirth);

        $this->assertNotEquals($person->id, $prestige->id);
    }
}

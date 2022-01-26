<?php

namespace Magic\Tests\Unit;

use Carbon\Carbon;
use Magic\AttributeBag;
use Magic\Tests\Bags\ComputingAttributeBag;
use Magic\Tests\Bags\MutatingAttributeBag;
use PHPUnit\Framework\TestCase;

class AttributeBagTest extends TestCase
{
    private array $attributes;
    private AttributeBag $bag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->attributes = [
            'name' => 'Colin',
            'birthday' => Carbon::parse('1981-03-31')->toDate()
        ];

        $this->bag = new AttributeBag($this->attributes);
    }

    /** @test */
    public function it_sets_attributes_when_constructed()
    {
        $this->assertEquals($this->attributes['name'], $this->bag->name);
        $this->assertEquals($this->attributes['birthday'], $this->bag->birthday);
    }

    /** @test */
    public function it_returns_null_when_accessing_an_attribute_that_doesnt_exist()
    {
        $this->assertNull($this->bag->not_there);
    }

    /** @test */
    public function it_can_mutate_the_attribute()
    {
        $mutatingBag = new MutatingAttributeBag($this->attributes);

        $this->assertEquals(strtoupper($this->attributes['name']), $mutatingBag->name);
    }

    /** @test */
    public function it_can_compute_attributes()
    {
        $computingBag = new ComputingAttributeBag($this->attributes);

        $this->assertEquals(40, $computingBag->age);
    }

    /** @test */
    public function it_can_update_the_values_of_attributes()
    {
        $fullName = 'Colin DeCarlo';
        $this->bag->name = $fullName;

        $this->assertEquals($fullName, $this->bag->name);

        // this is an important assertion
        $this->assertFalse(property_exists($this->bag, 'name'));
    }

    /** @test */
    public function it_can_set_new_attributes_at_runtime()
    {
        $this->assertNull($this->bag->weight);

        $weight = 165;
        $this->bag->weight = $weight;

        $this->assertEquals($weight, $this->bag->weight);

        // this is an important assertion
        $this->assertFalse(property_exists($this->bag, 'weight'));
    }

    /*
     * OR
     */

    /** @test */
    public function it_doesnt_allow_new_attributes_to_be_set_at_runtime()
    {
        $this->assertNull($this->bag->weight);

        $weight = 165;
        $this->bag->weight = $weight;

        $this->assertNull($this->bag->weight);
    }

    /** @test */
    public function it_can_modify_the_value_being_set()
    {
        $mutatingBag = new MutatingAttributeBag($this->attributes);

        $sometimeInFebruary = '1981-02-28';
        $mutatingBag->birthday = $sometimeInFebruary;

        $this->assertEquals($sometimeInFebruary, $mutatingBag->birthday->format('Y-m-d'));
    }

    /** @test */
    public function it_confirms_when_attributes_exist()
    {
        $this->assertTrue(isset($this->bag->name));
    }

    /** @test */
    public function it_confirms_when_attributes_dont_exist()
    {
        $this->assertFalse(isset($this->bag->nope));
    }

    /** @test */
    public function it_confirms_when_computed_attributes_exist()
    {
        $computingBag = new ComputingAttributeBag($this->attributes);

        $this->assertTrue(isset($computingBag->age));
    }

    /** @test */
    public function it_allows_attributes_to_be_removed()
    {
        $this->assertTrue(isset($this->bag->name));

        unset($this->bag->name);

        $this->assertFalse(isset($this->bag->name));
    }

    /** @test */
    public function it_can_register_methods_at_runtime()
    {
        $this->bag->record('shout', function ($message) {
            return strtoupper($message);
        });

        $this->assertEquals('HELLO WORLD!', $this->bag->shout('Hello World!'));
    }

    /** @test */
    public function it_doesnt_freak_out_if_a_method_that_doesnt_exist_is_called()
    {
        $this->assertNull($this->bag->whatever());
    }

    /** @test */
    public function it_can_reference_other_class_properties_in_recorded_methods()
    {
        $this->bag->record('greeting', function ($name) {
            return sprintf("Hello %s, I'm %s. It's nice to meet you.", $name, $this->name);
        });

        $this->assertEquals("Hello Sally, I'm Colin. It's nice to meet you.", $this->bag->greeting('Sally'));
    }

    /** @test */
    public function it_can_record_functions_to_called_in_a_static_context()
    {
        Carbon::setTestNow('1970-01-01');

        AttributeBag::registerFunction('dayOfTheWeek', function () {
            return Carbon::now()->format('l');
        });

        $this->assertEquals('Thursday', AttributeBag::dayOfTheWeek());
    }

    /** @test */
    public function it_doesnt_freak_out_if_a_static_function_is_called_that_doesnt_exist()
    {
        $this->assertNull(AttributeBag::nope());
    }

    /** @test */
    public function it_can_serialize_itself()
    {
        $serialized = 'O:18:"Magic\AttributeBag":2:{s:4:"name";s:5:"Colin";s:8:"birthday";O:8:"DateTime":3:{s:4:"date";s:26:"1981-03-31 00:00:00.000000";s:13:"timezone_type";i:3;s:8:"timezone";s:3:"UTC";}}';
        $this->assertEquals($serialized, serialize($this->bag));
    }

    /** @test */
    public function it_can_unserialize_itself()
    {
        $serialized = 'O:18:"Magic\AttributeBag":2:{s:4:"name";s:5:"Colin";s:8:"birthday";O:8:"DateTime":3:{s:4:"date";s:26:"1981-03-31 00:00:00.000000";s:13:"timezone_type";i:3;s:8:"timezone";s:3:"UTC";}}';
        $unserialized = unserialize($serialized);

        $this->assertEquals($this->attributes['name'], $unserialized->name);
        $this->assertEquals($this->attributes['birthday'], $unserialized->birthday);

        $this->assertFalse(property_exists($unserialized, 'name'));
        $this->assertFalse(property_exists($unserialized, 'birthday'));
    }

    /** @test */
    public function it_reminds_colin_to_talk_about_debug_info()
    {
        $talkedAboutDebugInfo = false;

        $this->assertTrue($talkedAboutDebugInfo);
    }
}

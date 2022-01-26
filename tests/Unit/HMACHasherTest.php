<?php

namespace Magic\Tests\Unit;

use Carbon\Carbon;
use Magic\AttributeBag;
use Magic\HMACHasher;
use Magic\Tests\Bags\ComputingAttributeBag;
use Magic\Tests\Bags\MutatingAttributeBag;
use PHPUnit\Framework\TestCase;

class HMACHasherTest extends TestCase
{
    /** @test */
    public function it_can_hash_string()
    {
        $hash = new HMACHasher('this is some shared private key value', 'sha256');

        $expectedHash = 'a71df18fe95897ae6d7eb7e5028fc2fb4c6451d789911b5697b484f48e771161';

        $this->assertEquals($expectedHash, $hash('hello world'));
    }

    /** @test */
    public function it_can_have_a_default_algorithm()
    {
        HMACHasher::setDefaultAlgorithm('sha256');

        $hash = new HMACHasher('this is some shared private key value');

        $expectedHash = 'a71df18fe95897ae6d7eb7e5028fc2fb4c6451d789911b5697b484f48e771161';

        $this->assertEquals($expectedHash, $hash('hello world'));
    }

    /** @test */
    public function it_complains_if_the_default_algorithm_is_unavailable()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unknown algorithm');

        HMACHasher::setDefaultAlgorithm('nope');
    }

    /** @test */
    public function it_complains_if_the_algorithm_is_unavailable()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unknown algorithm');

        new HMACHasher('blargh', 'nope');
    }
}

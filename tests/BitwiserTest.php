<?php
require "../src/Flow/Bitwiser/AbstractBitwiser.php";

use Flow\Bitwiser\AbstractBitwiser;

class BitwiserTest extends PHPUnit_Framework_TestCase
{

    public function testBitwiser()
    {
        $fooBitwiser = new FooBitwiser();

        $fooBitwiser->add(FooBitwiser::OPTION_A);

        $this->assertEquals(1, $fooBitwiser->getState());

        $fooBitwiser->add(FooBitwiser::OPTION_B);

        $this->assertEquals(3, $fooBitwiser->getState());

        $fooBitwiser->add(FooBitwiser::OPTION_C);

        $this->assertEquals(7, $fooBitwiser->getState());
        $this->assertEquals(true, $fooBitwiser->has(FooBitwiser::OPTION_C));

        $fooBitwiser->remove(FooBitwiser::OPTION_B);

        $this->assertEquals(5, $fooBitwiser->getState());
        $this->assertEquals(true, $fooBitwiser->hasNot(FooBitwiser::OPTION_B));

    }

    public function testBitwiserReference()
    {
        $self = $this;
        $state = 7;

        $fooBitwiser = new FooBitwiser($state);

        $fooBitwiser->remove(FooBitwiser::OPTION_B);

        $this->assertEquals($state, $fooBitwiser->getState());
    }

    public function testBitwiserCallback()
    {
        $self = $this;
        $state = 7;

        // Test Closure style callable
        $fooBitwiser = new FooBitwiser($state, function (FooBitwiser $bitwiser) use ($self) {
            $self->setState($bitwiser);
        });

        $fooBitwiser->remove(FooBitwiser::OPTION_B);

        $this->assertEquals($fooBitwiser->getState(), 5);
        $this->assertEquals($fooBitwiser->getState(), $self->state);

        // Test array style callable
        $fooBitwiser = new FooBitwiser($state, array($this, 'setState'));

        $fooBitwiser->remove(FooBitwiser::OPTION_A);

        $this->assertEquals(4, $this->state);
        $this->assertEquals($this->state, $fooBitwiser->getState());
    }

    public function setState($bitwiser)
    {
        $this->state = $bitwiser->getState();
    }
}

class FooBitwiser extends AbstractBitwiser
{
    const OPTION_A = 0;
    const OPTION_B = 1;
    const OPTION_C = 2;
}
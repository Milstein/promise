<?php

namespace React\Promise;

/**
 * @group When
 * @group WhenReduce
 */
class WhenReduceTest extends TestCase
{
    protected function plus()
    {
        return function ($sum, $val) {
            return $sum + $val;
        };
    }

    protected function append()
    {
        return function ($sum, $val) {
            return $sum . $val;
        };
    }

    /** @test */
    public function shouldReduceValuesWithoutInitialValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(6));

        $when = new When();
        $when->reduce(
            array(1, 2, 3),
            $this->plus()
        )->then($mock);
    }

    /** @test */
    public function shouldReduceValuesWithInitialValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(7));

        $when = new When();
        $when->reduce(
            array(1, 2, 3),
            $this->plus(),
            1
        )->then($mock);
    }

    /** @test */
    public function shouldReduceValuesWithInitialPromise()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(7));

        $when = new When();
        $when->reduce(
            array(1, 2, 3),
            $this->plus(),
            $when->resolve(1)
        )->then($mock);
    }

    /** @test */
    public function shouldReducePromisedValuesWithoutInitialValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(6));

        $when = new When();
        $when->reduce(
            array($when->resolve(1), $when->resolve(2), $when->resolve(3)),
            $this->plus()
        )->then($mock);
    }

    /** @test */
    public function shouldReducePromisedValuesWithInitialValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(7));

        $when = new When();
        $when->reduce(
            array($when->resolve(1), $when->resolve(2), $when->resolve(3)),
            $this->plus(),
            1
        )->then($mock);
    }

    /** @test */
    public function shouldReducePromisedValuesWithInitialPromise()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(7));

        $when = new When();
        $when->reduce(
            array($when->resolve(1), $when->resolve(2), $when->resolve(3)),
            $this->plus(),
            $when->resolve(1)
        )->then($mock);
    }

    /** @test */
    public function shouldReduceEmptyInputWithInitialValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $when = new When();
        $when->reduce(
            array(),
            $this->plus(),
            1
        )->then($mock);
    }

    /** @test */
    public function shouldReduceEmptyInputWithInitialPromise()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $when = new When();
        $when->reduce(
            array(),
            $this->plus(),
            $when->resolve(1)
        )->then($mock);
    }

    /** @test */
    public function shouldRejectWhenInputContainsRejection()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(2));

        $when = new When();
        $when->reduce(
            array($when->resolve(1), $when->reject(2), $when->resolve(3)),
            $this->plus(),
            $when->resolve(1)
        )->then($this->expectCallableNever(), $mock);
    }

    /** @test */
    public function shouldResolveWithNullWhenInputIsEmptyAndNoInitialValueOrPromiseProvided()
    {
        // Note: this is different from when.js's behavior!
        // In when.reduce(), this rejects with a TypeError exception (following
        // JavaScript's [].reduce behavior.
        // We're following PHP's array_reduce behavior and resolve with NULL.
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(null));

        $when = new When();
        $when->reduce(
            array(),
            $this->plus()
        )->then($mock);
    }

    /** @test */
    public function shouldAllowSparseArrayInputWithoutInitialValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(3));

        $when = new When();
        $when->reduce(
            array(null, null, 1, null, 1, 1),
            $this->plus()
        )->then($mock);
    }

    /** @test */
    public function shouldAllowSparseArrayInputWithInitialValue()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(4));

        $when = new When();
        $when->reduce(
            array(null, null, 1, null, 1, 1),
            $this->plus(),
            1
        )->then($mock);
    }

    /** @test */
    public function shouldReduceInInputOrder()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo('123'));

        $when = new When();
        $when->reduce(
            array(1, 2, 3),
            $this->append(),
            ''
        )->then($mock);
    }

    /** @test */
    public function shouldAcceptAPromiseForAnArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo('123'));

        $when = new When();
        $when->reduce(
            $when->resolve(array(1, 2, 3)),
            $this->append(),
            ''
        )->then($mock);
    }

    /** @test */
    public function shouldResolveToInitialValueWhenInputPromiseDoesNotResolveToAnArray()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(1));

        $when = new When();
        $when->reduce(
            $when->resolve(1),
            $this->plus(),
            1
        )->then($mock);
    }

    /** @test */
    public function shouldProvideCorrectBasisValue()
    {
        $insertIntoArray = function ($arr, $val, $i) {
            $arr[$i] = $val;

            return $arr;
        };

        $d1 = new Deferred();
        $d2 = new Deferred();
        $d3 = new Deferred();

        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo(array(1, 2, 3)));

        $when = new When();
        $when->reduce(
            array($d1->promise(), $d2->promise(), $d3->promise()),
            $insertIntoArray,
            array()
        )->then($mock);

        $d3->resolve(3);
        $d1->resolve(1);
        $d2->resolve(2);
    }
}

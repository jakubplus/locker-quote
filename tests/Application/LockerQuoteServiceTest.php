<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\LockerQuoteService;
use PHPUnit\Framework\TestCase;

final class LockerQuoteServiceTest extends TestCase
{
    private LockerQuoteService $svc;

    protected function setUp(): void
    {
        $this->svc = new LockerQuoteService();
    }

    public function testFitsIntoAorBOrC(): void
    {
        // A (bardzo płaskie)
        $rA = $this->svc->quote(60, 30, 5);
        self::assertTrue($rA['fits']);
        self::assertSame('A', $rA['locker']['code']);

        // B
        $rB = $this->svc->quote(50, 30, 18);
        self::assertTrue($rB['fits']);
        self::assertSame('B', $rB['locker']['code']);

        // C
        $rC = $this->svc->quote(60, 35, 30);
        self::assertTrue($rC['fits']);
        self::assertSame('C', $rC['locker']['code']);
    }

    public function testRotationIsAllowed(): void
    {
        // Wymiary pasują po rotacji (sortowanie)
        $r = $this->svc->quote(5, 60, 30);
        self::assertTrue($r['fits']);
        self::assertSame('A', $r['locker']['code']);
    }

    public function testSuggestsCourierWhenTooBig(): void
    {
        $r = $this->svc->quote(120, 60, 60); // za grube na skrytkę
        self::assertFalse($r['fits']);
        self::assertArrayHasKey('courier', $r);
        self::assertNotEmpty($r['courier']['name']);
        self::assertArrayHasKey('price_estimated', $r['courier']);
    }

    public function testRejectsJustOverC(): void
    {
        // minimalnie ponad wysokość C (41 cm)
        $r = $this->svc->quote(64, 38, 41.1);
        self::assertFalse($r['fits']);
    }
}

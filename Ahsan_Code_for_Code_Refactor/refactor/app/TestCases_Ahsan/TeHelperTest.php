<?php

namespace Tests\Unit\Helpers;

use App\Helpers\TeHelper;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class TeHelperTest extends TestCase
{
    public function testWillExpireAtReturnsCorrectDateTime()
    {
        // Arrange
        $expiryMinutes = 30; // Expiry time in minutes
        $currentDateTime = Carbon::now(); // Current date and time
        $expectedExpiryDateTime = $currentDateTime->addMinutes($expiryMinutes);

        // Act
        $actualExpiryDateTime = TeHelper::willExpireAt($expiryMinutes);

        // Assert
        $this->assertEquals($expectedExpiryDateTime, $actualExpiryDateTime);
    }
}
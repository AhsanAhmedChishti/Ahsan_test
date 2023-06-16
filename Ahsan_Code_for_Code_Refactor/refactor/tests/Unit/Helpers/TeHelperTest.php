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

            // Expiry time in minutes
            $expiryMinutes = 10; 
        
            // Current date and time
            $currentDateTime = Carbon::now(); 
    
            $expectedExpiryDateTime = $currentDateTime->addMinutes($expiryMinutes);

        // Act
        $actualExpiryDateTime = TeHelper::willExpireAt($expiryMinutes);

        // Assert
        $this->assertEquals($expectedExpiryDateTime, $actualExpiryDateTime);
    }
}
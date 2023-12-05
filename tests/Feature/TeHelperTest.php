<?php

namespace Tests\Feature;

use Carbon\Carbon;
use DTApi\Helpers\TeHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeHelperTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_calculates_correct_expiry_time()
    {
        // Arrange
        $dueTime = Carbon::now()->addHours(3);
        $createdAt = Carbon::now();

        // Act
        $expiryTime = TeHelper::willExpireAt($dueTime, $createdAt);

        // Assert
        $this->assertNotNull($expiryTime);
        $this->assertIsString($expiryTime);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $expiryTime);

        // Add additional assertions based on the expected behavior of the method
    }

    // Add more test methods for other scenarios if needed
}

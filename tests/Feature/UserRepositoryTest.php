<?php

namespace Tests\Feature;

use App\Models\User;
use DTApi\Repository\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Repositories\UserRepository */
    protected $userRepository;

    /** @test */
    public function it_can_create_or_update_a_user()
    {
        // Arrange
        $id = null; // Set the user ID to null for creating a new user
        $requestData = [
            // Set your request data here...
        ];

        // Act
        $this->userRepository = new UserRepository();

        $user = $this->userRepository->createOrUpdate($id, $requestData);

        // Assert
        $this->assertInstanceOf(User::class, $user);

        // Add additional assertions based on the expected behavior of the method
    }
}

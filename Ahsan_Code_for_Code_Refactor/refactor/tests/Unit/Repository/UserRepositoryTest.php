<?php

namespace Tests\Unit\Repository;

use App\Models\User;
use App\Repository\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateOrUpdateCreatesNewUser()
    {
        // Arrange
        $sampleData = [
            'name' => 'Ahmed Ahsan',
            'email' => 'ahmedahsan@mailinator.com',
            'password' => 'Test@123',
        ];

        $userRepository = new UserRepository();

        // Act
        $createdUser = $userRepository->createOrUpdate($sampleData);

        // Assert
        $this->assertDatabaseHas('users', [
            'name' => 'Ahmed Ahsan',
            'email' => 'ahmedahsan@mailinator.com',
        ]);

        $this->assertInstanceOf(User::class, $createdUser);
        $this->assertEquals('Ahmed Ahsan', $createdUser->name);
        $this->assertEquals('ahmedahsan@mailinator.com', $createdUser->email);
    }

    public function testCreateOrUpdateUpdatesExistingUser()
    {
        // Arrange
        $existingUser = User::factory()->create();

        $sampleData = [
            'name' => 'Jane Smith',
            'email' => 'janesmith@example.com',
            'password' => 'newpassword',
        ];

        $userRepository = new UserRepository();

        // Act
        $updatedUser = $userRepository->createOrUpdate($sampleData, $existingUser->id);

        // Assert
        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'name' => 'Jane Smith',
            'email' => 'janesmith@example.com',
        ]);

        $this->assertInstanceOf(User::class, $updatedUser);
        $this->assertEquals('Jane Smith', $updatedUser->name);
        $this->assertEquals('janesmith@example.com', $updatedUser->email);
    }
}
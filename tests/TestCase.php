<?php

namespace Tests;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    protected function actingAsStudent(?Student $student = null): Student
    {
        $student ??= $this->createStudent();
        Sanctum::actingAs($student->user);

        return $student;
    }

    protected function createStudent(array $attributes = []): Student
    {
        $userAttributes = array_merge(
            ['role' => 'student'],
            $attributes['user'] ?? [],
            array_intersect_key($attributes, array_flip(['email', 'name', 'password']))
        );
        unset($attributes['user'], $attributes['name'], $attributes['password']);

        $user = User::factory()->create($userAttributes);

        return Student::query()->create(array_merge([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'status' => Student::STATUS_ACTIVE,
            'terms_accepted_at' => now(),
        ], $attributes));
    }
}

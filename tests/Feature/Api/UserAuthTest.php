<?php

namespace Tests\Feature\Api;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserAuthTest extends TestCase
{
    use RefreshDatabase;

    /** test a registered user */
    public function test_a_registered_user()
    {
        $this->withoutExceptionHandling();

        $data = [
            'email'    => fake()->unique()->safeEmail(),
            'password' => $pass = Hash::make(12345678),
            'password_confirmation' => $pass,
        ];

        $res = $this->post('/api/register', $data);

        $res->assertOk();

        $this->assertDatabaseCount('users', 1);

        $user = User::first();

        $this->assertEquals($data['email'], $user->email);
        $this->assertTrue(Hash::check($data['password'], $user->password));

        $res->assertJson([
            'data' => [
                'email'    => $user->email,
                'password' => $user->password,
            ]
        ]);
    }

    /** test a while registering user data field is required */
    public function test_a_while_registering_user_data_field_is_required()
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->post('/api/register');

        $res->assertStatus(302);
        $res->assertSessionHasErrors(['email', 'password']);
    }

    /** test login successfully */
    public function test_login_successfully()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'email'    => fake()->unique()->safeEmail(),
            'password' => Hash::make(12345678),
        ]);

        $res = $this->post('/api/login', [
            'email'    => $user['email'],
            'password' => 12345678,
        ]);

        $res->assertOk();
    }

    /** test a auth user can access show user page */
    public function test_a_auth_user_can_access_show_user_page()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        Salary::factory()->create();

        $res = $this->actingAs($user)->get('/api/show/' . $user->id);

        $res->assertOk();
    }

    /** test a logout success  */
    public function test_a_logout_success()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'email'    => fake()->unique()->safeEmail(),
            'password' => Hash::make(12345678),
        ]);

        $res = $this->post('/api/login', [
            'email'    => $user['email'],
            'password' => 12345678,
        ]);

        $res->assertOk();

        $token = $user->createToken('MyApp')->plainTextToken;

        $res = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->get('/api/logout');

        $res->assertStatus(200);
        $this->assertGuest('api');
    }
}

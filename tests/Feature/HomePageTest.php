<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_home_page_contains_national(){
        $response = $this->get('/');
        $response->assertSee('National');
    }


public function test_new_user_can_register(): void
{
    // Create a category first if the table is empty
    $categoryId = DB::table('membership_categories')->insertGetId([
        'name'       => 'Regular Member',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $payload = [
        'name'                   => 'John Doe',
        'username'               => 'johndoe',
        'email'                  => 'john@example.com',
        'phone'                  => '08012345678',
        'password'               => 'Password123!',
        'password_confirmation'  => 'Password123!',
        'member_type'            => 'regular',
        'membership_category_id' => $categoryId,
    ];

    $response = $this->post('/register', $payload);

    $this->assertDatabaseHas('users', [
        'email'                  => 'john@example.com',
        'username'               => 'johndoe',
        'membership_category_id' => $categoryId,
    ]);
}
}

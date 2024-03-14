<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('api/register', [
            'name' => 'Laura',
            'email' => 'laura@mail.com',
            'password' => 123456789
        ]);

        $this->assertCount(1, User::all());
        $response->assertJsonFragment(['name' => 'Laura']);
        $response->assertJsonFragment(['msg' => 'Usuario creado correctamente']);
        $response->assertStatus(201);
    }
    
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('abcdefghi')
        ]);
        
        $response = $this->postJson('api/login', [
            'email' => $user->email,
            'password' => 'abcdefghi'
        ]);

        $response->assertJsonFragment(['msg' => 'Usuario correctamente autenticado']); 
        $response->assertJsonStructure(['msg', 'token']);

        //Para testear si el token existe en la base de datos

        $token = DB::table('personal_access_tokens')->where('name', $user->email)->first();

        $this->assertDatabaseHas('personal_access_tokens', [
            'token' => $token->token
        ]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('abcdefghi')
        ]);
        
        $this->postJson('api/login', [
            'email' => $user->email,
            'password' => 'abcdefghi'
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('api/logout');

        $response->assertJsonFragment(['msg' => 'Usuario desconectado correctamente']);
    }
}

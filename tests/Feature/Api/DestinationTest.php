<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Destination;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\Assert;

class DestinationTest extends TestCase
{

    use RefreshDatabase;
    /**
     * Should test get all destinations when method index has not middleware
     */
     
    public function test_user_no_auth_can_see_all_destinations(): void
    {
        $this->withoutExceptionHandling();

        Destination::factory()->create();

        $response = $this->getJson('/api/destinations');

        $response->assertJsonCount(1)
        ->assertStatus(200);
    }


    /**
     * Should test get all destinations when method index has middleware
     */

    public function test_user_auth_can_see_own_destinations_first(): void //NO PASA EL TEST
    {
        $this->withoutExceptionHandling();

        User::factory()->create([
            'id' => 1
        ]);

        $user = User::factory()->create([
            'id' => 2
        ]); 

        Destination::factory()->create([
            'id' => 1,
            'title' => 'Islas Azores',
            'location' => 'Portugal',
            'image' => 'img/azores.jpg',
            'user_id' => 1
        ]);

        Destination::factory()->create([
            'id' => 2,
            'title' => 'Islas Galápagos',
            'location' => 'Ecuador',
            'image' => 'img/galapagos.jpg',
            'user_id' => 2
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/destinations/dashboard');

        $this->assertEquals(2 , $response[0]['user_id']); // El usuario autenticado debería ser el primero

    }

    /**
     * Should test auth user can create a destination
     */
    public function test_auth_user_can_create_a_destination()
     {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Storage::fake('public');
        $file = UploadedFile::fake()->image('cadaques.jpg');

        $response = $this->postJson('/api/destinations', [
            'title' => 'Cadaqués',
            'location' => 'Cataluña',
            'image' => $file
        ]);

        $response->assertStatus(201)
        ->assertJsonFragment(['user_id' => 1]);
        $this->assertEquals(1, $user->destinations->count());
        Assert::assertFileExists(Storage::disk('public')->path('img/' . $file->hashName()));
     }

     /**
     * Should test auth user can see a destination
     */
    public function test_auth_user_can_see_a_destination()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Destination::factory()->create([
            'location' => 'Nepal'
        ]);

        $response = $this->getJson('/api/destination/1');

        $response->assertJsonFragment(['location' => 'Nepal']);
    }

    /**
     * Should test auth user can't see a destination that does not exist
     */
    public function test_auth_user_cant_see_a_not_existing_destination()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/destination/1');

        $response->assertJsonFragment(['msg' => 'No existe ese destino']);
    }

    /**
     * Should test auth user can delete a destination
     */
    public function test_auth_user_can_delete_a_destination()
    {
        $user = User::factory()->create(['id' => 1]);

        Sanctum::actingAs($user);

        Destination::factory()->create(['user_id' => 1]);

        $response = $this->deleteJson('/api/destination/1');

        $response->assertJsonFragment(['msg' => 'Destino borrado correctamente']);
        $this->assertCount(0, Destination::all());
    }

    public function test_auth_user_cannot_delete_a_destination_of_someone_else()
    {
        User::factory()->create(['id' => 1]);
        $userNoAuth = User::factory()->create(['id' => 2]);

        Sanctum::actingAs($userNoAuth);

        Destination::factory()->create(['id' => 1, 'user_id' => 1]);

        $response = $this->deleteJson('/api/destination/1');

        $response->assertJsonFragment(['msg' => 'Destino inexistente']);
        $this->assertCount(1, Destination::all());
    }


     /**
     * Should test user one to many relashionship with destination
     */
     /* public function test_auth_user_can_create_various_destinations()
     {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'id' => 1
        ]);

        Destination::factory()->create([
            'user_id' => $user->id
        ]);

        User::factory()->create([
            'id' => 2
        ]);

        Destination::factory()->create([
            'user_id' => 2
        ]);

        $this->assertEquals(1, $user->destinations->count());
     } */

     /**
     * Should test a destination belongs to one user
     */
    /* public function test_destination_belongs_to_one_user()
     {
        $this->withoutExceptionHandling();

        User::factory()->create([
            'id' => 1
        ]);

        $destination = Destination::factory()->create([
            'user_id' => 1
        ]);

        User::factory()->create([
            'id' => 2
        ]);

        Destination::factory()->create([
            'user_id' => 2
        ]);

        $this->assertIsObject($destination->users());
        $this->assertEquals(1, $destination->user_id);
     } */

     /**
     * Should test user can give like to destination
     */

     public function test_auth_user_can_like_a_destination()
     {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'id' => 1
        ]);
        Sanctum::actingAs($user);

        $destination = Destination::factory()->create([
            'id' => 1,
            'user_id' => $user->id
        ]);

        $response = $this->postJson("/api/destinations/fav/{$destination->id}");

        $response->assertStatus(200)
        ->assertJsonFragment([ 'msg' => 'Has dado like' ]);
        $this->assertTrue($destination->isFavorite->contains($user));
    }

     /**
     * Should test user can eliminate like to a destination
     */

     public function test_auth_user_can_unlike_a_destination()
     {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'id' => 1
        ]);
        Sanctum::actingAs($user);

        $destination = Destination::factory()->create([
            'id' => 1,
            'user_id' => $user->id
        ]);

        $destination->isFavorite()->attach($user);

        $response = $this->postJson("/api/destinations/notfav/{$destination->id}");

        $response->assertStatus(200)
        ->assertJsonFragment([ 'msg' => 'Has dado unlike' ]);
        $this->assertFalse($destination->isFavorite->contains($user));
     }

     

}



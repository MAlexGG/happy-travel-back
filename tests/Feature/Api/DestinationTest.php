<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Destination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class DestinationTest extends TestCase
{

    use RefreshDatabase;
    /**
     * Should test get all destinations when method index has not middleware
     */
     
    /* public function test_user_no_auth_can_see_all_destinations(): void
    {
        $this->withoutExceptionHandling();

        Destination::factory()->create();

        $response = $this->getJson('/api/destinations');

        $response->assertJsonCount(1)
        ->assertStatus(200);
    } */


    /**
     * Should test get all destinations when method index has middleware
     */

    public function test_user_auth_can_see_all_destinations(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        Destination::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/destinations');

        $response->assertJsonCount(1)
        ->assertStatus(200);
    }

    /**
     * Should test auth user can create a destination
     */
    public function test_auth_user_can_create_a_destination()
     {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/destinations', [
            'title' => 'Cadaqués',
            'location' => 'Cataluña'
        ]);

        $response->assertStatus(201)
        ->assertJsonFragment(['user_id' => 1]);
        $this->assertEquals(1, $user->destinations->count());
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



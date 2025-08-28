<?php

namespace Tests\Feature;

use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkCreationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_short_link()
    {
        $response = $this->postJson('/links', [
            'url' => 'https://example.com',
            'title' => 'Test Link',
            'description' => 'Test Description',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Short link created successfully!',
                 ]);

        $this->assertDatabaseHas('links', [
            'original_url' => 'https://example.com',
            'title' => 'Test Link',
            'description' => 'Test Description',
        ]);
    }

    /** @test */
    public function it_validates_required_url()
    {
        $response = $this->postJson('/links', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['url']);
    }

    /** @test */
    public function it_validates_url_format()
    {
        $response = $this->postJson('/links', [
            'url' => 'not-a-valid-url'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['url']);
    }

    /** @test */
    public function it_can_create_link_with_custom_alias()
    {
        $response = $this->postJson('/links', [
            'url' => 'https://example.com',
            'custom_alias' => 'my-custom-link',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('links', [
            'original_url' => 'https://example.com',
            'short_code' => 'my-custom-link',
            'custom_alias' => true,
        ]);
    }

    /** @test */
    public function it_rejects_duplicate_custom_alias()
    {
        // Create first link with custom alias
        Link::create([
            'short_code' => 'existing-alias',
            'original_url' => 'https://first.com',
            'custom_alias' => true,
            'is_active' => true,
        ]);

        // Try to create second link with same alias
        $response = $this->postJson('/links', [
            'url' => 'https://example.com',
            'custom_alias' => 'existing-alias',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['custom_alias']);
    }
}

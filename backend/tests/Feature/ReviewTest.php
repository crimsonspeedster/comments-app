<?php

namespace Tests\Feature;

use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_review(): void
    {
        $data = [
            'user_name' => 'TestUser123',
            'email' => 'test@mail.com',
            'url' => 'https://google.com',
            'text' => 'This is a <strong>test</strong> review.',
        ];

        $response = $this->postJson('/api/v1/reviews', $data);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_name',
                    'email',
                    'url',
                    'text',
                ],
            ])
            ->assertJsonPath('data.user_name', 'TestUser123')
            ->assertJsonPath('data.email', 'test@mail.com')
            ->assertJsonPath('data.url', 'https://google.com')
            ->assertJsonPath(
                'data.text',
                'This is a <strong>test</strong> review.'
            );

        $this->assertDatabaseHas('reviews', [
            'user_name' => 'TestUser123',
            'email' => 'test@mail.com',
            'url' => 'https://google.com',
            'text' => 'This is a <strong>test</strong> review.',
            'parent_id' => null,
        ]);

        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_user_can_create_reply_to_review(): void
    {
        $parentReview = Review::create([
            'user_name' => 'Parent123',
            'email' => 'parent@mail.com',
            'url' => null,
            'text' => 'Parent review',
        ]);

        $data = [
            'parent_id' => $parentReview->id,
            'user_name' => 'Reply123',
            'email' => 'reply@mail.com',
            'text' => 'This is a reply.',
        ];

        $response = $this->postJson('/api/v1/reviews', $data);

        $response
            ->assertCreated()
            ->assertJsonPath('data.parent_id', $parentReview->id)
            ->assertJsonPath('data.user_name', 'Reply123');

        $this->assertDatabaseHas('reviews', [
            'parent_id' => $parentReview->id,
            'user_name' => 'Reply123',
            'email' => 'reply@mail.com',
            'text' => 'This is a reply.',
        ]);

        $this->assertDatabaseCount('reviews', 2);
    }

    public function test_required_fields_are_validated(): void
    {
        $response = $this->postJson('/api/v1/reviews', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'user_name',
                'email',
                'text',
            ]);

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_user_name_must_contain_only_latin_letters_and_digits(): void
    {
        $response = $this->postJson('/api/v1/reviews', [
            'user_name' => 'Олег',
            'email' => 'oleg@mail.com',
            'text' => 'Test review',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['user_name']);

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_email_must_be_valid(): void
    {
        $response = $this->postJson('/api/v1/reviews', [
            'user_name' => 'Oleg123',
            'email' => 'invalid-email',
            'text' => 'Test review',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_url_must_be_valid_when_provided(): void
    {
        $response = $this->postJson('/api/v1/reviews', [
            'user_name' => 'Oleg123',
            'email' => 'oleg@mail.com',
            'url' => '#',
            'text' => 'Test review',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['url']);

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_parent_review_must_exist(): void
    {
        $response = $this->postJson('/api/v1/reviews', [
            'parent_id' => 999999,
            'user_name' => 'Oleg123',
            'email' => 'oleg@mail.com',
            'text' => 'Test reply',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['parent_id']);

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_review_text_is_sanitized(): void
    {
        $data = [
            'user_name' => 'Oleg123',
            'email' => 'oleg@mail.com',
            'text' => '<strong>Allowed</strong><script>alert(1)</script>',
        ];

        $response = $this->postJson('/api/v1/reviews', $data);

        $response->assertCreated();

        $review = Review::query()->firstOrFail();

        $this->assertStringContainsString(
            '<strong>Allowed</strong>',
            $review->text
        );

        $this->assertStringNotContainsString(
            '<script>',
            $review->text
        );

        $this->assertStringNotContainsString(
            'alert(1)',
            $review->text
        );
    }
}

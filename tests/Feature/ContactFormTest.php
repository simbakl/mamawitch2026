<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_submits_successfully(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'subject' => 'Booking',
            'message' => 'Bonjour, nous aimerions vous booker.',
            'honeypot' => '',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Jean Dupont',
            'email' => 'jean@example.com',
        ]);
    }

    public function test_contact_form_rejects_spam_honeypot(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Spammer',
            'email' => 'spam@bot.com',
            'subject' => 'Buy now',
            'message' => 'Spam content',
            'honeypot' => 'gotcha',
        ]);

        $response->assertSessionHasErrors('honeypot');
        $this->assertDatabaseMissing('contact_messages', ['email' => 'spam@bot.com']);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $response = $this->post('/contact', [
            'name' => '',
            'email' => '',
            'message' => '',
            'honeypot' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
    }

    public function test_contact_form_validates_email_format(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Test',
            'email' => 'not-an-email',
            'message' => 'Hello',
            'honeypot' => '',
        ]);

        $response->assertSessionHasErrors('email');
    }
}

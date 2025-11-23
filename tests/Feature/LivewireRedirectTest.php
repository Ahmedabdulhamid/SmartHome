<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
class LivewireRedirectTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_livewire_update_route_redirects_to_localized_route()
    {
        // Set the current locale
        LaravelLocalization::setLocale('ar');

        // Make a POST request to the un-localized livewire update route
        $response = $this->post('/livewire/update', [
            // Add any data if needed
        ]);

        // Assert that the request was redirected to the correct localized route
        $expectedUrl = LaravelLocalization::getLocalizedURL(
            'ar',
            url('/admin/livewire/update')
        );
        $response->assertStatus(302);
        $response->assertRedirect($expectedUrl);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleRoutesTest extends TestCase
{
    public function test_root_without_language_preference_redirects_to_default_locale(): void
    {
        // Explicitly empty: the test client sends "en-us" by default.
        $this->get('/', ['Accept-Language' => ''])->assertRedirect('/pt');
    }

    public function test_root_redirects_english_browsers_to_english(): void
    {
        $this->get('/', ['Accept-Language' => 'en-US,en;q=0.9'])->assertRedirect('/en');
    }

    public function test_root_redirects_portuguese_browsers_to_portuguese(): void
    {
        $this->get('/', ['Accept-Language' => 'pt-BR,pt;q=0.9,en;q=0.8'])->assertRedirect('/pt');
    }

    public function test_root_redirects_other_languages_to_default_locale(): void
    {
        $this->get('/', ['Accept-Language' => 'de-DE,de;q=0.9'])->assertRedirect('/pt');
    }

    public function test_root_redirect_varies_by_accept_language(): void
    {
        $this->get('/')->assertHeader('Vary', 'Accept-Language');
    }

    public function test_portuguese_home_is_served_in_portuguese(): void
    {
        $this->get('/pt')
            ->assertOk()
            ->assertSee('<html lang="pt-BR">', false)
            ->assertSee('Em construção.');
    }

    public function test_english_home_is_served_in_english(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('Under construction.');
    }

    public function test_unsupported_locale_returns_not_found(): void
    {
        $this->get('/xx')->assertNotFound();
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicHomeAssetsTest extends TestCase
{
    public function test_home_uses_existing_public_assets_without_legacy_directory_urls(): void
    {
        $this->assertFileExists(public_path('css/legacy.css'));
        $this->assertFileExists(public_path('js/legacy.js'));

        $this->get('/')
            ->assertOk()
            ->assertSee('css/legacy.css', false)
            ->assertSee('js/legacy.js', false)
            ->assertDontSee('/legacy/', false);
    }
}

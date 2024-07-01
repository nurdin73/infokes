<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminModuleTest extends DuskTestCase
{
    /**
     * A Dusk test login admin.
     */
    public function testLoginAdmin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->visitRoute('home');
        });
    }

    /**
     * A Dusk test page pasien.
     */
    public function testPagePasien(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/pasien')->assertSee('Master Data Pasien');
        });
    }

    /**
     * A Dusk test page pasien.
     */
    public function testPageRegistrationService(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register-pasien')->assertSee('Daftar Registrasi Layanan');
        });
    }
}

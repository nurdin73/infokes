<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegistrationServiceModuleTest extends DuskTestCase
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

    public function testPageSearchRegistrationServiceFound()
    {
        $this->browse(function (Browser $browser) {
            $browser = $browser->visit('/register-pasien')
                ->type('search', "INV000001")
                ->click('#search-data')
                ->assertSee("RM000001");
        });
    }

    public function testPageSearchRegistrationServiceNotFound()
    {
        $this->browse(function (Browser $browser) {
            $browser = $browser->visit('/register-pasien')
                ->type('search', "qwdqwdqwd")
                ->click('#search-data')
                ->assertSee("Layanan pasien tidak ditemukan");
        });
    }

    public function testExportDataRegistrationService()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register-pasien')
                ->seeLink(route('register-pasien.export'));
        });
    }
}

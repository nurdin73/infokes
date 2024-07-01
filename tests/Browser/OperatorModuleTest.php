<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OperatorModuleTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testLoginOperator(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'operator@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->visitRoute('home');
        });
    }

    public function testForbiddenPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/pasien')
                ->assertSee('ANDA TIDAK MEMILIKI AKSES');
        });
    }

    public function testPageRegistrationService(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register-pasien')->assertSee('Daftar Registrasi Layanan');
        });
    }
}

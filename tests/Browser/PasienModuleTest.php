<?php

namespace Tests\Browser;

use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PasienModuleTest extends DuskTestCase
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

    public function testPageSearchPasienFound()
    {
        $this->browse(function (Browser $browser) {
            $browser = $browser->visit('/pasien')
                ->type('search', "RM000001")
                ->press('Cari')
                ->assertSee("123123");
        });
    }

    public function testPageSearchPasienNotFound()
    {
        $this->browse(function (Browser $browser) {
            $browser = $browser->visit('/pasien')
                ->type('search', "qwdqwdqwd")
                ->press('Cari')
                ->assertSee("Pasien tidak ditemukan");
        });
    }

    public function testExportDataPasien()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/pasien')
                ->seeLink(route('pasien.export'));
        });
    }

    public function testInsertPasien()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/pasien')
                ->assertPathIs('/pasien')
                ->click('#createModalButton')
                ->whenAvailable('.modal', function ($modal) {
                    $modal->assertSee('Tambah Data Pasien')
                        ->type('name', Factory::create()->name)
                        ->type('nik', rand() * 10)
                        ->type('birthday', Factory::create()->date())
                        ->select('gender', Factory::create()->randomElement(['Laki-Laki', 'Perempuan']))
                        ->type('address', Factory::create()->address())
                        ->press('Save changes');
                })->assertSee('Pasien baru berhasil ditambahkan');
        });
    }
}

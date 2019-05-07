<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfileManagementTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * @test 
     * @group incomplete
     */
    /*
    public function a_user_should_be_able_to_update_their_profile()
    {
        $user = User::first();

        $this->browse(function(Browser $browser) use ($user) {
            //sadly enough page with account update is left for managers only
        });
    }
     */
}

<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class SettingsManagementTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * @test
     * @group complete
     */
    /*
    public function a_guide_should_not_be_able_to_view_settings_page()
    {

        $user = User::where('role', 'guide')->first();

        $this->browse(function(Browser $browser) use ($user){
            $browser->loginAs($user)
                ->visit('settings')
                ->assertPathIsNot('/settings')
                ->assertDontSee("Settings")
                ->assertPathIs('/leave-days')
                ->assertUrlIs('http://localhost:8000/leave-days');
        });
    }
     */
    /**
     * @test
     * @group complete
     */
    /*
    public function a_manager_should_be_able_to_update_account_details_in_settings_page()
    {
        $user = User::where('role','mngr')->first();

        $this->browse(function(Browser $browser) use ($user){
            $browser->loginAs($user)
                ->visit('/settings')
                ->pause(10000)
                ->assertSee('Profile Settings')
                ->type('input#name', 'test name')
                ->type('input#username', 'new_username')
                ->type('input#phone_no', '2038192834')
                ->press('Save')
                ->waitForText('Profile Successfully Updated!')
                ->assertSee('Profile Successfully Updated!')
                ;
        });
        $this->assertDatabaseHas('users', ['name'=>'test name', 'username'=>'new_username','phone_no'=>'2038192834']);
    }
     */
    /**
     * @test
     * @group incomplete
     */
    public function a_manager_should_be_able_to_update_calenders()
    {
        $user = User::where('role', 'mngr')->first();

        $this->browse(function(Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings')
                ->assertSee('Calendar Settings')
                //->clickLink('Update Calendars')  //Dies Google auth redirect at this particular point
                ;
        });
    }
}

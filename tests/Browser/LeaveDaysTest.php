<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\DatePicker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\LeaveDay;
use App\Setting;
use Carbon\Carbon;

class LeaveDaysTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * @test
     * @group current
     */
    public function a_guide_should_be_able_to_list_their_paginated_payment_leave_days()
    {
        $user = User::where('role', 'guide')->first();

        $leaveDays = factory(LeaveDay::class, 50)->create([
            'user_id'=>$user->id
        ]);

        $this->browse(function(Browser $browser) use ($user, $leaveDays){
            $browser->loginAs($user)
                ->visit('/leave-days')
                ->assertSee('My Unavailable Days')
                ->assertUrlIs('http://localhost:8000/leavedays')
                ->type('#min_date', Carbon::now()->addYears(3)->format('m/d/Y'))
                ->type('#max_date', Carbon::now()->subYears(3)->format('m/d/Y'))
                ->press('Get')
                ->waitFor('.btn-link')
                ->assertSee('Load More')
                ->press('Load More')
                ->pause(2000)
                //not enough assertions// assert vue template load //not getting loadmore button
                ;
        });
    }

    /**
     * @test
     * @group complete
     */
    public function a_guide_should_be_able_to_create_leave_day()
    {
        $user = User::where('role', 'guide')->first();

        $this->browse(function(Browser $browser) use ($user){
            $browser->loginAs($user)
                ->visit('leave-days')
                ->assertSee('New Leave Day')
                ->press('New Leave Day')
                ->whenAvailable('.modal', function($modal){
                    $modal->assertSee('Leave Day Form')
                        ->click('input[type=text]#min_date')
                        ->type('input[type=text]#min_date', Carbon::now()->addDays(2)->format('m/d/Y'))
                        /*->whenAvailable('.bootstrap-datetimepicker-widget', function($datepicker){
                            $datepicker->click('td.day');
                            
                        })*/
                        ->click('input[type=text]#max_date')
                        ->type('input[type=text]#max_date', Carbon::now()->addDays(4)->format('m/d/Y'))
                        /*->whenAvailable('.bootstrap-datetimepicker-widget', function($datepicker){
                            $datepicker->click('td.day');
                        })*/
                        ->type('#summary', 'testing with laravel dusk')
                        ->type('#description', 'Testing software')
                        ->press('Save');
                })
                ->waitForReload()
                ->waitForText('testing with laravel dusk')
                ->assertSee('testing with laravel dusk')
                ->assertPathIs('/leave-days');
        });
        $this->assertDatabaseHas('leave_days', ['summary'=>'testing with laravel dusk','description'=>'Testing software']);
    }

    /**
     * @test
     * @group complete
     */
    public function a_manager_should_be_able_to_view_paginated_leave_days_from_all_users_within_date_range()
    {
        $user = User::where('role', 'mngr')->first();

        $leaveDays = factory(LeaveDay::class, 100)->create();

        Setting::setValue(Setting::READ_CALENDAR_ID_KEY, 'akosdfjowe');

        $this->browse(function(Browser $browser) use ($user){
            $browser->loginAs($user)
                ->visit('leave-days')
                ->assertSee('Leave Days')
                ->value('input:last-child', '')
                ->value('input:first-child', '')
                ->type('input:first-child',Carbon::now()->modify('-2 years')->format('m/d/Y'))
                ->type('input:last-child',Carbon::now()->addMonths(12)->format('m/d/Y'))
                ->press('Get')
                ->pause(2000)
                ->assertSee('Load More')
                ->press('Load More')
                ;
        });

    }
    /**
     * @test
     * @group complete
     */
    public function can_filter_according_to_dates_and_hide_dates_not_within_range()
    {
        $user = User::where('role', 'guide')->first();

        $leaveDay1 = factory(LeaveDay::class)->create([
            'user_id'=>$user->id,
            'starts_at'=>Carbon::now()->subDays(10),
            'ends_at'=>Carbon::now()->subDays(8)
        ]);
        $leaveDay2 = factory(LeaveDay::class)->create([
            'user_id'=>$user->id,
            'starts_at'=>Carbon::now()->subDays(7),
            'ends_at'=>Carbon::now()->subDays(5)
        ]);

        $leaveDay3 = factory(LeaveDay::class)->create([
            'user_id'=>$user->id,
            'starts_at'=>Carbon::now()->subDays(3),
            'ends_at'=>Carbon::now()->subDays(2)
        ]);
        $carbon1 = Carbon::now();
        $carbon2 = Carbon::now();
        $carbon3 = Carbon::now();
        $carbon4 = Carbon::now();
        $this->browse(function(Browser $browser) use ($user, $leaveDay1,$leaveDay2,$leaveDay3, $carbon1, $carbon2,$carbon3,$carbon4){
            $browser->loginAs($user)
                ->visit('/leave-days')
                ->assertSee('Leave Days')
                ->type('#startDate', $carbon1->subDays(11)->format('m/d/Y'))
                ->type('#endDate', $carbon2->subDays(4)->format('m/d/Y'))
                ->press('Get')
                ->waitForText($leaveDay1->summary)
                ->assertSee($leaveDay1->summary)
                ->assertDontSee($leaveDay3->summary)
                ->type('#startDate', $carbon3->subDays(11)->format('m/d/Y'))
                ->type('#endDate', $carbon4->subDays(1)->format('m/d/Y'))
                ->press('Get')
                ->waitForText($leaveDay3->summary)
                ->assertSee($leaveDay3->summary)
            ;
        });
    }

    /**
     * @test
     * @group complete
     */
    public function can_delete_leave_days_and_assert_leave_day_dissapears_from_view()
    {
        $user = User::where('role', 'guide')->first();

        $leaveDay1 = factory(LeaveDay::class)->create([
            'user_id'=>$user->id,
            'starts_at'=>Carbon::now()->subDays(10),
            'ends_at'=>Carbon::now()->subDays(8)
        ]);
        $leaveDay2 = factory(LeaveDay::class)->create([
            'user_id'=>$user->id,
            'starts_at'=>Carbon::now()->subDays(7),
            'ends_at'=>Carbon::now()->subDays(5)
        ]);

        $leaveDay3 = factory(LeaveDay::class)->create([
            'user_id'=>$user->id,
            'starts_at'=>Carbon::now()->subDays(3),
            'ends_at'=>Carbon::now()->subDays(2)
        ]);
        $carbon1 = Carbon::now();
        $carbon2 = Carbon::now();

        $this->browse(function(Browser $browser) use ($user, $leaveDay1,$leaveDay2,$leaveDay3, $carbon1, $carbon2){
            $browser->loginAs($user)
                ->visit('/leave-days')
                ->assertSee('Leave Days')
                ->type('#startDate', $carbon1->subDays(11)->format('m/d/Y'))
                ->type('#endDate', $carbon2->subDays(1)->format('m/d/Y'))
                ->press('Get')
                ->waitForText($leaveDay1->summary)
                ->assertSee($leaveDay1->summary)
                ->click('div.list-group-item:first-child .btn-group .btn-danger')
                ->whenAvailable('#deleteModal', function($modal) use ($leaveDay1){
                    $modal->assertSee("You are about to delete this leave event.")
                        ->assertSee($leaveDay1->summary)
                        ->press('Proceed')
                        ;
                })
                ->waitForText("Leave Day Successfully Deleted")
                ->assertDontSee($leaveDay1->summary)
                ->assertSee($leaveDay2->summary)
                ;
        });
        $this->assertDatabaseMissing('leave_days', ['summary'=>$leaveDay1->summary]);
    }
    /**
     * @test
     * @group complete
     */
    public function a_guide_should_be_able_to_update_leave_day()
    {
        $user = User::where('role', 'guide')->first();

        $leaveDay = factory(LeaveDay::class, 1)->create([
            'user_id'=>$user->id,
            'starts_at'=>'2018-04-05 00:00:00',
            'ends_at'=>'2018-04-07 00:00:00'
        ])->first();

        $this->browse(function(Browser $browser) use ($user, $leaveDay){
            $browser->loginAs($user)
                ->visit('leave-days')
                ->assertSee('Leave Days')
                ->type('#startDate', '04/04/2018')
                ->type('#endDate', '08/04/2018')
                ->press('Get')
                ->assertUrlIs('http://localhost:8000/leave-days')
                ->press('Edit')
                ->whenAvailable('.modal', function($modal) use ($leaveDay){
                    $modal->assertSee($leaveDay->summary)
                        ->type('#min_date','04/06/2018 07:20 pm')
                        ->type('#max_date','04/05/2018 07:20 pm')
                        ->type('#summary', 'Testing update Leave Days test for guides')
                        ->type('#description', 'Leave day testing')
                        ->press('Save');
                })
                ->assertSee('Testing update Leave Days test for guides')
                ;
        });

        $this->assertDatabaseHas('leave_days', ['summary'=>'Testing update Leave Days test for guides','description'=>'Leave day testing']);
        $this->assertDatabaseMissing('leave_days', ['summary'=>$leaveDay->summary]);
    }

    /**
     * @test
     * @group incomplete
     */
    public function a_guide_should_be_able_to_view_leave_day()
    {
        $this->assertTrue(true);
    }
}

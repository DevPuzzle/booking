<?php
namespace Tests\UseCases;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class LeaveDaysTest
 * @package Tests\UseCases
 *
 * @group usecases
 * @group leave-days
 */
class LeaveDaysTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @group incomplete
     */
    public function a_guide_should_be_able_to_list_their_paginated_payment_leave_days()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     * @group incomplete
     */
    public function a_guide_should_be_able_to_create_a_leave_day()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     * @group incomplete
     */
    public function a_guide_should_be_able_to_view_leave_day()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     * @group incomplete
     */
    public function a_guide_should_be_able_to_update_leave_day()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     * @group incomplete
     */
    public function a_guide_should_be_able_to_delete_leave_day()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     * @group incomplete
     */
    public function a_manager_should_be_able_to_view_paginated_leave_days_from_all_users()
    {
        $this->assertTrue(true);
    }

}

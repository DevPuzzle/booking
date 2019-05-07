<?php
namespace Tests\UseCases;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class ProfileManagementTest
 * @package Tests\UseCases
 *
 * @group usecases
 * @group profile
 */
class ProfileManagementTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @group incomplete
     */
    public function a_user_should_be_able_to_update_their_profile()
    {
        $this->assertTrue(true);
    }
}

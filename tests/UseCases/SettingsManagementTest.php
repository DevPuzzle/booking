<?php
namespace Tests\UseCases;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class SettingsManagementTest
 * @package Tests\UseCases
 *
 * @group usecases
 * @group settings
 */
class SettingsManagementTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @group current
     */
    public function a_guide_should_not_be_able_to_view_settings_page()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     * @group incomplete
     */
    public function a_manager_should_be_able_to_update_settings_page()
    {
        $this->assertTrue(true);
    }
}

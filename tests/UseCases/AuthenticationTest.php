<?php

namespace Tests\UseCases;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class AuthenticationTest
 * @package Tests\UseCases
 *
 * @group usecases
 * @group auth
 * @group authentication
 */
class AuthenticationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @group complete
     */
    public function a_user_should_be_able_to_log_in_from_wiki()
    {
        $user = factory(User::class)->create();
        $this->post('auth', ['user_email' => $user->email])
            ->assertStatus(302)
            ->assertRedirect(route('leave-days.index'));
        $this->assertTrue(auth()->check());
    }

    /**
     * @test
     * @group complete
     */
    public function a_user_should_be_redirected_to_wiki_if_not_logged_in()
    {
        $this->get('/')
            ->assertRedirect(route('wiki'))
            ->assertStatus(302);
        $this->assertFalse(auth()->check());
    }

    /**
     * @test
     * @group complete
     */
    public function a_user_should_be_able_to_log_out()
    {
        $user = factory(User::class)->create();
        $this->post('auth', ['user_email' => $user->email]);
        $this->get('logout')
            ->assertStatus(302);
        $this->assertFalse(auth()->check());
    }
}

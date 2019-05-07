<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Page;

class PageModuleTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * @test
     * @group complete
     */
    public function a_admin_can_see_all_listed_pages_where_published_is_true()
    {
        $admin = User::where('role','mngr')->first();
        $pages = factory(Page::class, 10)->create();
        $this->browse(function (Browser $browser) use ($admin, $pages){
            $browser->loginAs($admin)
                ->visit('http://localhost:8000/pages')
                ->assertSee($pages->where('published',true)->first()->summary)
                ->assertDontSee($pages->where('published', false)->first()->summary)
                ;
        });
    }
    /**
     * @test
     * @group complete
     */
    public function a_admin_can_view_his_own_pages()
    {
        $admin = User::where('role', 'mgnr')->first();
        $pages = factory(Page::class, 10)->create(['user_id'=>$admin->id]);

        $this->browse(function (Browser $browser) use ($admin, $pages){
            $browser->loginAs($admin)
                ->visit('http://localhost:8000/admin/pages')
                ->assertSee($pages->first()->summary)
                ->assertSee($pages->last()->summary);
        });
    }

    /**
     * @test
     * @group complete
     */
    public function a_admin_can_create_page()
    {
        $admin = User::where('role', 'mgnr')->first();

        $page = factory(Page::class)->make();

        $this->browse(function(Browser $browser) use ($admin, $page) {
            $browser->loginAs($admin);
            $browser->visit('http://localhost:8000/admin/pages/create');
            $browser->pause(1400);
            $this->typeInCKEditor('#cke_1_contents iframe', $browser, $page->title);
            $this->typeInCKEditor('#cke_2_contents iframe', $browser, $page->summary);
            $this->typeInCKEditor('#cke_3_contents iframe', $browser, $page->content);
            $browser->press('Save')
                ->waitForText($page->title)
                ->assertPathIs('/admin/pages/1')
                ->assertUrlIs('http://localhost:8000/admin/pages/1')
                ->assertSee($page->title)
                ->assertSee($page->summary);
        });
        $this->assertDatabaseHas('pages', ['summary'=>'<p>'.$page->summary.'</p>', 'content'=>'<p>'.$page->content.'</p>']);
    }

    /**
     * @test
     * @group conpm
     */
    public function a_admin_can_update_page_content()
    {
        $admin = User::where('role', 'mngr')->first();
        $savedPage = factory(Page::class)->create(['user_id'=>$admin->id]);
        $updatePage = factory(Page::class)->make();

        $this->browse(function(Browser $browser) use ($admin, $savedPage, $updatePage) {
            $browser->loginAs($admin)
                ->visit('http://localhost:8000/admin/pages/'.$savedPage->id.'/edit')
                ->pause(1000)
                ->assertSee($savedPage->title);

            $this->typeInCKEditor('#cke_1_contents iframe', $browser, $updatePage->title);
            $this->typeInCKEditor('#cke_2_contents iframe', $browser, $updatePage->summary);
            $this->typeInCKEditor('#cke_3_contents iframe', $browser, $updatePage->content);

            $browser->press('Update')
                ->waitForText("Page Content Updated Successfully.")
                ->assertSee($updatePage->title)
                ->pause(3000);
        });

        $this->assertDatabaseHas('pages', ['summary'=> '<p>'.$updatePage->summary.'</p>']);
        $this->assertDatabaseMissing('pages', ['summary'=>$savedPage->summary]);
    }

    /**
     * @test
     * @group complete
     */
    public function a_admin_can_delete_a_page()
    {
        $admin = User::where('role', 'mngr')->first();
        $page = factory(Page::class)->create(['user_id'=>$admin->id]);

        $this->browse(function(Browser $browser) use ($admin, $page){
            $browser->loginAs($admin)
                ->visit('http://localhost:8000/admin/pages/'.$page->id)
                ->assertSee($page->title)
                ->press('delete')
                ->assertPathIs('/admin/pages')
                ->assertDontSee($page->title);
        });
        $this->assertDatabaseMissing('pages', ['title'=>$page->title]);
    }
    
    /**
     * @test
     * @group complete
     */
    public function a_admin_can_publish_his_post()
    {
        $admin = User::where('role', 'mngr')->first();
        $page = factory(Page::class)->create(['user_id'=>$admin->id, 'published'=>false]);

        $this->browse(function(Browser $browser) use ($admin, $page) {
            $browser->loginAs($admin)
                ->visit('http://localhost:8000/admin/pages/'.$page->id)
                ->assertSee($page->title)
                ->clickLink('publish')
                ->assertSee('published');
        });
        $this->assertDatabaseHas('pages', ['title'=>$page->title,'published'=>1]);
    }
}

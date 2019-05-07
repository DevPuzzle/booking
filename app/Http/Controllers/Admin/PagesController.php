<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Page;
use App\Region;
use App\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource of authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $options = [
            'categories'=> \App\Category::all(),
            'regions'=> Region::all(),
            'roles' => Role::all(),
            'statuses' => Page::getStatusesArray(),
            
        ];
        
        $selectedOptions =[
            'categories'=> 'all',
            'regions'=>  'all',
            'Accessibleby' => 'all',
            'status' => 'all',
        ];
        
        $data['pages'] = Page::orderBy('id', 'desc')->paginate(15);
        //dd($data,$options);
        return view('pages.admin.index',compact('selectedOptions'))->with($data)
                                        ->with($options);
    }
    
    public function filteredPages()
    {
        $options = [
            'categories'=> \App\Category::all(),
            'regions'=> Region::all(),
            'roles' => Role::all(),
            'statuses' => Page::getStatusesArray(),
            
        ];
        
        $selectedOptions =[
            'categories'=> request()->get('Categories'),
            'regions'=>  request()->get('regions'),
            'Accessibleby' => request()->get('Accessibleby'),
            'status' => request()->get('status'),
        ];
        
        $filteredPages= Page::query();
        
        // select by category
        if( request()->get('Categories') != 'all' && request()->get('Categories') ){
        
            $filteredPages = \App\Category::find(request()->get('Categories'))->pages();
        }
        
        // select by regions
        if( request()->get('regions') != 'all' && request()->get('regions')){
            $filteredPages = $filteredPages ->whereHas('regions', function ($query) {
                        $query->where('viewable_id', '=', request()->get('regions'));
                });
        }
        // select by roles
        if( request()->get('Accessibleby') != 'all' && request()->get('Accessibleby') ){
        
            $filteredPages = $filteredPages->whereHas('roles', function ($query) {
                        $query->where('viewable_id', '=', request()->get('Accessibleby'));
                });
        }
        // select by statuses
        if( request()->get('status') != 'all' && request()->get('status') ){
            
            //'published','scheduled','draft'
            
            if( request()->get('status') == 'published' ){
                $filteredPages = $filteredPages->where('published_at','<=', Carbon::now());
            }
            elseif ( request()->get('status') == 'scheduled' ) {
                $filteredPages = $filteredPages->where('published_at','>',Carbon::now());
            }
            elseif ( request()->get('status') == 'draft' ) {
                $filteredPages = $filteredPages->whereNull('published_at');
            }
        }
        //!!! work with morph params!!!
        $data['pages'] =$filteredPages->orderBy('id', 'desc')->paginate(15);

        return view('pages.admin.index', compact('selectedOptions'))->with($data)
                                        ->with($options);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.admin.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required'
        ]);

        $page = new Page($request->only('title', 'summary', 'content'));

        $page->save();

        if (sizeof($request->role)>0) {
            $page->roles()->sync($request->role);
        }

        if (sizeof($request->region)>0) {
            $page->regions()->sync($request->region);
        }

        if (sizeof($request->category)>0) {
            $page->categories()->sync($request->category);
        }

        if ($request->date) {
            $page->published_at = Carbon::parse($request->date);
            $page->save();
        }

        if (request()->ajax()) {
            return response()->json($page);
        }

        return redirect()->route('admin.pages.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Page $page
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
        $data['page'] = $page;

        if (request()->ajax()) {
            return response()->json($data);
        }

        return view('pages.admin.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Page $page
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {
        $page->update($request->only('title', 'summary', 'content'));

        if (request()->ajax()) {
            return response()->json($page);
        }

        return redirect('/admin/pages/' . $page->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Page $page
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        $page->destroy();

        return redirect()->route('admin.pages.index');
    }

    /**
     * Attach Page Category
     *
     * @param \App\Page $page
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function category(Page $page, Request $request)
    {
        $page->categories()->sync($request->all());

        return response()->json($request);
    }

    /**
     * Return page categories
     * @param \App\Page $page
     * @return \Illuminate\Http\Response
     */
    public function categories(Page $page)
    {
        $categories = $page->categories()->get();

        return response()->json($categories);
    }

    /**
     * Publish Page From User
     *
     * @param  \App\Page $page
     * @return \Illuminate\Http\Response
     */
    public function publish(Page $page)
    {
        $page->publish();

        if (request()->ajax()) {
            return response()->json($page);
        }
        return redirect()->back();
    }

    /**
     * Unpublish Page From User
     *
     * @param  \App\Page $page
     * @return \Illuminate\Http\Response
     */
    public function unpublish(Page $page)
    {
        $page->unpublish();

        if (request()->ajax()) {
            return response()->json($page);
        }
        return redirect()->back();
    }

    /**
     * Schedule page publishing
     * @param \App\Page $page
     * @return \ILluminate\Http\Response
     */
    public function schedule(Page $page, Request $request)
    {

        $page->published_at = Carbon::parse($request->date);
        $page->save();
        return response()->json($page);
    }

    /**
     * Show the form in new browser window for uploading an image from ckeditor
     * @return \Illuminate\Http\Response
     */
    public function image()
    {
        return view('pages.admin.image');
    }

    /**
     * Add image for ckeditor content
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $path = $request->file('photo')->store('public');

        return response()->json(Storage::url($path));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Page $page
     * @param $type to represent either region or role
     * @return \Illuminate\Http\Response
     */
    public function viewBy(Request $request, Page $page, $type)
    {
        if ($type == 'role') {
            $page->roles()->sync($request->all());
        } elseif ($type == 'region') {
            $page->regions()->sync($request->all());
        }

        return response()->json($request->all());
    }

    /**
     * Display a listing of all Roles
     *
     * @return \Illuminate\Http\Response
     */
    public function roles()
    {
        $roles = Role::whereNotIn('name', ['Administrator'])->get();
        return response()->json($roles);
    }

    /**
     * Display a listing of all Regions
     *
     * @return \Illuminate\Http\Response
     */
    public function regions()
    {
        $regions = Region::all();
        return response()->json($regions);
    }

    public function allowedregions(Page $page)
    {
        $regions = $page->regions()->get();
        return response()->json($regions);
    }

    public function allowedroles(Page $page)
    {
        $roles = $page->roles()->get();
        return response()->json($roles);
    }
}

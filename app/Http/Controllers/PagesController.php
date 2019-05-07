<?php

namespace App\Http\Controllers;

use App\Page;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $options = [
            'categories'=> \App\Category::all(),
            'regions'=> \App\Region::all(),
            'roles' => \App\Role::all(),
            'statuses' => \App\Page::getStatusesArray(),
            
        ];
        $selectedOptions =[
            'categories'=> 'All',
            'regions'=>  'All',
        ];
        
        $data['pages'] = Page::where('published_at', '!=', null)
            ->latest('updated_at')
            ->where(function ($q) {
                $q->doesntHave('roles')
                    ->orWhereHas('roles', function ($q) {
                        $q->where('roles.id', auth()->user()->role_id);
                    });
            })
            ->where(function ($q) {
                $q->doesntHave('regions')
                    ->orWhereHas('regions', function ($q) {
                        $q->where('regions.id', auth()->user()->region_id);
                    });
            })
            ->paginate(3);
            
        
        return view('pages.index',compact('selectedOptions'))->with($data)->with($options);
    }
    
    public function filteredPages()
    {
        $options = [
            'categories'=> \App\Category::all(),
            'regions'=>  \App\Region::all(),
            
            
        ];
        
        $selectedOptions =[
            'categories'=> request()->get('Categories'),
            'regions'=>  request()->get('regions'),
        ];
        
        $pages= Page::where('published_at', '!=', null)
            ->latest('updated_at')
            ->where(function ($q) {
                $q->doesntHave('roles')
                    ->orWhereHas('roles', function ($q) {
                        $q->where('roles.id', auth()->user()->role_id);
                    });
            })
            ->where(function ($q) {
                $q->doesntHave('regions')
                    ->orWhereHas('regions', function ($q) {
                        $q->where('regions.id', auth()->user()->region_id);
                    });
            });
        
        if( request()->get('Categories') != 'all' && request()->get('Categories')){
            $pages = \App\Category::find(request()->get('Categories'))->pages();
        }
        
        //dd($pages);
        
        if( request()->get('regions') != 'all' && request()->get('regions')){
            $pages = $pages ->whereHas('regions', function ($query) {
                        $query->where('viewable_id', '=', request()->get('regions'));
                });
        }
        
        //!!! work with morph params!!!
        /*$data['pages'] = Page::whereHas('regions', function ($query) {
                        $query->where('viewable_id', '=', request()->get('regions'));
                })->orderBy('id', 'desc')->paginate(15);*/
        
        $data['pages'] = $pages->orderBy('id', 'desc')->paginate(3);
        
        return view('pages.index', compact('selectedOptions'))->with($data)
                ->with($options);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Page $page
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page)
    {
        if (!auth()->user()->can('view', $page)) {
            return redirect()->route('pages.index');
        }

        $data['page'] = $page;

        if (request()->ajax()) {
            return response()->json($data);
        }

        return view('pages.show', $data);
    }

    /**
     * Mark page as read by User.
     *
     * @param \App\Page $page
     * @return Illuminate\Http\Reponse
     */
    public function markAsRead(Page $page)
    {
        auth()->user()->pages()->attach($page->id);
        return response()->json(true);
    }

    /**
     * Check if page is read by User
     *
     * @param \App\Page $page
     * @return \Illuminate\Http\Response
     */
    public function isread(Page $page)
    {
        if (auth()->user()->pages()->where('page_id', $page->id)->exists()) {
            return response()->json(true);
        }

        return response()->json(false);
    }
}

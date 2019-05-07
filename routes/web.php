<?php


Route::middleware(['auth'])->group(function () {
    Route::get('leave-days/user/{user}', 'LeaveDaysController@user')->name('leave-days.user');
    // leave-days resource
    Route::resource('leave-days', 'LeaveDaysController');
    Route::get('/', function () {
        return Redirect::to('leavedays');
    });
    Route::get('logout', 'Auth\WpLoginController@logout')->name('logout');

    Route::get('leavedays/{user?}', function (\Illuminate\Http\Request $request, $username = null) {
        if ($request->expectsJson()) {
            $query = \App\LeaveDay::with('user');
            $username = auth()->user()->isAdmin ? $username : auth()->user()->username;
            if ($username) {
                $query->whereHas('user', function ($q) use ($username) {
                    return $q->where('username', $username);
                });
            }
            return $query
                ->where(function ($q) use ($request) {
                    $q->where('starts_at', '>=', $request->get('from', date('Y-m-d H:i:S'), strtotime('2018-03-10')))
                        ->orWhere('ends_at', '<=', $request->get('to', date('Y-m-d H:i:S'), strtotime('+2 weeks')))
                        ->orWhere('recurring_last_instance_date', '<=', $request->get('to', date('Y-m-d H:i:S'), strtotime('+2 weeks')));

                })
                ->get();
        }

        return view('recurrence.index', compact('username'));
    });
    Route::get('timesheets', 'TimesheetsController@index')->name('timesheets.index');
    Route::get('api/timesheets', 'TimesheetsController@getTimesheets');
    Route::get('api/admintimesheets', 'TimesheetsController@getUsersTimesheets');
    Route::post('api/timesheets', 'TimesheetsController@store');
    Route::post('api/deletetimesheets', 'TimesheetsController@delete');
});



Route::middleware(['auth','can:admin,App\User'])->group(function () {
    Route::get('settings', 'SettingsController@index')->name('settings.index');
    Route::put('settings/user', 'SettingsController@updateUser')->name('settings.user.update');
    Route::get('settings/cal', 'SettingsController@updateCalendarSettings')->name('settings.calendar.update');
    Route::put('settings/cal', 'SettingsController@updateCalendar')->name('settings.calendar.update');
    Route::get('sync', 'SettingsController@syncCalendar')->name('settings.sync-calendar');
});

Route::middleware(['auth'])->prefix('api')->namespace('Api')->name('api.')->group(function () {
    Route::resource('leave-days', 'LeaveDaysController', ['only' => ['index']]);
    Route::get('leave-days/user/{user}', 'LeaveDaysController@user')->name('leave-days.user');
});
Route::post('auth', 'Auth\WpLoginController@auth');
Route::get('auth/{user}', 'Auth\WpLoginController@authAlex');



Route::get('wiki', function () {
    return redirect(env('WIKI_URL'));
})->name('wiki');

Route::get('/auth/google/redirect', 'GoogleAuthController@redirect')->name('google.redirect');
Route::get('/auth/google/callback', 'GoogleAuthController@callback')->name('google.callback');

Route::get('/user/page/{page}', 'PagesController@isread')->middleware('auth');
Route::get('/user/page/{page}/markasread', 'PagesController@markAsRead')->middleware('auth');
Route::post('pages/filtered', 'PagesController@filteredPages')->name('pages.filtered')->middleware('auth');
Route::get('pages/filtered', 'PagesController@filteredPages')->name('pages.filtered')->middleware('auth');
Route::resource('pages', 'PagesController')->only(['index', 'show'])->middleware('auth');

Route::middleware(['auth', 'can:admin,App\User'])->prefix('admin')->namespace('Admin')->name('admin.')->group(function(){
    Route::get('/pages/image', 'PagesController@image');
    Route::post('/pages/upload', 'PagesController@upload');
    Route::get('pages/ajax/{page}', 'PagesController@edit');
    Route::get('pages/{page}/publish', 'PagesController@publish');
    Route::post('pages/{page}/schedule', 'PagesController@schedule');
    Route::post('pages/{page}/viewby/{type}', 'PagesController@viewBy');
    Route::resource('pages', 'PagesController')->except(['show']);
    Route::get('pages/{page}/publish', 'PagesController@publish')->name('pages.publish');
    Route::get('pages/{page}/send', 'PagesController@send')->name('pages.send');
    Route::get('pages/{page}/unpublish', 'PagesController@unpublish')->name('pages.unpublish');
    
    Route::get('pages/filtered', 'PagesController@filteredPages')->name('pages.filtered');
    Route::post('pages/filtered', 'PagesController@filteredPages')->name('pages.filtered');
    
    Route::get('roles', 'PagesController@roles');
    Route::get('regions', 'PagesController@regions');
    Route::get('categories', 'PagesController@categories');
    Route::get('pages/{page}/regions/checked', 'PagesController@allowedregions');
    Route::get('pages/{page}/roles/checked', 'PagesController@allowedroles');
    Route::post('pages/{page}/category', 'PagesController@category');
    Route::get('pages/{page}/categories', 'PagesController@categories');
    Route::resource('categories', 'CategoriesController');
});

Route::get('leavedays/{user?}', function (\Illuminate\Http\Request $request, $username = null) {
    if ($request->expectsJson()) {
        $query = \App\LeaveDay::with('user');
        $username = auth()->user()->isAdmin ? $username : auth()->user()->username;
        if ($username) {
            $query->whereHas('user', function ($q) use ($username) {
                return $q->where('username', $username);
            });
        }
         return $query->where(function ($q) use ($request) {
                $from = $request->get('from', date('Y-m-d H:i:S'), strtotime('yesterday'));
                $to = $request->get('to', date('Y-m-d H:i:S'), strtotime('+2 weeks'));
                $q->whereDate('starts_at', '>=', $from)
                    ->orWhereDate('ends_at', '<=', $to)
                    ->orWhereDate('recurring_last_instance_date', '<=', $to);

            })
            ->get();
    }

    return view('recurrence.index', compact('username'));
})->middleware('auth');

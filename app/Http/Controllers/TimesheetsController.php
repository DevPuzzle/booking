<?php

namespace App\Http\Controllers;

use App\Timesheet;
use Illuminate\Http\Request;

class TimesheetsController extends Controller
{
  public function index()
  {
    $data['user'] = auth()->user();
    return view('timesheets.index', $data);
  }

  public function getUsersTimesheets(Request $request)
  {
    $this->validate($request, [['start_date' => 'required|date'], ['end_date' => 'required|date']]);
    $start_date = $request->get('start_date');
    $end_date = $request->get('end_date');
    
    //$usersTimeSheets = with(\App\User::class);
    
    return Timesheet::with('user')->whereBetween('date', [$start_date, $end_date])->get();
  }
  
  public function getTimesheets(Request $request)
  {
    $this->validate($request, [['start_date' => 'required|date'], ['end_date' => 'required|date']]);
    $start_date = $request->get('start_date');
    $end_date = $request->get('end_date');
    $user = auth()->user();
    return $user->timesheets()->whereBetween('date', [$start_date, $end_date])->get();
  }
  public function delete(Request $request) {

        $item = $request->all();
        Timesheet::destroy($item['id']);
        return response()->json(array('status' => 'delete success', 'id deleted' => $item['id']), 200);
    }

    public function store(Request $request)
  {
    $timesheet = $request->all();
    $user = auth()->user();
    collect($timesheet)->map(function ($timesheetDay) use ($user) {
        
        if( isset($timesheetDay['id']) ){
            
            Timesheet::updateOrCreate(['id' => $timesheetDay['id']],
          [
              'user_id' => $user->id,
              'hours' => $timesheetDay['hours'],
              'notes' => $timesheetDay['notes'],
              'date' => $timesheetDay['date']
          ]);
        }
        else{
            Timesheet::create(
                    [
              'user_id' => $user->id,
              'hours' => $timesheetDay['hours'],
              'notes' => $timesheetDay['notes'],
              'date' => $timesheetDay['date']
          ]);
        }
      
    
      
    });
    return response()->json(array('status' => 'success'), 201);
  }
}

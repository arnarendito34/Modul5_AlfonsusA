<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
      $pageTitle = 'Employee List';
      //RAW SQL QUERY
      $employees = DB::select('
      select *, employees.id as employee_id, positions.name as position_name from employees left join positions on employees.position_id = positions.id
      ');

      //RAW SQL BUILDER
      $tes = DB::table('employees')
            ->select('*','employees.id as employee_id','positions.name as position_name' )
            ->leftjoin('positions', 'employees.position_id', '=', 'positions.id')
            ->get();
      return view('employee.index', [
        'pageTitle' => $pageTitle,
        'employees' => $tes
    ]);
    }

    public function create()
    {
        $pageTitle = 'Create Employee';
        //RAW SQL BUILDER
        $testing = DB::select('select * from positions');
        $positions = DB::table('positions')
                    ->select('*')
                    ->get();
        return view('employee.create', compact('pageTitle','positions'));
    }

    public function store(Request $request)
    {
        $messages = [
            'required' => ':Attribute harus diisi.',
            'email' => 'Isi :attribute dengan format yang benar',
            'numeric' => 'Isi :attribute dengan angka'
        ];

        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'age' => 'required|numeric',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::table('employees')->insert([
            'firstname' => $request->firstName,
            'lastname' => $request->lastName,
            'email' => $request->email,
            'age' => $request->age,
            'position_id' => $request->position,
        ]);
        return redirect()->route('employees.index');
    }

    public function show(string $id)
    {
        $pageTitle = 'Employee Detail';

        $employees = collect(DB::select('select *, employees.id as employee_id, positions.name as
        position_name from employees left join positions on employees.position_id = positions.id where employees.id = ?'
        ,[$id]))->first();

        //RAW SQL BUILDER
        $employee = DB::table('employees')
                    ->select('*','employees.id as employee_id','positions.name as position_name' )
                    ->leftjoin('positions', 'employees.position_id', '=', 'positions.id')
                    ->where('employees.id', '=', $id)
                    ->first();
        return view('employee.show', compact('pageTitle','employee'));
    }

    public function edit(string $id)
    {
        $pageTitle = 'Employee Edit';

        $employee = DB::table('employees')
                    ->select('*','employees.id as employee_id','positions.name as position_name' )
                    ->leftjoin('positions', 'employees.position_id', '=', 'positions.id')
                    ->where('employees.id', '=', $id)
                    ->first();
        $positions= DB::table('positions')->select('*')->get();
        return view('employee.edit',compact('pageTitle','employee','positions'));
    }

    public function update(Request $request, $id)
    {
        $messages = [
            'required' => ':Attribute harus diisi.',
            'email' => 'Isi :attribute dengan format yang benar',
            'numeric' => 'Isi :attribute dengan angka'
        ];

        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'age' => 'required|numeric',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        DB::table('employees')->where('id',$id)->update([
            'firstname' => $request->firstName,
            'lastname' => $request->lastName,
            'email' => $request->email,
            'age' => $request->age,
            'position_id' => $request->position,
        ]);
        return redirect()->route('employees.index');
    }

    public function destroy(string $id)
    {
        DB::table('employees')->where('id', $id)->delete();

        return redirect()->route('employees.index');
    }
}

<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Http\Validations\Config\EmployeeValidation;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees=Employee::paginate(5);
        return response([
            'data_list'=>$employees
        ]);
    }
    public function store(Request $request)
    {
        $validate = EmployeeValidation::validate($request);
        if (!$validate['success']) {
            return response($validate);
        }
        $employee=Employee::create([
            'name'=>$request->name,
            'address'=>$request->address,
            'designation'=>$request->designation,
            'salary'=>$request->salary
        ]);
        if ($employee) {
            return response([
                'data_list'=>$employee
            ]);
        }else{
            return response([
                'message'=>'Failed'
            ]);
        }
    }
    public function update(Request $request,$id)
    {
        $validate = EmployeeValidation::validateUpdate($request);
        if (!$validate['success']) {
            return response($validate);
        }
        $employee=Employee::find($id);
        if ($employee) {
            $employee->name=$request->name;
            $employee->address=$request->address;
            $employee->designation=$request->designation;
            $employee->salary=$request->salary;
            $employee->save();
            return response([
                'data_list'=>$employee
            ]);
        }else{
            return response([
                'message'=>'not found'
            ]);
        }
    }
    public function delete($id)
    {
        $employee=Employee::find($id);
        if ($employee) {
            $employee->delete();
            return response([
                'message'=>'deleted'
            ]);
        }else{
            return response([
                'message'=>'not found'
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees);
    }

    public function show($id)
    {
        $employee = Employee::find($id);
        return response()->json($employee);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'gender' => 'required|in:male,female,non-binary,prefer-not-to-say',
            'birthday' => 'required|date',
            'monthly_salary' => 'required|numeric'
        ]);

        $employee = Employee::create($validated);
        return response()->json($employee, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'gender' => 'required|in:male,female,non-binary,prefer-not-to-say',
            'birthday' => 'required|date',
            'monthly_salary' => 'required|numeric'
        ]);

        $employee = Employee::find($id)->update($validated);
        return response()->json($employee);
    }

    public function destroy($id)
    {
        Employee::destroy($id);
        return response()->json(null, 204);
    }
}

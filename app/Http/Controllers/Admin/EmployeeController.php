<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::paginate(15);
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        $dzongkhags = DB::table('tbldzongkhag')->orderBy('name')->get();
        $gewogs = DB::table('tblgewog')->orderBy('name')->get();
        
        $gewogsByDzongkhag = $gewogs->groupBy('dzongkhag_id')->map(function ($gewogs) {
            return $gewogs->values();
        })->toArray();
        
        return view('admin.employees.create', compact('dzongkhags', 'gewogsByDzongkhag'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cid' => 'required|string|unique:employees',
            'phone' => 'nullable|string|max:20',
            'role_title' => 'required|string|in:cleaner,guard,gardener',
            'dzongkhag_id' => 'nullable|integer|exists:tbldzongkhag,id',
            'gewog_id' => 'nullable|integer|exists:tblgewog,id',
            'address' => 'nullable|string',
            'joining_date' => 'required|date',
            'status' => 'required|in:Active,Inactive',
        ]);

        Employee::create($validated);
        return redirect()->route('admin.employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        $dzongkhags = DB::table('tbldzongkhag')->orderBy('name')->get();
        $gewogs = DB::table('tblgewog')->orderBy('name')->get();
        
        $gewogsByDzongkhag = $gewogs->groupBy('dzongkhag_id')->map(function ($gewogs) {
            return $gewogs->values();
        })->toArray();
        
        return view('admin.employees.edit', compact('employee', 'dzongkhags', 'gewogsByDzongkhag'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cid' => 'required|string|unique:employees,cid,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'role_title' => 'required|string|in:cleaner,guard,gardener',
            'dzongkhag_id' => 'nullable|integer|exists:tbldzongkhag,id',
            'gewog_id' => 'nullable|integer|exists:tblgewog,id',
            'address' => 'nullable|string',
            'joining_date' => 'required|date',
            'status' => 'required|in:Active,Inactive',
        ]);

        $employee->update($validated);
        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted successfully.');
    }
}

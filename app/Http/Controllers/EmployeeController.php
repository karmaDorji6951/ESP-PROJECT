<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::latest()->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateEmployee($request);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('employees', 'public');
        }

        Employee::create($data);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $this->validateEmployee($request, $employee->id);

        if ($request->hasFile('photo')) {
            if ($employee->photo_path) {
                Storage::disk('public')->delete($employee->photo_path);
            }

            $data['photo_path'] = $request->file('photo')->store('employees', 'public');
        }

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->photo_path) {
            Storage::disk('public')->delete($employee->photo_path);
        }

        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    private function validateEmployee(Request $request, ?int $employeeId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cid' => ['required', 'string', 'max:50', Rule::unique('employees', 'cid')->ignore($employeeId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role_title' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'joining_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);
    }
}

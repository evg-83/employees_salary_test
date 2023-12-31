<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Salary\SalaryRequest;
use App\Http\Resources\Salary\SalaryResource;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    /** Accepting a transaction */
    public function store(SalaryRequest $request)
    {
        try {
            $data = $request->validated();

            $userAuthId = auth()->user()->id;
            $salaries = Salary::all();

            foreach ($salaries as $salary) {
                $userData = $salary->where('employee_id', $userAuthId)->exists();
            }

            if (!empty($userData)) {
                Salary::where('employee_id', $userAuthId)->increment('hours', $data['hours']);
            } else {
                $salary = Salary::create($data);
            }

            $response = [
                'success' => true,
                'data'    => SalaryResource::make($salary),
                'message' => 'Employee details entered successfully.',
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /** Amount withdrawal */
    public function show(User $user)
    {
        try {
            $salaries = Salary::all();

            foreach ($salaries as $salary) {
                $userPayAmount = $salary
                    ->where('employee_id', $user->id)
                    ->select(DB::raw('salaries.*, (salaries.hours * salaries.rate) as payAmount'))
                    ->value('payAmount');

                $userEmployeeId = $salary
                    ->where('employee_id', $user->id)
                    ->value('employee_id');
            }

            $userSalary = Arr::add([$userEmployeeId=>$userPayAmount], $userEmployeeId, $userPayAmount);

            return response()->json($userSalary, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /** Payment of the entire accumulated amount */
    public function destroy(User $user)
    {
        try {
            $salaries = Salary::all();

            foreach ($salaries as $salary) {
                $salary->where('employee_id', $user->id)->delete();
            }

            return response()->json([
                'message' => 'Redemption of the transaction was successful.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }
}

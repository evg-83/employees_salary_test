<?php

namespace Tests\Feature\Api;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SalaryTest extends TestCase
{
    use RefreshDatabase;

    /** test stored employee payroll data */
    public function test_stored_employee_payroll_data()
    {
        $this->withoutExceptionHandling();

        $data = [
            'employee_id' => 1,
            'hours'       => 8,
            'rate'        => 250,
        ];

        $user = User::factory()->create();
        $res = $this->actingAs($user)->post('/api/store', $data);

        $res->assertOk();

        $this->assertDatabaseCount('salaries', 1);

        $salary = Salary::first();

        $this->assertEquals($data['employee_id'], $salary->employee_id);
        $this->assertEquals($data['hours'], $salary->hours);
        $this->assertEquals($data['rate'], $salary->rate);

        $res->assertJson([
            'data' => [
                'employee_id' => $salary->employee_id,
                'hours'       => $salary->hours,
                'rate'        => $salary->rate,
            ]
        ]);
    }

    /** test a while storing salary data field is required */
    public function test_a_while_storing_salary_data_field_is_required()
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->post('/api/store');

        $res->assertStatus(302);
        $res->assertSessionHasErrors(['employee_id', 'hours', 'rate']);
    }

    /** test a amount withdrawal */
    public function test_a_amount_withdrawal_show()
    {
        $this->withoutExceptionHandling();

        $user   = User::factory()->create();

        $salaries = Salary::factory()->create();

        $res = $this->actingAs($user)->get('/api/show/' . $user->id);

        $res->assertOk();

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

        $userSalary = Arr::add([$userEmployeeId => $userPayAmount], $userEmployeeId, $userPayAmount);

        $res->assertJson($userSalary);
    }

    /** Payment of the entire accumulated amount */
    public function test_a_payment_of_the_entire_accumulated_amount()
    {
        $this->withoutExceptionHandling();

        $user   = User::factory()->create();

        Salary::factory()->create();

        $res = $this->actingAs($user)->delete('/api/show/' . $user->id);

        $res->assertOk();

        $this->assertDatabaseCount('salaries', 0);
    }
}

<?php

namespace Tests\Feature\Api;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Salary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
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

        $res = $this->post('/api/store', $data);

        $res->assertOk();

        $this->assertDatabaseCount('salaries', 1);

        $salary = Salary::first();

        $this->assertEquals($data['employee_id'], $salary->employee_id);
        $this->assertEquals($data['hours'], $salary->hours);
        $this->assertEquals($data['rate'], $salary->rate);

        $res->assertJson([
            'id'          => $salary->id,
            'employee_id' => $salary->employee_id,
            'hours'       => $salary->hours,
            'rate'        => $salary->rate,
        ]);
    }
}

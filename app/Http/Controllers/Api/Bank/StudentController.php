<?php

namespace App\Http\Controllers\Api\Bank;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\FeesService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StudentController extends Controller
{
    protected $feesService;

    public function __construct(FeesService $feesService)
    {
        $this->feesService = $feesService;
    }

    function find(Request $request)
    {
        $request->validate(['software_id' => 'required']);

        $student = Student::where(['software_id' => $request->software_id])->first();
        throw_unless($student, NotFoundHttpException::class, 'Student not found.');

        $data['student'] = Student::select('ins.name as institution_name', 'software_id', 'name_en', 'name_bn', 'prof.fathers_name_en', 'prof.mothers_name_en')
            ->join('institutions as ins', 'ins.id', 'students.institution_id')
            ->join('student_profiles as prof', 'prof.student_id', 'students.id')
            ->where(['software_id' => $request->software_id])
            ->first();

        $data['fees_lists'] = $this->fees($student);

        return $this->sendResponse($data);
    }

    private function fees($student)
    {
        $dues_fees_lists = $this->feesService->dues($student);

        $fees_lists = [];
        foreach ($dues_fees_lists as $key => $duesFees) {
            $discount = $student->discounts()
                ->where('account_head_id', $duesFees->account_head_id)
                ->value('amount') ?? 0;

            $fees = $duesFees->toArray();

            $fees['discount_amount'] = $discount;
            $fees['payable_amount'] = $fees['amount'] - $discount;

            if ($fees['account_head_type'] == 'tuition') {
                $fees = $this->tuitionFees($student, $fees, $discount);
            }

            if (!empty($fees)) {
                unset($fees['fine_amount']);
                array_push($fees_lists, $fees);
            }
        }
        return $fees_lists;
    }

    private function tuitionFees($student, $fees, $discount)
    {
        $tuitionFees = $this->feesService->tuitions($student)
            ->where('status', '!=', 'success')
            ->where('generate_month', false);

        if ($tuitionFees->count() == 0) {
            return [];
        }

        $tuition_fees = [];
        foreach ($tuitionFees as $tuitionFee) {
            array_push($tuition_fees, [
                'id' => $tuitionFee->id,
                'date' => $tuitionFee->date,
                'amount' => (int) $tuitionFee->amount,
                'discount_amount' => $discount,
                'payable_amount' => $tuitionFee->amount -  $discount,
            ]);
        }

        $tuitionFeesAmount = $tuitionFees->sum('amount');
        $tuitionsDiscount = $discount * count($tuitionFees);
        $fees['amount'] = $tuitionFeesAmount;
        $fees['discount_amount'] = $tuitionsDiscount;
        $fees['payable_amount'] = $tuitionFeesAmount - $tuitionsDiscount;
        $fees['tuition_months'] = $tuition_fees;

        return (array) $fees;
    }
}
<?php

namespace App\Services;

use App\Models\FeeSetup;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\TuitionFeeGenerate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FeesService
{
    /**
     * Get fees lists
     *
     * @param \App\Models\Student $student
     * @return \Illuminate\Http\Responses
     */
    public function all($student)
    {
        $data       = [];
        $conditions = Student::conditions($student);
        $feeSetup   = FeeSetup::with('details')->where($conditions)->first();

        if (!empty($feeSetup)) {
            $data = $feeSetup->details()
                ->join('account_heads', 'account_heads.id', '=', 'fee_setup_details.account_head_id')
                ->select(
                    'account_head_id',
                    'account_heads.type as account_head_type',
                    'account_heads.name_en as account_head_name',
                    'start_date',
                    'expire_date',
                    'additional_date',
                    'amount'
                )
                ->get();
        }

        return $data;
    }

    /**
     * Get dues fees lists
     *
     * @param \App\Models\Student $student
     * @return \Illuminate\Http\Response
     */
    public function dues($student)
    {
        $duesFees = [];
        $commonConditions = Student::commonArr($student);

        $conditions = Student::conditions($student);
        $feeSetup = FeeSetup::where($conditions)->first();

        Log::channel('dev-log')->info('Student: ', $student->toArray());
        Log::channel('dev-log')->info('Conditions: ', $conditions);

        if (!empty($feeSetup->details)) {
            $monthlyID = $feeSetup->details()->where('payment_duration', 'Monthly')->pluck('account_head_id')->toArray();
            $yearlyID  = $feeSetup->details()->where('payment_duration', 'Yearly')->pluck('account_head_id')->toArray();

            $monthlyFees = Invoice::where($commonConditions)
                ->whereMonth('invoice_date', date('m'))
                ->whereIn('account_head_id', $monthlyID)
                ->where('student_id', $student->id)
                ->pluck('account_head_id')
                ->toArray();
            $yearlyFees = Invoice::where($commonConditions)
                ->whereYear('invoice_date', date('Y'))
                ->whereIn('account_head_id', $yearlyID)
                ->where('student_id', $student->id)
                ->pluck('account_head_id')
                ->toArray();

            $headID = array_merge($monthlyFees, $yearlyFees);

            $duesFees = $feeSetup->details()
                ->join('account_heads', 'account_heads.id', '=', 'fee_setup_details.account_head_id')
                ->join('payment_gateways as gateway', 'gateway.id', '=', 'fee_setup_details.payment_gateway_id')
                ->select(
                    'gateway.provider',
                    'fee_setup_details.id',
                    'account_head_id',
                    'depend_head_id',
                    'account_heads.type as account_head_type',
                    'account_heads.name_en as account_head_name',
                    'amount'
                )
                ->where('school_fees', 1)
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('additional_date', '>=', date('Y-m-d'))
                ->whereNotIn('account_head_id', $headID)
                ->get();
        }

        return $duesFees;
    }

    /**
     * Get tuitions fees lists
     *
     * @param \App\Models\Student $student
     * @return \Illuminate\Http\Response
     */
    // public function tuitions($student)
    // {
    //     $conditions = Student::conditions($student);
    //     $commonConditions = Student::commonArr($student);
    //     $tuitionFees = TuitionFeeGenerate::where($conditions)->first();
    //     throw_unless($tuitionFees, NotFoundHttpException::class, 'Tuition fees not generated.');

    //     $details = $tuitionFees->details()
    //         ->where('status', 1)
    //         ->select('id', 'date', 'amount')  // Make sure 'month' is included
    //         ->get()
    //         ->map(function ($item) use ($student, $commonConditions) {
    //             $status = $item->invoice_details()
    //                 ->whereHas('invoice', function ($subquery) use ($student, $commonConditions) {
    //                     $subquery->where($commonConditions)->where('student_id', $student->id);
    //                 })->value('status');

    //             $item['generate_month'] = $status ? true : false;
    //             $item['checked'] = $status ? true : false;
    //             $item['status'] = $status ?? 'pending';
    //             return $item;
    //         });

    //     return [
    //         'fees_lists' => $details,
    //         'class_id' => $student->academic_class_id ?? null,
    //     ];
    // }

    public function tuitions($student)
    {
        $conditions = Student::conditions($student);
        $commonConditions = Student::commonArr($student);
        $tuitionFees = TuitionFeeGenerate::where($conditions)->first();
        throw_unless($tuitionFees, NotFoundHttpException::class, 'Tuition fees not generated.');

        return $tuitionFees->details()
            ->where('status', 1)
            ->select('id', 'date', 'amount')->get()
            ->map(function ($item) use ($student, $commonConditions) {
                $status = $item->invoice_details()
                    ->whereHas('invoice', function ($subquery) use ($student, $commonConditions) {
                        $subquery->where($commonConditions)->where('student_id', $student->id);
                    })->value('status');

                $item['generate_month'] = $status ? true : false;
                $item['checked'] = $status ? true : false;
                $item['status'] = $status ?? 'pending';
                return $item;
            });
    }


    public function tuitionDuesFees($student)
    {
        $conditions = Student::conditions($student);
        $tuitionFees = TuitionFeeGenerate::where($conditions)->first();
        if (!$tuitionFees) {
            return collect();
        }

        return $tuitionFees->details()
            ->where('status', 1)
            ->where(function ($query) use ($student) {
                $query->whereDoesntHave('invoice_details', function ($subQuery) use ($student) {
                    $subQuery->whereHas('invoice', function ($sub) use ($student) {
                        $sub->where('student_id', $student->id);
                    });
                })
                    ->orWhereHas('invoice_details', function ($subQuery) use ($student) {
                        $subQuery->where('status', '!=', 'success')
                            ->whereHas('invoice', function ($sub) use ($student) {
                                $sub->where('student_id', $student->id);
                            });
                    });
            })
            ->select('id', 'date', 'amount')
            ->get();
    }
}

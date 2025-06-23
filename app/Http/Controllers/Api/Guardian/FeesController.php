<?php

namespace App\Http\Controllers\Api\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Services\FeesService;

class FeesController extends Controller
{
    protected $feesService;

    public function __construct(FeesService $feesService)
    {
        $this->feesService = $feesService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $student = auth()->user()->current_student;
        $data    = $this->feesService->all($student);
        return $this->sendResponse($data);
    }

    /**
     * Get dues fees lists
     *
     * @return \Illuminate\Http\Response
     */
    public function duesFeesLists()
    {
        $student = auth()->user()->current_student;

        $data['invoice_no'] = Invoice::invoiceNumber();
        $data['fees_lists'] = $this->feesService->dues($student);
        $data['discounts']  = [];

        if (!empty($student->discounts)) {
            $data['discounts'] = $student->discounts()->where([
                'academic_class_id' => $student->academic_class_id,
                'academic_session_id' => $student->academic_session_id,
                'institution_id' => $student->institution_id
            ])->get();
        }

        return $this->sendResponse($data);
    }

    /**
     * Get tuition fees lists
     *
     * @return \Illuminate\Http\Response
     */
    public function tuitionFees()
    {
        $student = auth()->user()->current_student;
        $data    = $this->feesService->tuitions($student);

        return $this->sendResponse($data);
    }
}

<?php

namespace App\Http\Controllers\Api\Guardian;

use App\Http\Controllers\Api\Guardian\Traits\FindResultTrait;
use App\Http\Controllers\Controller;
use App\Models\MasterSetup\Exam;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class ResultController extends Controller
{
    use FindResultTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function examLists(Request $request)
    {
        $query = Exam::latest('id')
            ->select('name', 'institution_category_id', 'id')
            ->with('class_test_exam', 'institution_category')
            ->where([
                'exam_type' => $request->exam_type,
                'institution_category_id' => $request->institution_category_id,
            ]);

        $data = $query->get();
        return $this->sendResponse($data);
    }

    /**
     * Search results
     *
     * @return \Illuminate\Http\Response
     */
    public function result(Request $request)
    {
        $student_id = auth()->user()->current_student->id ?? '';
        $category = $request->institution_category_id;

        $model = '';
        switch ($category) {
            case 1:
                $model = \App\Models\Result\PrimaryResultDetails::class;
                break;
            case 2:
                $model = \App\Models\Result\SecondaryResultDetails::class;
                break;
        }
        throw_unless($model, Exception::class, 'Institution Category is required!', 422);

        $data = $this->getResult($model, $student_id, $request, $category);

        throw_unless($data, Exception::class, 'Result not found!', 404);
        throw_if($data->result->status != 'published', Exception::class, 'Result not published!', 404);
        throw_if($data->result->published_date > date('Y-m-d'), Exception::class, 'Result not published!', 404);

        return $this->sendResponse($data);
    }

    /**
     * Download marksheet 
     *
     * @param  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downloadMarksheet(Request $request)
    {
        $student_id = auth()->user()->current_student->id ?? '';
        $category = $request->institution_category_id;

        $marksheet = '';
        $model = '';
        $table = '';
        switch ($category) {
            case 1:
                $table = 'primary';
                $marksheet = 'pdf.primary_marksheet';
                $model = \App\Models\Result\PrimaryResultDetails::class;
                break;
            case 2:
                $table = 'secondary';
                $marksheet = 'pdf.secondary_marksheet';
                $model = \App\Models\Result\SecondaryResultDetails::class;
                break;
        }
        throw_unless($model, Exception::class, 'Institution Category is required!', 422);

        $detail = $this->getResult($model, $student_id, $request, $category);

        $highest_marks = $this->getHighestMarks($model, $detail->result, $table);

        if (empty($request->pdf)) {
            return $this->sendResponse([
                'result' => $detail,
                'highest_marks' => $highest_marks
            ]);
        }

        $storage_url = config('app.do_asset_url');
        $pdf      = Pdf::loadView($marksheet, compact('detail', 'highest_marks', 'storage_url'))->setPaper('a4', 'landscape');
        $fileName = "marksheet.pdf";
        return $pdf->download($fileName);
        // return $pdf->stream();
    }
}

<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\MasterSetup;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\AcademicQualification;
use App\Models\Department;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DepartmentController extends Controller {

    public function index( Request $request ) {
        $query = Department::latest( 'id' );
        $query->whereLike( $request->field_name, $request->value );
        $query->whereAny( 'status', $request->status );

        if ( $request->allData ) {
            return $query->pluck( 'name', 'id' )->toArray();
        } else {
            $datas = $query->paginate( $request->pagination );
            return new Resource( $datas );
        }
    }


    public function create() {
        return view( 'layouts.backend_app' );
    }

 
    public function store( Request $request ) {
        if ( $this->validateCheck( $request ) ) {
            try {
                $data = $request->except( 'dept_qualifications', 'checked_qualifications' );
                $res  = Department::create( $data );

                if ( $res && !empty( $request->checked_qualifications ) ) {
                    $res->department_qualifications()->createMany( $request->checked_qualifications );
                }
                Artisan::call( 'cache:clear' );
                return response()->json( ['message' => 'Create Successfully!', 'id' => $res->id ?? ''], 200 );
            } catch ( Exception $ex ) {
                return response()->json( ['exception' => $ex->errorInfo ?? $ex->getMessage()], 422 );
            }
        }
    }


    public function show( Request $request, Department $department ) {
        if ( $request->format() == 'html' ) {
            return view( 'layouts.backend_app' );
        }
        $department->load( ['department_qualifications'] );
        return $department;
    }

    public function edit( Department $department ) {
        return view( 'layouts.backend_app' );
    }

    public function update( Request $request, Department $department ) {
        if ( $this->validateCheck( $request, $department->id ) ) {
            try {
                $data = $request->except( 'dept_qualifications', 'checked_qualifications' );

                $department->update( $data );

                if ( $department && !empty( $request->checked_qualifications ) ) {
                    $department->department_qualifications()->delete();
                    $department->department_qualifications()->createMany( $request->checked_qualifications );
                }

                Artisan::call( 'cache:clear' );
                return response()->json( ['message' => 'Update Successfully!'], 200 );
            } catch ( Exception $ex ) {
                return response()->json( ['exception' => $ex->errorInfo ?? $ex->getMessage()], 422 );
            }
        }
    }


    public function destroy( Department $department ) {
        if ( $department->update( ['status' => 'deactive'] ) ) {
            Artisan::call( 'cache:clear' );
            return response()->json( ['message' => 'Delete Successfully!'], 200 );
        } else {
            return response()->json( ['error' => 'Delete Unsuccessfully!'], 200 );
        }
    }


    public function departmentQualifications() {
        $data = AcademicQualification::active()->select( 'id', 'name' )
            ->with(
                'department_qualifications.department:id,name',
                'department_qualifications.academic_class'
            )->get();
        return response()->json( $data );
    }


    public function validateCheck( $request, $id = null ) {
        return true;
        return $request->validate( [
            //ex: 'name' => 'required|email|nullable|date|string|min:0|max:191',
        ], [
            //ex: 'name' => "This name is required" (custom message)
        ] );
    }
}
<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\MasterSetup;

use App\Models\AcademicClassMapping;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterSetup\Institution;
use App\Helpers\GlobalHelper;

class AcademicClassMappingController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,  $institution_id)
    {
        $institution = Institution::with('class_mappings')->find($institution_id);

        $setDefault = [
            'campus_id' => null,
            'shift_id' => null,
            'medium_id' => null,
            'academic_class_id' => null,
            'group_id' => null,
            'section_id' => null,
        ];

        $mappings = $institution->class_mappings->count() ? $institution->class_mappings : [$setDefault];
        return [
            'id'        => $institution->id,
            'name'      => $institution->name,
            'class_mappings' => $mappings,
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('layouts.backend_app');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $institution_id)
    {
        if ($this->validateCheck($request)) {
            try {

                $institution = Institution::find($institution_id);

                // update, delete, insert
                GlobalHelper::updelsert(
                    'institution_id',
                    $institution->id,
                    AcademicClassMapping::class,
                    $request->class_mappings
                );

                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Validate form field.
     *
     * @return \Illuminate\Http\Response
     */
    public function validateCheck($request, $id = null)
    {
        return $request->validate([
            'class_mappings.*.campus_id' => 'required',
            'class_mappings.*.shift_id' => 'required',
            'class_mappings.*.medium_id' => 'required',
            'class_mappings.*.academic_class_id' => 'required',
            'class_mappings.*.group_id' => 'required',
            'class_mappings.*.section_id' => 'required',
        ]);
    }
}

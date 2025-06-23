<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\MasterSetup;

use App\Http\Resources\Resource;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterSetup\Institution;
use App\Traits\ImageUpload;
use Illuminate\Support\Facades\DB;

class InstitutionController extends Controller
{
    use ImageUpload;
    protected $withDateFolder = false;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query  = Institution::with('package')->latest('id');
        $query->whereLike($request->field_name, $request->value);
        $query->whereLike('package_id', $request->package_id);

        if ($request->allData) {
            return Institution::select('id', 'name')->get();
        } else {
            $datas = $query->paginate($request->pagination);
            return new Resource($datas);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('layouts.backend_app');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($this->validateCheck($request)) {
            try {
                DB::beginTransaction();
                $data = $request->all();
                $logo = $request->file('logo');
                $idFront = $request->file('idcard_front_part');
                $idBack = $request->file('idcard_back_part');
                $admit = $request->file('admit_card_image');
                $p_marksheet = $request->file('primary_term_marksheet');
                $s_marksheet = $request->file('secondary_term_marksheet');
                $s_marksheet_c = $request->file('secondary_term_marksheet_combined');
                $admin_admit = $request->file('admin_admit_card');
                $seat_card = $request->file('seat_card');

                $data['logo'] = !empty($logo) ? $this->upload($logo, 'institution_logo') : null;
                $data['idcard_front_part'] = !empty($idFront) ? $this->upload($idFront, 'id_card') : null;
                $data['idcard_back_part'] = !empty($idBack) ? $this->upload($idBack, 'id_card') : null;
                $data['admit_card_image'] = !empty($admit) ? $this->upload($admit, 'admit_card') : null;
                $data['primary_term_marksheet'] = !empty($p_marksheet) ? $this->upload($p_marksheet, 'primary_term_marksheet') : null;
                $data['secondary_term_marksheet'] = !empty($s_marksheet) ? $this->upload($s_marksheet, 'secondary_term_marksheet') : null;
                $data['secondary_term_marksheet_combined'] = !empty($s_marksheet_c) ? $this->upload($s_marksheet_c, 'secondary_term_marksheet') : null;
                $data['admin_admit_card'] = !empty($admin_admit) ? $this->upload($admin_admit, 'admin_admit_card') : null;
                $data['seat_card'] = !empty($seat_card) ? $this->upload($seat_card, 'seat_card') : null;

                $institution = Institution::create($data);

                DB::commit();
                return response()->json(['message' => 'Create Successfully!', 'id' => $institution->id ?? ''], 200);
            } catch (Exception $ex) {
                DB::rollback();
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MasterSetup\Institution  $institution
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Institution $institution)
    {
        return $institution;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MasterSetup\Institution  $institution
     * @return \Illuminate\Http\Response
     */
    public function edit(Institution $institution)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MasterSetup\Institution  $institution
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Institution $institution)
    {
        if ($this->validateCheck($request, $institution->id)) {
            try {
                $data = $request->all();
                $logo = $request->file('logo');
                $idFront = $request->file('idcard_front_part');
                $idBack = $request->file('idcard_back_part');
                $admit = $request->file('admit_card_image');
                $p_marksheet = $request->file('primary_term_marksheet');
                $s_marksheet = $request->file('secondary_term_marksheet');
                $s_marksheet_c = $request->file('secondary_term_marksheet_combined');
                $admin_admit = $request->file('admin_admit_card');
                $seat_card = $request->file('seat_card');

                $data['logo'] = !empty($logo) ? $this->upload($logo, 'institution_logo', $institution->logo) : $this->getOriginalPath($institution->logo);
                $data['idcard_front_part'] = !empty($idFront) ? $this->upload($idFront, 'id_card', $institution->idcard_front_part) : $this->getOriginalPath($institution->idcard_front_part);
                $data['idcard_back_part'] = !empty($idBack) ? $this->upload($idBack, 'id_card', $institution->idcard_back_part) : $this->getOriginalPath($institution->idcard_back_part);
                $data['admit_card_image'] = !empty($admit) ? $this->upload($admit, 'admit', $institution->admit_card_image) : $this->getOriginalPath($institution->admit_card_image);
                $data['primary_term_marksheet'] = !empty($p_marksheet) ? $this->upload($p_marksheet, 'primary_term_marksheet', $institution->primary_term_marksheet) : $this->getOriginalPath($institution->primary_term_marksheet);
                $data['secondary_term_marksheet'] = !empty($s_marksheet) ? $this->upload($s_marksheet, 'secondary_term_marksheet', $institution->secondary_term_marksheet) : $this->getOriginalPath($institution->secondary_term_marksheet);
                $data['secondary_term_marksheet_combined'] = !empty($s_marksheet_c) ? $this->upload($s_marksheet_c, 'secondary_term_marksheet', $institution->secondary_term_marksheet_combined) : $this->getOriginalPath($institution->secondary_term_marksheet_combined);
                $data['admin_admit_card'] = !empty($admin_admit) ? $this->upload($admin_admit, 'admin_admit_card', $institution->admin_admit_card) : $this->getOriginalPath($institution->admin_admit_card);
                $data['seat_card'] = !empty($seat_card) ? $this->upload($seat_card, 'seat_card', $institution->seat_card) : $this->getOriginalPath($institution->seat_card);

                $institution->update($data);
                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MasterSetup\Institution  $institution
     * @return \Illuminate\Http\Response
     */
    public function destroy(Institution $institution)
    {
        if ($institution->delete()) {
            return response()->json(['message' => 'Delete Successfully!'], 200);
        } else {
            return response()->json(['error' => 'Delete Unsuccessfully!'], 200);
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
            'name'          => 'required|string|min:0|max:191',
            'short_name'    => 'required|string|min:0|max:191',
            'logo'          => 'nullable|mimes:jpeg,jpg,png',
            'idcard_front_part' => 'nullable|mimes:jpeg,jpg,png',
            'idcard_back_part'  => 'nullable|mimes:jpeg,jpg,png',
            'primary_term_marksheet' => 'nullable|mimes:jpeg,jpg,png',
            'admit_card_image'  => 'nullable|mimes:jpeg,jpg,png',
            'secondary_term_marksheet'  => 'nullable|mimes:jpeg,jpg,png',
            'secondary_term_marksheet_combined'  => 'nullable|mimes:jpeg,jpg,png',
        ]);
    }
}

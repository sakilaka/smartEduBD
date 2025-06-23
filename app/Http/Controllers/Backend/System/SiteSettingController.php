<?php

/**
 * @Oshit Sutra Dhar
 */

namespace App\Http\Controllers\Backend\System;

use App\Http\Controllers\Controller;
use App\Models\System\SiteSetting;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SiteSettingController extends Controller
{
    use ImageUpload;
    protected $withDateFolder = false;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('layouts.backend_app');
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
        $this->validateCheck($request);

        Cache::forget('site_setting_cache');

        $conf       = SiteSetting::first();
        $data       = $request->all();
        $logo       = $request->file('logo');
        $logo_sm    = $request->file('logo_small');
        $favicon    = $request->file('favicon');

        $data['logo'] = !empty($logo) ? $this->upload($logo, 'config', $conf->logo) : $this->getOriginalPath($conf->logo);
        $data['logo_small'] = !empty($logo_sm) ? $this->upload($logo_sm, 'config', $conf->logo_small) : $this->getOriginalPath($conf->logo_small);
        $data['favicon'] = !empty($favicon) ? $this->upload($favicon, 'config', $conf->favicon) : $this->getOriginalPath($conf->favicon);

        $conf = !empty($conf) ? $conf->update($data) : SiteSetting::create($data);

        return response()->json(['message' => 'Update Successfully!'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\SiteSetting  $SiteSetting
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, SiteSetting $siteSetting)
    {
        return SiteSetting::first();
    }

    /**
     * Validate form field.
     *
     * @return \Illuminate\Http\Response
     */
    public function validateCheck($request, $id = null)
    {
        return $request->validate([
            'title'       => 'required|string|min:0|max:191',
            'short_title' => 'required|string|min:0|max:191',
            'logo'        => 'nullable|mimes:jpeg,jpg,png',
            'logo_small'  => 'nullable|mimes:jpeg,jpg,png',
            'favicon'     => 'nullable|mimes:jpeg,jpg,png'
        ]);
    }
}

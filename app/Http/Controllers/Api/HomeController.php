<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterSetup\Institution;
use Illuminate\Http\Request;
use App\Models\Website\Content\Content;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function content(Request $request)
    {
        $data = Content::select('title', 'description', 'image')
            ->where(['slug' => $request->slug])
            ->first();

        return $this->sendResponse($data);
    }

    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function notices(Request $request)
    {
        $insID = auth()->user()->students()->distinct('institution_id')->pluck('institution_id');

        $data = Institution::select('id', 'name')
            ->with(['notices' => function ($q) {
                $q->select('institution_id', 'slug', 'date', 'title', 'description', 'file_type', 'image')->limit(8);
            }])
            ->whereIn('id', $insID)
            ->where('status', 'active')
            ->get();

        return $this->sendResponse($data);
    }
}
<?php

namespace App\Http\Controllers\Backend\System;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use App\Traits\Lib\DynamicDataTrait;
use App\Traits\Lib\CustomDataTrait;
use Illuminate\Support\Facades\Auth;

class LibController extends Controller
{
    use DynamicDataTrait, CustomDataTrait;

    /**
     * SYSTEMS DATA RETURN
     */
    public function systems()
    {
        try {
            return [
                "global"      => $this->index(),
                "profile"     => Auth::guard('admin')->user()->profile ?? '',
                "permissions" => App::make('premitedMenuArr'),
                "site"        => App::make('siteSettingObj'),
                "menus"       => App::make('sideMenus'),
            ];
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }


    private function index()
    {
        $data = [];
        $data += $this->customData();
        $data += $this->commonCustomData();
        $data += $this->dynamicData();
        $data += $this->commonDynamicData();
        return $data;
    }
}

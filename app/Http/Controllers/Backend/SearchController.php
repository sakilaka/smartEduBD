<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\System\Menu;
use Illuminate\Http\Request;

class SearchController extends Controller {

    public function index( Request $request ) {

        $menus = Menu::where( 'menu_name', 'LIKE', "%{$request->key}%" )
            ->whereNotNull( 'route_name' )->get();
        return response()->json( $menus );
    }
}

<?php

namespace App\Http\Controllers\Backend\System;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\System\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request ) {
        $query = Menu::with( 'parent' )->latest();
        $query->whereLike( $request->field_name, $request->value );

        $datas = $query->paginate( $request->pagination );
        return new Resource( $datas );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view( 'layouts.backend_app' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request ) {
        $this->validateCheck( $request );
        Cache::forget( 'side_menu_cache' );
        $res = Menu::create( $request->all() );
        return response()->json( ['message' => 'Create Successfully!', 'id' => $res->id ?? ''], 200 );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Menu  $Menu
     * @return \Illuminate\Http\Response
     */
    public function show( Request $request, Menu $menu ) {
        if ( $request->format() == 'html' ) {
            return view( 'layouts.backend_app' );
        }
        return $menu;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function edit( Menu $menu ) {
        return view( 'layouts.backend_app' );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Menu  $Menu
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, Menu $menu ) {
        $this->validateCheck( $request );
        Cache::forget( 'side_menu_cache' );
        $menu->update( $request->all() );
        return response()->json( ['message' => 'Update Successfully!'], 200 );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Menu  $Menu
     * @return \Illuminate\Http\Response
     */
    public function destroy( Menu $menu ) {
        Cache::forget( 'side_menu_cache' );
        if ( $menu->delete() ) {
            return response()->json( ['message' => 'Delete Successfully!'], 200 );
        } else {
            return response()->json( ['message' => 'Delete Unsuccessfully!'], 200 );
        }
    }
    public function menus( Request $request, $data = null ) {
        return Menu::getMenuList();
    }

    public function dashboardMenu( Request $request ) {
        // return Menu::getMenuList();
    }

    /**
     * Validate form field.
     *
     * @return \Illuminate\Http\Response
     */
    public function validateCheck( $request, $id = null ) {
        return $request->validate( [
            'menu_name' => 'required|string|min:0|max:191',
            'sorting'   => 'required',
        ] );
    }
}

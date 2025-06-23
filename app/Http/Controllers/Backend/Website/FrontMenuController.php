<?php

/**
 * @Oshit Sutra Dhar
 */

namespace App\Http\Controllers\Backend\Website;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\Website\FrontMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FrontMenuController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request ) {
        $query = FrontMenu::with( 'content', 'parent' )->latest();
        $query->whereAny( 'type', $request->type );
        $query->whereAny( 'sorting', $request->sorting );
        $query->whereAny( 'position', $request->position );
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
        Cache::forget( 'front_menu_cache' );
        $this->validateCheck( $request );
        $res = FrontMenu::create( $request->all() );
        return response()->json( ['message' => 'Create Successfully!', "id" => $res->slug], 200 );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function show( Request $request, $slug ) {
        if ( $request->format() == 'html' ) {
            return view( 'layouts.backend_app' );
        }
        return FrontMenu::with( 'content' )->where( 'slug', $slug )->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function edit( $id ) {
        return view( 'layouts.backend_app' );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, FrontMenu $frontMenu ) {
        Cache::forget( 'front_menu_cache' );
        $this->validateCheck( $request );
        $frontMenu->update( $request->all() );
        return response()->json( ['message' => 'Update Successfully!'], 200 );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy( FrontMenu $frontMenu ) {
        Cache::forget( 'front_menu_cache' );
        if ( $frontMenu->delete() ) {
            return response()->json( ['message' => 'Delete Successfully!'], 200 );
        } else {
            return response()->json( ['message' => 'Delete Unsuccessfully!'], 200 );
        }
    }

    /**
     * get parent menus.
     */
    public function getParentMenu() {
        return FrontMenu::getMenuList();
    }

    /**
     * Validate form field.
     *
     * @return \Illuminate\Http\Response
     */
    public function validateCheck( $request, $id = null ) {
        return $request->validate( [
            'sorting'  => 'required',
            'status'   => 'required',
            'position' => 'required',
            'title'    => 'required|string|min:0|max:191',
        ] );
    }
}

<?php

/**
 * @Oshit Sutra Dhar
 */

namespace App\Http\Controllers\Backend\Website\Gallery;

use App\Http\Controllers\Base\BaseController;
use App\Http\Resources\Resource;
use App\Models\Website\Gallery\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlbumController extends BaseController {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request ) {
        $query = Album::oldest( 'sorting' );
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
        $data = $request->all();
        if ( !empty( $request->image ) ) {
            $data['image'] = $this->upload( $request->image, 'album', null, true );
        }

        $res = Album::create( $data );
        return response()->json( ['message' => 'Create Successfully!', "id" => $res->slug], 200 );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function show( Request $request, Album $album ) {
        if ( $request->format() == 'html' ) {
            return view( 'layouts.backend_app' );
        }
        return $album;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function edit( Album $album ) {
        return view( 'layouts.backend_app' );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, Album $album ) {
        $data = $request->all();

        if ( !empty( $request->image ) && $request->image != $album->image ) {
            $data['image'] = $this->upload( $request->image, 'album', $album->image, true );
        } else {
            $data['image'] = $this->oldFile( $album->image );
        }

        $album->update( $data );
        return response()->json( ['message' => 'Update Successfully!'], 200 );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function destroy( Album $album ) {
        $this->oldFile( $album->image, true );
        if ( $album->delete() ) {
            return response()->json( ['message' => 'Delete Successfully!'], 200 );
        } else {
            return response()->json( ['message' => 'Delete Unsuccessfully!'], 200 );
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function album( $type ) {
        return Album::oldest( 'sorting' )->where( 'type', $type )->pluck( 'name', 'id' );
    }

    /**
     * Validate form field.
     *
     * @return \Illuminate\Http\Response
     */
    public function validateCheck( $request ) {
        return $request->validate( [
            'name' => 'required',
        ] );
    }
}

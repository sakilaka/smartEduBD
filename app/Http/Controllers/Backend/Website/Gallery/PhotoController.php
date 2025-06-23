<?php

/**
 * @Oshit Sutra Dhar
 */

namespace App\Http\Controllers\Backend\Website\Gallery;

use App\Http\Controllers\Base\BaseController;
use App\Http\Resources\Resource;
use App\Models\Website\Gallery\Album;
use App\Models\Website\Gallery\Photo;
use App\Traits\ResizeTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PhotoController extends BaseController {

    use ResizeTrait;

    protected $resizeArr = [
        ["width" => 300, "height" => 200],
        ["width" => 600, "height" => 400],
    ];
    protected $resize_type = "perfect";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request ) {
        $query = Photo::with( 'album' )->oldest( 'sorting' );
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
        Cache::forget( 'website_cache' );
        if ( $this->validateCheck( $request ) ) {
            $data = $this->uploadImages( $request );

            // If any error occured
            if ( !empty( $data['errors'] ) ) {
                return response()->json( ['exception' => $data['errors']], 422 );
            }

            $res = Photo::insert( $data );
            return response()->json( ['message' => 'Create Successfully!'], 200 );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    private function uploadImages( $request ) {
        $info  = $request->uploaded_images;
        $files = $request->file( 'files' );
        $data  = [];

        $album = Album::find( $request->album_id );

        foreach ( $files ?? [] as $key => $file ) {

            /**
             * $file
             * $nested_folder = ["photos","album_name"]
             * original_image = bool
             * resize_image = bool
             */
            $upload = $this->resizer( $file, ["photos", $album->name], false, true );

            //if any error occured
            if ( !empty( $upload['errors'] ) ) {
                return $upload;
            }

            if ( !empty( $upload ) ) {
                $arr = [
                    'sorting'  => $info[$key]['sorting'] ?? 0,
                    'title'    => $info[$key]['title'] ?? null,
                    'album_id' => $request->album_id,
                    'images'   => json_encode( $upload ),
                ];
                array_push( $data, $arr );
            }
        }
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function show( Request $request, Photo $photo ) {
        if ( $request->format() == 'html' ) {
            return view( 'layouts.backend_app' );
        }
        return $photo;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function edit( Photo $photo ) {
        return view( 'layouts.backend_app' );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, Photo $photo ) {
        Cache::forget( 'website_cache' );
        $data  = $request->all();
        $file  = $request->file( 'image' );
        $album = Album::find( $request->album_id );

        if ( !empty( $file ) ) {
            $images         = $this->resizer( $file, ["photos", $album->name], false, true );
            $data['images'] = json_encode( $images );
        } else {
            $data['images'] = json_encode( $photo->images );
        }

        $photo->update( $data );
        return response()->json( ['message' => 'Update Successfully!'], 200 );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function destroy( Photo $photo ) {
        Cache::forget( 'website_cache' );

        // Image Delete
        // foreach ( $photo->images as $key => $file ) {
        //     if ( Storage::disk( 'public' )->exists( $file ) ) {
        //         Storage::delete( $file );
        //     }
        // }

        if ( $photo->delete() ) {
            return response()->json( ['message' => 'Delete Successfully!'], 200 );
        } else {
            return response()->json( ['message' => 'Delete Unsuccessfully!'], 200 );
        }
    }

    /**
     * Validate form field.
     *
     * @return \Illuminate\Http\Response
     */
    public function validateCheck( $request ) {
        return $request->validate( [
            'album_id' => 'required',
        ] );
    }
}

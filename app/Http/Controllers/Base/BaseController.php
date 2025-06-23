<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class BaseController extends Controller {
    /*-----IMAGE UPLOAD-----*/
    protected function upload( $file, $path, $old = null, $base64 = false ) {
        $code = date( 'ymdhis' ) . '-' . rand( 1111, 9999 );

        /*-------DELETE OLD IMAGE-------*/
        if ( !empty( $old ) ) {
            $oldFile = $this->oldFile( $old );
            if ( Storage::disk( 'public' )->exists( $oldFile ) ):
                Storage::delete( $oldFile );
            endif;
        }

        /*-------FILE/IMAGE UPLOAD-------*/
        if ( !empty( $file ) && !$base64 ) {
            $fileName = $code . $file->getClientOriginalName();
            return Storage::putFileAs( 'upload/' . $path, $file, $fileName );
        }

        /*-------base64 IMAGE UPLOAD-------*/
        if ( !empty( $file ) && $base64 ) {
            $image     = str_replace( 'data:image/jpeg;base64,', '', $file );
            $image     = str_replace( ' ', '+', $image );
            $imageName = 'upload/' . $path . '/' . $code . '.jpeg';
            Storage::disk( 'public' )->put( $imageName, base64_decode( $image ) );
            return $imageName;
        }
    }

    /*-----OLD IMAGE-----*/
    public function oldFile( $file, $isDelete = false ) {
        $ex      = explode( 'storage/', $file );
        $oldFile = $ex[1] ?? "";

        if ( $isDelete && $oldFile ) {
            if ( Storage::disk( 'public' )->exists( $oldFile ) ) {
                Storage::delete( $oldFile );
            }
        }
        return $oldFile;
    }

    /*-----Folder Create-----*/
    public function makeDir( $folder, $subfolder = null ) {
        $main_dir = storage_path( "app/public/upload/{$folder}" );
        if ( $subfolder ) {
            $main_dir = storage_path( "app/public/upload/{$folder}/{$subfolder}" );
        }

        if ( !file_exists( $main_dir ) ) {
            mkdir( $main_dir, 0777, true );
        }
    }
}

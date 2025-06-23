<?php

namespace App\Http\Controllers\Backend\System;

use App\Helpers\Module;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

class ModuleController extends Controller {
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( Request $request ) {
        Session::put( 'model', $request->model_name );
        $model = Session::get( 'model' );

        if ( $request->format() == 'html' ) {
            return view( 'layouts.backend_app' );
        }
        if ( $request->isMethod( 'get' ) ) {
            return Module::folderPath( $model ?? 'Test' );
        }
        if ( $request->isMethod( 'post' ) ) {
            return $this->store( $request );
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkModel( Request $request ) {
        return Module::check( $request );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request ) {
        $model      = $request->model_name ?? "";
        $only_model = $request->only_model ?? "";

        if ( $this->validateCheck( $request ) ) {
            $ex = str_split( $model );
            if ( $ex[0] == ucfirst( $ex[0] ) ) {
                if ( Module::createFile( $model, $only_model ) ) {
                    return response()->json( ['message' => 'Module Create Successfully'], 200 );
                } else {
                    return response()->json( ['error' => 'Something went wrong, but Some file are crated, please check'], 200 );
                }
            } else {
                return response()->json( ['warning' => 'First Letter must be capital'], 200 );
            }
        }
    }

    /*--------------------------------------------
     * VALIDATION
     *-------------------------------------------*/
    public function validateCheck( $request ) {
        return $request->validate( [
            'model_name' => 'required|alpha|max:255|regex:/^\S*$/u',
        ] );
    }
}

<?php

/**
 * @Oshit Sutra Dhar
 */

namespace App\Models\System;

use Auth;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model {

    protected $guarded = ['id'];
    public $timestamps = false;

    // RELATION WITH ROLE
    public function role() {
        return $this->belongsTo( Role::class );
    }
    // RELATION WITH PERMISSION
    public function permission() {
        return $this->belongsTo( Permission::class );
    }

    // GET PERMISSIONS
    public static function permissions() {
        return RolePermission::select( 'permission_id', 'role_id' )
            ->with( ['permission' => function ( $q ) {
                $q->select( 'id', 'name', 'route', 'parent_id' );
            }] )->get()->groupBy( 'role_id' );
    }

    // PERMISSION PROCESS
    public static function permissionProcess( $obj ) {
        $routes          = [];
        $rolePermissions = $obj->get( Auth::guard( 'admin' )->user()->role_id );
        if ( $rolePermissions ) {
            foreach ( $rolePermissions->toArray() as $value ) {
                if ( !empty( $value['permission']['parent_id'] ) ) {
                    $routes[] = $value['permission']['route'];
                }

            }
        }
        return $routes;
    }
}

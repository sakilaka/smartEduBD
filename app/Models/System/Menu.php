<?php

/**
 * @Oshit Sutra Dhar
 */

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class Menu extends Model {
    protected $guarded = ['id'];

    //RELATIONSHIP
    public function parent() {
        return $this->belongsTo( Menu::class );
    }
    public function childs() {
        return $this->hasMany( Menu::class, 'parent_id', 'id' )->oldest( 'sorting' );
    }
    public function childMenus() {
        return $this->childs()->with( 'childMenus' );
    }

    // ADMIN SIDE MENUS
    public static function menus() {
        $premitedMenuArr = App::make('premitedMenuArr');


        $menus = Menu::whereNull('parent_id')
            ->with([
                'childMenus' => function ($query) use ($premitedMenuArr) {
                    $query->whereIn('route_name', $premitedMenuArr);
                }
            ])
            ->oldest('sorting')
            ->get()
            ->toArray();


        return $menus;
    }
    // FOR SELECT MENU
    public static function getMenuList() {
        $parent = Menu::with('childs' )->where( 'parent_id', Null )
            ->oldest( 'sorting' )->get();
        $menus = Menu::recursiveMenuList( $parent );
        return $menus;
    }
    public static function recursiveMenuList( $datas ) {
        static $desiglistlist = [];
        static $level         = 0;
        $level++;
        if ( !empty( $datas ) ) {
            foreach ( $datas as $desiglist ) {
                if ( !empty( $desiglist['childs'] ) ) {
                    $desiglistlist[$desiglist['id']] = str_repeat( '|__', $level - 1 ) . $desiglist['menu_name'];
                } else {
                    $desiglistlist[$desiglist['id']] = str_repeat( '|__', $level - 1 ) . $desiglist['menu_name'];
                }
                Menu::recursiveMenuList( $desiglist['childs'] );
            }
        }
        $level--;
        return $desiglistlist;
    }
}

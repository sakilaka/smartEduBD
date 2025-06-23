<?php

namespace App\Providers;

use App\Models\System\Menu;
use App\Models\System\RolePermission;
use App\Models\System\SiteSetting;
use App\Models\Website\FrontMenu;
use App\Models\Website\Gallery\Photo;
use App\Models\Website\Gallery\Slider;
use App\Models\Website\Gallery\Video;
use App\Models\Website\News;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    protected $defer = true;
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        /*-----SITE SETTINGS-----*/
        $this->app->singleton('siteSettingObj', function ($app) {
            return Cache::rememberForever('site_setting_cache', function () {
                $site = SiteSetting::first();
                return !empty($site) ? $site->toArray() : [];
            });
        });

        /*-----LEFT MENUS-----*/
        $this->app->singleton('sideMenus', function ($app) {
            return Menu::menus();
        });

        /*-----PERMISSION ALL PROCESS-----*/
        $this->app->singleton('premitedMenuArr', function ($app) {
            if (Auth::check()) {
                $obj = RolePermission::permissions();
                if ($obj->count()) {
                    $allIndexes = RolePermission::permissionProcess($obj);
                }
            }
            return $allIndexes ?? [];
        });

        /*-----FRONT MENUS-----*/
        $this->app->singleton('frontMenuObj', function ($app) {
            return Cache::rememberForever('front_menu_cache', function () {
                return FrontMenu::getMenus();
            });
        });

        /*-----FRONT MENUS-----*/
        $this->app->singleton('websiteObj', function ($app) {
            return Cache::rememberForever('website_cache', function () {
                return [
                    'sliders' => Slider::latest('sorting')->limit(10)->get()->toArray(),
                    'photos'  => Photo::latest('sorting')->limit(3)->get()->toArray(),
                    'videos'  => Video::latest('sorting')->limit(3)->get()->toArray(),
                    'news'    => News::latest('sorting')->limit(6)->get()->toArray(),
                ];
            });
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    { }
}

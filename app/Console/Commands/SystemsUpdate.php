<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\System\Permission;
use App\Models\System\Role;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

class SystemsUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'systems:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Systems update successful.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Cache::forget('role_pemission_cache');
        $this->createRolePermission();

        $permissions = Permission::pluck('id');
        $role        = Role::find(1);
        $role->permissions()->sync($permissions);
        Artisan::call('optimize:clear');

        $this->info("Systems update successful.");
    }

    // PERMISSION CREATE FOR ROLE
    private function createRolePermission()
    {
        $allMenuListInserted = App::make('premitedMenuArr');
        $allRoutes = Route::getRoutes();
        $controllers = [];

        foreach ($allRoutes as $route) {
            $action = $route->getAction();
            if (is_array($action['middleware']) && in_array('auth.access', $action['middleware'])) {
                $route                = explode('.', $action['as']);
                $controllerActionName = class_basename($action['controller']);

                if (!in_array($controllerActionName, $allMenuListInserted)) {
                    $controllerAction                                        = explode('@', $controllerActionName);
                    $controllers[$controllerAction[0]][$controllerAction[1]] = $route[0];
                }
            }
        }

        foreach ($controllers as $key => $controller) {
            $data['name'] = $key;
            $parent       = Permission::firstOrCreate($data);
            if ($parent) {
                $data2['parent_id'] = $parent->id;
                foreach ($controller as $process => $route) {
                    $data2['name']  = $process;
                    $data2['route'] = $route . '.' . $process;
                    Permission::firstOrCreate($data2);
                }
            }
        }
    }
}

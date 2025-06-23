<?php

namespace App\Http\Controllers\Backend\System;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\System\Permission;
use App\Models\System\Role;
use App\Models\System\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Role::active()->where('id', '!=', 1)->latest('id');
        $query->whereLike($request->field_name, $request->value);
        if ($request->allData) {
            return $query->select('name', 'id')->get();
        } else {
            $datas = $query->paginate($request->pagination);
            return new Resource($datas);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('layouts.backend_app');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:100|unique:roles',
            'status' => 'required',
        ]);
        $data = $request->all();

        $permissions = $request->get('permissions');
        unset($data['permissions']);

        $Role = Role::create($data);

        if ($Role) {
            if ($permissions && count($permissions)) {
                $rolePermissions = $Role->permissions()->sync($permissions);
                if ($rolePermissions) {
                    Cache::forget('side_menu_cache');
                    Cache::forget('role_pemission_cache');
                    return response()->json(['message' => 'You have successfully created', 'id' => $Role->id ?? ''], 200);
                } else {
                    return response()->json(['message' => 'You have successfully created role but no permission set', 'id' => $Role->id ?? ''], 200);
                }
            } else {
                return response()->json(['message' => 'You have successfully created role but no permission set', 'id' => $Role->id ?? ''], 200);
            }
        } else {
            return response()->json(['error' => 'Opps! Something wrong. Please try again.'], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Role $role)
    {
        $data = Role::where('id', '!=', 1)->find($role->id);
        $data['permissions'] = $this->getAccessPermissions($role->id);
        return $data;
    }

    // GET PERMISSIONS
    public function getPermissions()
    {
        /*create permission*/
        $this->createRolePermission();
        return Permission::with('children')->whereNull('parent_id')->get();
    }

    // GET ACCESS PERMISSIONS
    public function getAccessPermissions($id)
    {
        return RolePermission::where('role_id', $id)->where('role_id', '!=', 1)->pluck('permission_id')->toArray();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'   => 'required|string|max:55|unique:roles,name,' . $role->id,
            'status' => 'required',
        ]);
        $data = $request->all();

        $permissions = $request->get('permissions');
        unset($data['permissions']);

        $role->fill($data)->save();
        if ($role) {
            $role->permissions()->sync($permissions);
            Artisan::call('cache:clear');

            return response()->json(['message' => 'You have successfully updated'], 200);
        } else {
            return response()->json(['error' => 'Opps! Something wrong. Please try again.'], 200);
        }
    }

    // SYSTEM ROLE UPDATE
    public function systemsRoleUpdate()
    {
        Cache::forget('role_pemission_cache');
        $this->createRolePermission();

        /*===Administrator===*/
        $permissions = Permission::pluck('id');
        $role        = Role::find(1);
        $role->permissions()->sync($permissions);
        Artisan::call('optimize:clear');
        /*===Administrator===*/
        return response()->json(['message' => 'Successfully Updated'], 200);
    }
    // PERMISSION CREATE FOR ROLE
    private function createRolePermission()
    {
        $allMenuListInserted = App::make('premitedMenuArr');
        // dd($allMenuListInserted);
        $allRoutes = Route::getRoutes();
        // dd($allRoutes);
        $controllers = [];
        foreach ($allRoutes as $route) {
            $action = $route->getAction();
            if (is_array($action['middleware']) && in_array('auth.access', $action['middleware'])) {
                $route                = explode('.', $action['as']);
                $controllerActionName = class_basename($action['controller']);
                // dd($controllerActionName);

                if (!in_array($controllerActionName, $allMenuListInserted)) {
                    $controllerAction                                        = explode('@', $controllerActionName);
                    $controllers[$controllerAction[0]][$controllerAction[1]] = $route[0];
                }
            }
        }
        // dd($controllers);

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        if ($role->id == 1) {
            return response()->json(['error' => 'You cannot delete this role!'], 200);
        }

        RolePermission::where('role_id', $role->id)->delete();
        Cache::forget('role_pemission_cache');
        if ($role->delete()) {
            return response()->json(['message' => 'Delete Successfully!'], 200);
        } else {
            return response()->json(['error' => 'Delete Unsuccessfully!'], 200);
        }
    }
}

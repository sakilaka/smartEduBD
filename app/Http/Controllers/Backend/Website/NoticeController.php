<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\Website;

use App\Http\Controllers\Base\BaseController;
use App\Http\Resources\Resource;
use App\Models\Admin;
use App\Models\MasterSetup\Institution;
use App\Models\Website\Notice;
use App\Models\Website\NoticeAssignable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NoticeController extends BaseController
{
    // use PushNotifyTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $class_id = $request->academic_class_id;
        $institution_id = Institution::current();

        $query = Notice::latest('id')->with('institution');
        $query->whereLike($request->field_name, $request->value);

        if (!empty($class_id)) {
            $query->whereSub('assignables', 'academic_class_id', $class_id);
            $query->orWhere('all_class', 1);
            $query->orWhere('notice_from', $class_id);
        }

        $query->whereAny('institution_id', $institution_id);
        $query->whereAny('type', $request->type);

        $datas = $query->paginate($request->pagination);
        return new Resource($datas);
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
        if ($this->validateCheck($request)) {
            try {
                $data        = $request->all();
                $assignables = json_decode($request->assignables, true);
                $admin       = Admin::find(auth()->guard('admin')->id());

                $data['notice_from'] = $admin->department->id ?? null;

                if (!empty($request->image)) {
                    $data['image'] = $this->upload($request->image, 'notices', null);
                }
                $res = Notice::create($data);

                // Mobile Push Notify
                if (!empty($res)) {
                    // assign department
                    if (empty($request->all_class)) {
                        $res->assignables()->createMany($assignables);
                    }

                    // Push Notify
                    // if ($res->type != 'office') {
                    //     $this->pushNotify($data, $assignables);
                    // }
                }
                return response()->json(['message' => 'Create Successfully!', 'id' => $res->id ?? ''], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Notice $notice)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }

        $data                = $notice;
        $data['assignables'] = $this->getAssignedIds($notice->id);
        return $data;
    }

    // GET QUALIFICATIONS IDS
    public function getAssignedIds($id)
    {
        return NoticeAssignable::where('notice_id', $id)->select('academic_class_id')->get()->toArray();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function edit(Notice $notice)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notice $notice)
    {
        if ($this->validateCheck($request, $notice->id)) {
            try {
                $data        = $request->all();
                $assignables = json_decode($request->assignables, true);

                if (!empty($request->image) && $request->image != $notice->image) {
                    $data['image'] = $this->upload($request->image, 'notices', $notice->image);
                } else {
                    $data['image'] = $this->oldFile($notice->image);
                }
                $notice->update($data);

                if ($notice) {
                    $notice->assignables()->delete();
                    $notice->assignables()->createMany($assignables);
                }

                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notice $notice)
    {
        $old = $this->oldFile($notice->image);
        if (Storage::disk('public')->exists($old)) {
            Storage::delete($old);
        }

        if ($notice->delete()) {
            return response()->json(['message' => 'Delete Successfully!'], 200);
        } else {
            return response()->json(['error' => 'Delete Unsuccessfully!'], 200);
        }
    }

    /**
     * Office Order
     *
     * @return \Illuminate\Http\Response
     */
    public function officeOrder(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Validate form field.
     *
     * @return \Illuminate\Http\Response
     */
    public function validateCheck($request, $id = null)
    {
        return true;
        return $request->validate([
            //ex: 'name' => 'required|email|nullable|date|string|min:0|max:191',
        ], [
            //ex: 'name' => "This name is required" (custom message)
        ]);
    }
}

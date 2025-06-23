<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Notification::latest();
        $query->whereLike($request->field_name, $request->value);
        $query->whereAny('status', $request->status);

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
                Notification::create($request->all());
                return response()->json(['message' => 'Create Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Notification $notification)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
        $notify = Notification::find($notification->id);
        $notification->update(['status' => 'r']);

        return $notify;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function edit(Notification $notification)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notification $notification)
    {
        if ($this->validateCheck($request, $notification->id)) {
            try {
                $notification->update($request->all());
                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        if ($notification->delete()) {
            return response()->json(['message' => 'Delete Successfully!'], 200);
        } else {
            return response()->json(['error' => 'Delete Unsuccessfully!'], 200);
        }
    }

    /**
     * Get Notificaitons for Header
     *
     * @return \Illuminate\Http\Response
     */
    public function notifications()
    {
        $data['notifications'] = Notification::where('status', 'ur')->latest('id')->limit(15)->get();
        $data['count']         = Notification::where('status', 'ur')->count();
        return response()->json($data);
    }

    /**
     * Read All Notificaitons
     *
     * @return \Illuminate\Http\Response
     */
    public function readNotifications()
    {
        Notification::where('status', 'ur')->update(['status' => 'r']);
        return response()->json(['message' => 'Mark all as read successfully!'], 200);
    }
}

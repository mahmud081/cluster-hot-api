<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Group;
use App\Models\GroupDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $group_list = auth()->user()->groups;

        return response()->json([
            'success' => true,
            'message' => $group_list,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray(),
            ]);
        }

        $name = $request->name;
        $user_id = auth()->id();
        $group = Group::create([
            'user_id' => $user_id,
            'name' => $name,
        ]);

        if (isset($request->devices) && (count($request->devices) > 0)) {
            $group_id = $group->id;

            foreach ($request->devices as $device_id) {
                $device = Device::find($device_id);
                if (!is_null($device)) {
                    $device_user_id = $device->user_id;

                    if ($device_user_id == $user_id) {
                        $group_device = GroupDevice::create([
                            'user_id' => $user_id,
                            'group_id' => $group_id,
                            'device_id' => $device_id,
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => $group,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $user_id = $group->user_id;

        if ($user_id == auth()->id()) {
            $devices = $group->devices;

            return response()->json(['success' => true, 'message' => $group]);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray(),
            ]);
        }

        $name = $request->name;
        $user_id = auth()->id();
        $group_user_id = $group->user_id;

        if ($user_id == $group_user_id) {
            if (isset($request->add_devices) && (count($request->add_devices) > 0)) {
                foreach ($request->add_devices as $device_id) {
                    $device = Device::find($device_id);
                    if (!is_null($device)) {
                        $device_user_id = $device->user_id;
                        $group_device_check = GroupDevice::where(['group_id' => $group->id, 'device_id' => $device_id])->first();
                        if (($device_user_id == $user_id) && is_null($group_device_check)) {
                            $group_device = GroupDevice::create([
                                'user_id' => $user_id,
                                'group_id' => $group->id,
                                'device_id' => $device_id,
                            ]);
                        }
                    }
                }
            }

            if (isset($request->remove_devices) && (count($request->remove_devices) > 0)) {
                foreach ($request->remove_devices as $device_id) {
                    $device = Device::find($device_id);
                    if (!is_null($device)) {
                        $device_user_id = $device->user_id;
                        $group_device_check = GroupDevice::where(['group_id' => $group->id, 'device_id' => $device_id])->first();
                        if (($device_user_id == $user_id) && !is_null($group_device_check)) {
                            $group_device_check->delete();
                        }
                    }
                }
            }
            try {
                $group->name = $name;
                $group->save();

                return response()->json(['success' => true, 'message' => $group]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $user_id = $group->user_id;

        if ($user_id == auth()->id()) {
            try {
                $group->delete();

                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }
}

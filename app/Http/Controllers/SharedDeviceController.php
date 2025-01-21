<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Invitation;
use App\Models\Property;
use App\Models\Room;
use App\Models\SharedDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SharedDeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function shareProperty(Property $property, User $user, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ]);
        }

        $sharer_id = auth()->id();
        $sharee_id = $user->id;
        $role = $request->role;
        $property_user_id = $property->user_id;
        if (($property_user_id == $sharer_id) && (Invitation::where(['sharer_id' => $sharer_id, 'sharee_id' => $sharee_id, 'accepted' => 1])->exists())) {
            $devices = $property->devices;

            $response_message = [];
            //$response_message[] = $devices;
            foreach ($devices as $device) {
                $device_id = $device->id;
                if (!SharedDevice::where(['device_id' => $device_id, 'sharee_id' => $sharee_id])->exists()) {
                    $shared_device = SharedDevice::create([
                        'sharer_id' => $sharer_id,
                        'sharee_id' => $sharee_id,
                        'device_id' => $device_id,
                        'role' => $role
                    ]);

                    $response_message[] = $shared_device;
                }
            }

            return response()->json([
                'success' => true,
                'message' => $response_message,
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function shareRoom(Room $room, User $user, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ]);
        }

        $sharer_id = auth()->user()->id;
        $sharee_id = $user->id;
        $role = $request->role;
        $room_user_id = $room->user_id;
        if (($room_user_id == $sharer_id) && (Invitation::where(['sharer_id' => $sharer_id, 'sharee_id' => $sharee_id, 'accepted' => 1])->exists())) {
            $devices = $room->devices;

            $response_message = [];
            //$response_message[] = $devices;
            foreach ($devices as $device) {
                $device_id = $device->id;
                if (!SharedDevice::where(['device_id' => $device_id, 'sharee_id' => $sharee_id])->exists()) {
                    $shared_device = SharedDevice::create([
                        'sharer_id' => $sharer_id,
                        'sharee_id' => $sharee_id,
                        'device_id' => $device_id,
                        'role' => $role
                    ]);

                    $response_message[] = $shared_device;
                }
            }

            return response()->json([
                'success' => true,
                'message' => $response_message,
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function shareDevice(Device $device, User $user, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ]);
        }

        $sharer_id = auth()->user()->id;
        $sharee_id = $user->id;
        $role = $request->role;
        $device_user_id = $device->user_id;
        if (($device_user_id == $sharer_id) && (Invitation::where(['sharer_id' => $sharer_id, 'sharee_id' => $sharee_id, 'accepted' => 1])->exists())) {
            $device_id = $device->id;

            if (!SharedDevice::where(['device_id' => $device_id, 'sharee_id' => $sharee_id])->exists()) {
                $shared_device = SharedDevice::create([
                    'sharer_id' => $sharer_id,
                    'sharee_id' => $sharee_id,
                    'device_id' => $device_id,
                    'role' => $role
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $shared_device,
                ], 201);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function updateSharedProperty(Property $property, User $user, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ]);
        }

        $sharer_id = auth()->user()->id;
        $sharee_id = $user->id;
        $role = $request->role;
        $property_user_id = $property->user_id;
        if (($property_user_id == $sharer_id)/*  && (Invitation::where(['sharer_id' => $sharer_id, 'sharee_id' => $sharee_id, 'accepted' => 1])->exists()) */) {
            $devices = $property->devices;
            $response_message = [];
            foreach ($devices as $device) {
                $device_id = $device->id;
                $shared_device = SharedDevice::where(
                    [
                        'sharer_id' => $sharer_id,
                        'sharee_id' => $sharee_id,
                        'device_id' => $device_id,
                    ]
                )->first();

                if (!is_null($shared_device)) {
                    $shared_device->role = $role;
                    $shared_device->save();
                    $response_message[] = $shared_device;
                }
            }

            return response()->json([
                'success' => true,
                'message' => $response_message,
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function updateSharedRoom(Room $room, User $user, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ]);
        }

        $sharer_id = auth()->user()->id;
        $sharee_id = $user->id;
        $role = $request->role;
        $room_user_id = $room->user_id;
        if (($room_user_id == $sharer_id) /* && (Invitation::where(['sharer_id' => $sharer_id, 'sharee_id' => $sharee_id, 'accepted' => 1])->exists()) */) {
            $devices = $room->devices;

            $response_message = [];
            //$response_message[] = $devices;
            foreach ($devices as $device) {
                $device_id = $device->id;
                $shared_device = SharedDevice::where(
                    [
                        'sharer_id' => $sharer_id,
                        'sharee_id' => $sharee_id,
                        'device_id' => $device_id,
                    ]
                )->first();

                if (!is_null($shared_device)) {
                    $shared_device->role = $role;
                    $shared_device->save();
                    $response_message[] = $shared_device;
                }
            }

            return response()->json([
                'success' => true,
                'message' => $response_message,
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function updateSharedDevice(Device $device, User $user, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ]);
        }

        $sharer_id = auth()->user()->id;
        $sharee_id = $user->id;
        $role = $request->role;
        $device_user_id = $device->user_id;
        if (($device_user_id == $sharer_id) /* && (Invitation::where(['sharer_id' => $sharer_id, 'sharee_id' => $sharee_id, 'accepted' => 1])->exists()) */) {
            $device_id = $device->id;

            $shared_device = SharedDevice::where(
                [
                    'sharer_id' => $sharer_id,
                    'sharee_id' => $sharee_id,
                    'device_id' => $device_id,
                ]
            )->first();

            if (!is_null($shared_device)) {
                $shared_device->role = $role;
                $shared_device->save();

                return response()->json([
                    'success' => true,
                    'message' => $shared_device,
                ], 201);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function deleteSharedProperty(Property $property, User $user)
    {
        $logged_in_user_id = auth()->user()->id;
        $sharee_id = $user->id;
        if ($logged_in_user_id == $sharee_id) {
            $devices = $property->devices;
            foreach ($devices as $device) {
                $device_id = $device->id;
                $shared_device = SharedDevice::where(
                    [
                        'sharee_id' => $sharee_id,
                        'device_id' => $device_id,
                    ]
                )->first();

                if (!is_null($shared_device)) {
                    $shared_device->delete();
                }
            }

            return response()->json([
                'success' => true
            ], 201);
        } else {
            $sharer_id = auth()->user()->id;
            $property_user_id = $property->user_id;
            if (($property_user_id == $sharer_id) /* && (Invitation::where(['sharer_id' => $sharer_id, 'sharee_id' => $sharee_id, 'accepted' => 1])->exists()) */) {
                $devices = $property->devices;
                foreach ($devices as $device) {
                    $device_id = $device->id;
                    $shared_device = SharedDevice::where(
                        [
                            'sharer_id' => $sharer_id,
                            'sharee_id' => $sharee_id,
                            'device_id' => $device_id,
                        ]
                    )->first();

                    if (!is_null($shared_device)) {
                        $shared_device->delete();
                    }
                }

                return response()->json([
                    'success' => true
                ], 201);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function deleteSharedRoom(Room $room, User $user)
    {
        $logged_in_user_id = auth()->user()->id;
        $sharee_id = $user->id;
        if ($logged_in_user_id == $sharee_id) {
            $devices = $room->devices;

            foreach ($devices as $device) {
                $device_id = $device->id;
                $shared_device = SharedDevice::where(
                    [
                        'sharee_id' => $sharee_id,
                        'device_id' => $device_id,
                    ]
                )->first();

                if (!is_null($shared_device)) {
                    $shared_device->delete();
                }
            }

            return response()->json([
                'success' => true
            ], 201);
        } else {
            $sharer_id = auth()->user()->id;
            $room_user_id = $room->user_id;
            if (($room_user_id == $sharer_id) /* && (Invitation::where(['sharer_id' => $sharer_id, 'sharee_id' => $sharee_id, 'accepted' => 1])->exists()) */) {
                $devices = $room->devices;

                foreach ($devices as $device) {
                    $device_id = $device->id;
                    $shared_device = SharedDevice::where(
                        [
                            'sharer_id' => $sharer_id,
                            'sharee_id' => $sharee_id,
                            'device_id' => $device_id,
                        ]
                    )->first();

                    if (!is_null($shared_device)) {
                        $shared_device->delete();
                    }
                }

                return response()->json([
                    'success' => true
                ], 201);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function deleteSharedDevice(Device $device, User $user)
    {
        $user_id = auth()->user()->id;
        $sharee_id = $user->id;

        $device_user_id = $device->user_id;
        if (($device_user_id == $user_id) || ($sharee_id == $user_id)) {
            $device_id = $device->id;

            $shared_device = SharedDevice::where(
                [
                    'sharee_id' => $sharee_id,
                    'device_id' => $device_id,
                ]
            )->first();

            if (!is_null($shared_device)) {
                $shared_device->delete();
            }

            return response()->json([
                'success' => true
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function sharedByMe()
    {
        $user_id = auth()->user()->id;
        $shared_devices = SharedDevice::where(['sharer_id' => $user_id])->get();
        $response_message = [];
        foreach ($shared_devices as $device) {
            $shared_device_details = ['sharer_id' => $device->sharer_id, 'sharee_id' => $device->sharee_id, 'role' => $device->role];
            $device_details = Device::find($device->device_id);
            $response_message[] = array_merge($shared_device_details, $device_details->toArray());
        }
        return response()->json([
            'success' => true,
            'message' => $response_message
        ], 201);
    }

    public function sharedWithMe()
    {
        $user_id = auth()->user()->id;
        $shared_devices = SharedDevice::where(['sharee_id' => $user_id])->get();
        $response_message = [];
        foreach ($shared_devices as $device) {
            $shared_device_details = ['sharer_id' => $device->sharer_id, 'sharee_id' => $device->sharee_id, 'role' => $device->role];
            $device_details = Device::find($device->device_id);
            $device_property = Property::find($device_details->property_id);
            $device_room = Room::find($device_details->room_id);
            $property_room_names = ['property_name' => $device_property->name, 'room_name' => $device_room->name];
            $response_message[] = array_merge($shared_device_details, $device_details->toArray(), $property_room_names);
        }
        return response()->json([
            'success' => true,
            'message' => $response_message
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

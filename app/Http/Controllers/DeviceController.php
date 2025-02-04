<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Group;
use App\Models\MqttAccessLog;
use App\Models\Network;
use App\Models\Property;
use App\Models\Room;
use App\Models\SharedDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function userDeviceList()
    {
        $device_list = auth()->user()->devices;

        return response()->json([
            'success' => true,
            'message' => $device_list,
        ], 201);
    }

    public function propertyDeviceList(Property $property)
    {
        $user_id = $property->user_id;
        if ($user_id == auth()->user()->id) {
            $device_list = $property->devices;

            return response()->json([
                'success' => true,
                'message' => $device_list,
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function roomDeviceList(Room $room)
    {
        $user_id = $room->user_id;
        if ($user_id == auth()->user()->id) {
            $device_list = $room->devices;

            return response()->json([
                'success' => true,
                'message' => $device_list,
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function networkDeviceList(Network $network)
    {
        $user_id = $network->user_id;
        if ($user_id == auth()->user()->id) {
            $device_list = $network->devices;

            return response()->json([
                'success' => true,
                'message' => $device_list,
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function groupDeviceList(Group $group)
    {
        $user_id = $group->user_id;
        if ($user_id == auth()->user()->id) {
            $device_list = $group->devices;

            return response()->json([
                'success' => true,
                'message' => $device_list,
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mqtt_id' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'ip_address' => 'required|string|max:255',
            'mac' => 'required|string|max:255',
            'property_id' => 'required|numeric',
            'network_id' => 'required|numeric',
            'room_id' => 'required|numeric',
            'position' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray(),
            ]);
        }

        $name = $request->name;
        $mqtt_id = $request->mqtt_id;
        $property_id = $request->property_id;
        $room_id = $request->room_id;
        $type = $request->type;
        $ip_address = $request->ip_address;
        $mac = $request->mac;
        $network_id = $request->network_id ?? 0;
        $position = $request->position;
        $max_value = isset($request->max_value) ? intval($request->max_value) : 0;
        $user_id = auth()->user()->id;

        if ($network_id == 0) {
            $validator = Validator::make($request->all(), [
                'ssid' => 'required|string|max:255',
                'password' => 'required|string|max:255',
                'mac_id' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toArray(),
                ]);
            }

            $ssid = $request->ssid;
            $password = $request->password;
            $mac_id = $request->mac_id;

            $network = Network::create([
                'user_id' => $user_id,
                'ssid' => $ssid,
                'password' => $password,
                'property_id' => $property_id,
                'mac_id' => $mac_id,
            ]);

            $network_id = $network->id;
        }

        $check_property = Property::where(['id' => $property_id, 'user_id' => $user_id])->first();
        $check_room = Room::where(['id' => $room_id, 'user_id' => $user_id, 'property_id' => $property_id])->first();
        $check_network = Network::where(['id' => $network_id, 'user_id' => $user_id, 'property_id' => $property_id])->first();
        if (!is_null($check_property) && !is_null($check_room) && !is_null($check_network)) {
            $device = Device::create([
                'name' => $name,
                'property_id' => $property_id,
                'user_id' => $user_id,
                'room_id' => $room_id,
                'network_id' => $network_id,
                'mqtt_id' => $mqtt_id,
                'type' => $type,
                'ip_address' => $ip_address,
                'mac' => $mac,
                'position' => $position,
                'max_value' => $max_value,
            ]);

            return response()->json([
                'success' => true,
                'message' => $device,
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Device $device)
    {
        $user_id = $device->user_id;
        $device_id = $device->id;
        $check_shared_device = SharedDevice::where(['device_id' => $device_id, 'sharee_id' => auth()->user()->id, 'role' => 2])->first();
        if (($user_id == auth()->user()->id) || !is_null($check_shared_device)) {
            return response()->json(['success' => true, 'message' => $device]);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Device $device)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mqtt_id' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'ip_address' => 'required|string|max:255',
            'mac' => 'required|string|max:255',
            'property_id' => 'required|numeric',
            'network_id' => 'required|numeric',
            'room_id' => 'required|numeric',
            'position' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray(),
            ]);
        }

        $name = $request->name;
        $mqtt_id = $request->mqtt_id;
        $property_id = $request->property_id;
        $room_id = $request->room_id;
        $type = $request->type;
        $ip_address = $request->ip_address;
        $mac = $request->mac;
        $network_id = $request->network_id;
        $position = $request->position;
        $max_value = isset($request->max_value) ? intval($request->max_value) : 0;
        $user_id = $device->user_id;
        $device_id = $device->id;

        if ($network_id == 0) {
            $validator = Validator::make($request->all(), [
                'ssid' => 'required|string|max:255',
                'password' => 'required|string|max:255',
                'mac_id' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toArray(),
                ]);
            }

            $ssid = $request->ssid;
            $password = $request->password;
            $mac_id = $request->mac_id;

            $network = Network::create([
                'user_id' => $user_id,
                'ssid' => $ssid,
                'password' => $password,
                'property_id' => $property_id,
                'mac_id' => $mac_id,
            ]);

            $network_id = $network->id;
        }

        $check_property = Property::where(['id' => $property_id, 'user_id' => $user_id])->first();
        $check_room = Room::where(['id' => $room_id, 'user_id' => $user_id, 'property_id' => $property_id])->first();
        $check_network = Network::where(['id' => $network_id, 'user_id' => $user_id, 'property_id' => $property_id])->first();
        $check_shared_device = SharedDevice::where(['device_id' => $device_id, 'sharee_id' => auth()->user()->id, 'role' => 2])->first();
        //return response()->json($check_shared_device);
        if ((!is_null($check_property) && !is_null($check_room) && !is_null($check_network) && ($user_id == auth()->user()->id)) || (!is_null($check_shared_device))) {
            try {
                $device->name = $name;
                $device->property_id = $property_id;
                $device->user_id = $user_id;
                $device->room_id = $room_id;
                $device->network_id = $network_id;
                $device->mqtt_id = $mqtt_id;
                $device->type = $type;
                $device->ip_address = $ip_address;
                $device->mac = $mac;
                $device->position = $position;
                $device->max_value = $max_value;
                $device->save();

                return response()->json([
                    'success' => true,
                    'message' => $device,
                ], 201);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Device $device)
    {
        $user_id = $device->user_id;
        $device_id = $device->id;
        $check_shared_device = SharedDevice::where(['device_id' => $device_id, 'sharee_id' => auth()->user()->id, 'role' => 2])->first();
        if (($user_id == auth()->user()->id) || !is_null($check_shared_device)) {
            try {
                $device->delete();

                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function updatePositions(Request $request)
    {
        $user_id = auth()->user()->id;
        if (isset($request->devices) && (count($request->devices) > 0)) {
            foreach ($request->devices as $device_id => $position) {
                $device = Device::find($device_id);
                if (!is_null($device)) {
                    $device_user_id = $device->user_id;

                    if ($device_user_id == $user_id) {
                        $device_position = intval($position);
                        $device->position = $device_position;
                        $device->save();
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
        ], 201);
    }

    public function topicACLCheck(Request $request)
    {
        $email = $request->username;
        $requested_topic = $request->topic;

        /* $user = User::where('email', $email)->first();
        if (!is_null($user)) {
        return response()->noContent(200);
        } */

        $device_mac_id = '';
        $device_id = 0;
        if ($this->string_starts_with($requested_topic, 'tele/') || $this->string_starts_with($requested_topic, 'stat/') || $this->string_starts_with($requested_topic, 'cmnd/')) {
            $topic_parts = explode('/', $requested_topic);
            if (count($topic_parts) > 1) {
                //$room_id_with_macid = $topic_parts[1];
                $middle_topic = $topic_parts[1];
                if ((stripos($middle_topic, 'clusterIoT') !== false) || (stripos($middle_topic, '_fb') !== false)) {
                    $mqtt_access_log = MqttAccessLog::create([
                        'req_type' => 'group_acl',
                        'device_id' => $device_id,
                        'email' => $email,
                    ]);

                    return response()->noContent(200);
                } else {
                    $room_id_with_macid_parts = explode('_', $middle_topic);
                    if (count($room_id_with_macid_parts) > 1) {
                        $device_mac_id = $room_id_with_macid_parts[1];
                    }
                }
            }
        } elseif ($this->string_starts_with($requested_topic, 'tasmota/discovery/')) {
            $topic_parts = explode('/', $requested_topic);
            if (count($topic_parts) > 2) {
                $device_mac_id = $topic_parts[2];
            }
        }

        if ($device_mac_id != '') {
            $device = Device::where('mac', $device_mac_id)->first();
            if (!is_null($device)) {
                $device_id = $device->id;
                $user = User::where('email', $email)->first();
                if (!is_null($user)) {
                    //have to write actual code
                    // $has_access_to_the_topic = 0;
                    $mqtt_access_log = MqttAccessLog::create([
                        'req_type' => 'acl',
                        'device_id' => $device_id,
                        'email' => $email,
                    ]);
                    $check_shared_device = SharedDevice::where(['device_id' => $device_id, 'sharee_id' => $user->id, 'role' => 2])->first();
                    if (($user->id == $device->user_id) || (!is_null($check_shared_device))) {
                        return response()->noContent(200);
                    }

                    // if ($has_access_to_the_topic) {
                    //     return response()->noContent(200);
                    // }
                }
            } else {
                // tele/<roomId_mac>/LWT
                // stat/<roomId_mac>/status5
                // cmnd/<roomId_mac>/status
                //Manual override for these type of topics, no auth checking, sending 200 by default
                if ((stripos($requested_topic, '/LWT') !== false) || (stripos($requested_topic, '/status5') !== false) || (stripos($requested_topic, '/status') !== false)) {
                    return response()->noContent(200);
                }
            }
        }

        $mqtt_access_log = MqttAccessLog::create([
            'req_type' => 'acl_fail',
            'device_id' => $device_id,
            'email' => $email,
        ]);

        return response()->noContent(400);
    }

    public function superuserCheck(Request $request)
    {
        $email = $request->username;

        $mqtt_access_log = MqttAccessLog::create([
            'req_type' => 'super',
            'device_id' => 0,
            'email' => $email,
        ]);

        if ($email == 'mahmudul.hasan@aclusterllc.com') {
            return response()->noContent(200);
        }

        return response()->noContent(400);
    }

    private function string_starts_with($string, $query)
    {
        return substr($string, 0, strlen($query)) === $query;
    }
}

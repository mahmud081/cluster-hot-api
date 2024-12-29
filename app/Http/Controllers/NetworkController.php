<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Network;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NetworkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function networkGroups(Network $network)
    {
        $group_devices = $network->groups;

        $response_message = [];
        $group_ids = $group_devices->pluck('group_id')->unique();

        if (count($group_ids) > 0) {
            foreach ($group_ids as $group_id) {
                $related_group = Group::find($group_id);
                if (!is_null($related_group)) {
                    $response_message[] = $related_group;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => $response_message
        ], 201);
    }

    public function propertyNetworkList(Property $property)
    {
        $user_id = $property->user_id;
        if ($user_id == auth()->user()->id) {
            $network_list = $property->networks;
            return response()->json([
                'success' => true,
                'message' => $network_list
            ], 200);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function userNetworkList()
    {
        $network_list = auth()->user()->networks;
        return response()->json([
            'success' => true,
            'message' => $network_list,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ssid' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'mac_id' => 'required|string|max:255',
            'property_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ]);
        }

        $ssid = $request->ssid;
        $password = $request->password;
        $property_id = $request->property_id;
        $mac_id = $request->mac_id;
        $user_id = auth()->user()->id;

        $check_property = Property::where(['id' => $property_id, 'user_id' => $user_id])->first();
        if (!is_null($check_property)) {
            $network = Network::create([
                'user_id' => $user_id,
                'ssid' => $ssid,
                'password' => $password,
                'property_id' => $property_id,
                'mac_id' => $mac_id
            ]);

            return response()->json([
                'success' => true,
                'message' => $network,
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Network $network)
    {
        $user_id = $network->user_id;
        if ($user_id == auth()->user()->id) {
            return response()->json(['success' => true, 'message' => $network]);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Network $network)
    {
        $validator = Validator::make($request->all(), [
            'ssid' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'mac_id' => 'required|string|max:255',
            'property_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ]);
        }

        $ssid = $request->ssid;
        $password = $request->password;
        $property_id = $request->property_id;
        $mac_id = $request->mac_id;
        $user_id = $network->user_id;

        $check_property = Property::where(['id' => $property_id, 'user_id' => $user_id])->first();

        if (!is_null($check_property) && ($user_id == auth()->user()->id)) {
            try {
                $network->ssid = $ssid;
                $network->password = $password;
                $network->property_id = $property_id;
                $network->mac_id = $mac_id;
                $network->save();
                return response()->json(['success' => true, 'message' => $network]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Network $network)
    {
        $user_id = $network->user_id;

        if ($user_id == auth()->user()->id) {
            try {
                $network->delete();
                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }
}

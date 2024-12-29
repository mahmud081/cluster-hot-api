<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function propertyRoomList(Property $property)
    {
        $user_id = $property->user_id;
        if ($user_id == auth()->user()->id) {
            $room_list = $property->rooms;
            return response()->json([
                'success' => true,
                'message' => $room_list
            ], 200);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function userRoomList()
    {
        $room_list = auth()->user()->rooms;
        return response()->json([
            'success' => true,
            'message' => $room_list,
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'columns' => 'required|numeric',
            'property_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ]);
        }

        $name = $request->name;
        $columns = $request->columns;
        $property_id = $request->property_id;
        $user_id = auth()->user()->id;

        $check_property = Property::where(['id' => $property_id, 'user_id' => $user_id])->first();
        if (!is_null($check_property)) {
            $room = Room::create([
                'user_id' => $user_id,
                'name' => $name,
                'columns' => $columns,
                'property_id' => $property_id
            ]);

            return response()->json([
                'success' => true,
                'message' => $room,
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        $user_id = $room->user_id;
        if ($user_id == auth()->user()->id) {
            return response()->json(['success' => true, 'message' => $room]);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'columns' => 'required|numeric',
            'property_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ]);
        }

        $name = $request->name;
        $columns = $request->columns;
        $property_id = $request->property_id;
        $user_id = $room->user_id;

        $check_property = Property::where(['id' => $property_id, 'user_id' => $user_id])->first();
        if (!is_null($check_property) && ($user_id == auth()->user()->id)) {
            try {
                $room->name = $name;
                $room->columns = $columns;
                $room->property_id = $property_id;
                $room->save();
                return response()->json(['success' => true, 'message' => $room]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $user_id = $room->user_id;

        if ($user_id == auth()->user()->id) {
            try {
                $room->delete();
                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }
}

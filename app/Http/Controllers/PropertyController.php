<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $property_list = auth()->user()->properties;
        return response()->json([
            'success' => true,
            'message' => $property_list,
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
                'message' => $validator->errors()->toArray()
            ]);
        }

        $name = $request->name;

        $property = Property::create([
            'user_id' => auth()->id(),
            'name' => $name,
        ]);

        return response()->json([
            'success' => true,
            'message' => $property,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        $user_id = $property->user_id;

        if ($user_id == auth()->user()->id) {
            return response()->json(['success' => true, 'message' => $property]);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ], 400);
        }

        $name = $request->name;
        $user_id = $property->user_id;

        if ($user_id == auth()->id()) {
            try {
                $property->name = $name;
                $property->save();
                return response()->json(['success' => true, 'message' => $property]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        $user_id = $property->user_id;

        if ($user_id == auth()->id()) {
            try {
                $property->delete();
                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }
}

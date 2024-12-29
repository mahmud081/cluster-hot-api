<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware(['api'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/refresh', [AuthController::class, 'refresh']);
    Route::get('/profile', [AuthController::class, 'userProfile']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    //Route::get('/lumsum', [AuthController::class, 'lumsum']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/logoutall', [AuthController::class, 'logoutall']);
});

Route::middleware(['jwt.verify'])->group(function () {
    Route::get('/products', [ProductController::class, 'show']);
    Route::get('/save-product', [ProductController::class, 'save']);

    // Property endpoints
    Route::post('/add-property', [PropertyController::class, 'store']);
    Route::get('/property-list', [PropertyController::class, 'index']);
    Route::get('/property-details/{property}', [PropertyController::class, 'show'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::post('/update-property/{property}', [PropertyController::class, 'update'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::get('/delete-property/{property}', [PropertyController::class, 'destroy'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    //end of Property endpoints

    // ROOM ENDPOINTS
    Route::post('/add-room', [RoomController::class, 'store']);
    Route::get('/user/room-list', [RoomController::class, 'userRoomList']);
    Route::get('/property/room-list/{property}', [RoomController::class, 'propertyRoomList'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::get('/room-details/{room}', [RoomController::class, 'show'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::post('/update-room/{room}', [RoomController::class, 'update'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::get('/delete-room/{room}', [RoomController::class, 'destroy'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    //End of ROOM ENDPOINTS

    // WIFI ENDPOINTS
    Route::post('/add-network', [NetworkController::class, 'store']);
    Route::get('/user/network-list', [NetworkController::class, 'userNetworkList']);
    Route::get('/property/network-list/{property}', [NetworkController::class, 'propertyNetworkList'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::get('/network-details/{network}', [NetworkController::class, 'show'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::get('/network/groups/{network}', [NetworkController::class, 'networkGroups'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::post('/update-network/{network}', [NetworkController::class, 'update'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::get('/delete-network/{network}', [NetworkController::class, 'destroy'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    // End of WIFI ENDPOINTS

    // DEVICE ENDPOINTS
    Route::post('/add-device', [DeviceController::class, 'store']);
    Route::get('/user/device-list', [DeviceController::class, 'userDeviceList']);
    Route::get('/property/device-list/{property}', [DeviceController::class, 'propertyDeviceList'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::get('/room/device-list/{room}', [DeviceController::class, 'roomDeviceList'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::get('/network/device-list/{network}', [DeviceController::class, 'networkDeviceList'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::get('/group/device-list/{group}', [DeviceController::class, 'groupDeviceList'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::get('/device-details/{device}', [DeviceController::class, 'show'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::post('/update-device/{device}', [DeviceController::class, 'update'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::get('/delete-device/{device}', [DeviceController::class, 'destroy'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::post('/update-position', [DeviceController::class, 'updatePositions']);
    // End of DEVICE ENDPOINTS
});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

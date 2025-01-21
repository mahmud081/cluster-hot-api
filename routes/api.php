<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SharedDeviceController;
use App\Http\Controllers\UserController;
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

    // GROUP ENDPOINTS
    Route::post('/add-group', [GroupController::class, 'store']);
    Route::get('/group-list', [GroupController::class, 'index']);
    Route::get('/group-details/{group}', [GroupController::class, 'show'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::post('/update-group/{group}', [GroupController::class, 'update'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::get('/delete-group/{group}', [GroupController::class, 'destroy'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    // END OF GROUP ENDPOINTS

    // INVITE ENDPOINTS
    Route::post('/search-user', [InvitationController::class, 'search']);
    Route::get('/send-invite/{user}', [InvitationController::class, 'store'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::get('/accept-invite/{invitation}', [InvitationController::class, 'update'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });
    Route::get('/delete-invite/{invitation}', [InvitationController::class, 'destroy'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });

    Route::get('/invitations-by-me', [InvitationController::class, 'invitationsByMe']);

    Route::get('/invitations-for-me', [InvitationController::class, 'invitationsforMe']);
    // END of INVITE ENDPOINTS

    // SHARE ENDPOINTS
    Route::post('/share-property/{property}/{user}', [SharedDeviceController::class, 'shareProperty'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });

    Route::post('/share-room/{room}/{user}', [SharedDeviceController::class, 'shareRoom'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });

    Route::post('/share-device/{device}/{user}', [SharedDeviceController::class, 'shareDevice'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });

    Route::post('/update/shared-property/{property}/{user}', [SharedDeviceController::class, 'updateSharedProperty'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });

    Route::post('/update/shared-room/{room}/{user}', [SharedDeviceController::class, 'updateSharedRoom'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });

    Route::post('/update/shared-device/{device}/{user}', [SharedDeviceController::class, 'updateSharedDevice'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });

    Route::get('/delete/shared-property/{property}/{user}', [SharedDeviceController::class, 'deleteSharedProperty'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });

    Route::get('/delete/shared-room/{room}/{user}', [SharedDeviceController::class, 'deleteSharedRoom'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });

    Route::get('/delete/shared-device/{device}/{user}', [SharedDeviceController::class, 'deleteSharedDevice'])->missing(function (Request $request) {
        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    });

    Route::get('/shared-by-me', [SharedDeviceController::class, 'sharedByMe']);
    Route::get('/shared-with-me', [SharedDeviceController::class, 'sharedWithMe']);

    // END of SHARE ENDPOINTS
});

Route::post('/test-params', [ProductController::class, 'showData']);

Route::post('/otp-verification', [UserController::class, 'verifyUser']);
Route::post('/resend-otp', [UserController::class, 'resendOtp']);
Route::post('/delete-account', [UserController::class, 'deleteAccount']);
Route::post('/recover-password-otp', [UserController::class, 'recoverPasswordOtp']);
Route::post('/recover-password', [UserController::class, 'recoverPassword']);

Route::post('/mqtt-acl-check', [DeviceController::class, 'topicACLCheck']);
Route::post('/super-check', [DeviceController::class, 'superuserCheck']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

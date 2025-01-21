<?php

namespace App\Http\Controllers;

use App\Mail\VerifyUser;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function verifyUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'id' => 'required|numeric',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ], 400);
        }

        $email = $request->email;
        $user_id = $request->id;
        $otp = $request->otp;
        $check = DB::table('user_verifications')->where(['email' => $email, 'user_id' => $user_id, 'token' => $otp])->first();

        if (!is_null($check)) {
            $user = User::find($check->user_id);

            if ($user->is_verified == 1) {
                DB::table('user_verifications')->where(['email' => $email, 'user_id' => $user_id, 'token' => $otp])->delete();

                return response()->json([
                    'success' => false,
                    'message' => 'This account is already verified.'
                ]);
            }

            $user->is_verified = 1;
            $user->save();

            DB::table('user_verifications')->where(['email' => $email, 'user_id' => $user_id, 'token' => $otp])->delete();

            return response()->json([
                'success' => true,
                'message' => 'You have successfully verified your email address.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Verification code is invalid.'
        ]);
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ], 400);
        }

        $email = $request->email;
        $user_id = $request->id;
        $check = DB::table('user_verifications')->where(['email' => $email, 'user_id' => $user_id])->first();

        if (!is_null($check)) {
            $user = User::find($check->user_id);

            if ($user->is_verified == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'This account is already verified.'
                ]);
            }

            $verification_code = random_int(100000, 999999);
            $update_otp = DB::table('user_verifications')
                ->where('id', $check->id)
                ->update(['token' => $verification_code]);

            if ($update_otp) {

                $name = $user->name;
                $formData = [
                    'name' => $user->name,
                    'verification_code' => $verification_code
                ];

                Mail::to($email, $name)->send(new VerifyUser($formData));


                return response()->json([
                    'success' => true,
                    'message' => 'New verification code is sent to your email address.'
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ], 400);
        }

        $email = $request->email;

        $user = User::where('email', $email)->first();
        if (is_null($user)) {
            return response()->json(['success' => false, 'message' => 'Invalid email address.']);
        }

        if ($user->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Account deleted.'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
    }

    public function recoverPasswordOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ], 400);
        }

        $email = $request->email;
        $user = User::where('email', $email)->first();
        if (is_null($user)) {
            return response()->json(['success' => false, 'message' => 'Invalid email address.']);
        }
        $user_id = $user->id;

        $verification_code = random_int(100000, 999999);
        $check = DB::table('user_verifications')->where(['email' => $email, 'user_id' => $user_id])->first();

        if (!is_null($check)) {
            $update_insert_otp = DB::table('user_verifications')
                ->where('id', $user_id)
                ->update(['token' => $verification_code]);
        } else {
            $update_insert_otp = DB::table('user_verifications')->insert(['user_id' => $user->id, 'token' => $verification_code, 'email' => $email]);
        }

        if ($update_insert_otp) {
            $subject = 'Please verify your email address.';
            $name = $user->name;
            $formData = [
                'name' => $user->name,
                'verification_code' => $verification_code
            ];

            Mail::to($email, $name)->send(new VerifyUser($formData));


            return response()->json([
                'success' => true,
                'message' => 'Email verification code is sent to your email address.'
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
        }
    }

    public function recoverPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:6|same:confirm_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ], 400);
        }

        $email = $request->email;
        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (is_null($user)) {
            return response()->json(['success' => false, 'message' => 'Invalid email address.']);
        }

        $user_id = $user->id;
        $otp = $request->otp;
        $check = DB::table('user_verifications')->where(['email' => $email, 'user_id' => $user_id, 'token' => $otp])->first();

        if (!is_null($check)) {
            $user->is_verified = 1;
            $user->password = Hash::make($request->input('password'));
            $user->save();

            DB::table('user_verifications')->where(['email' => $email, 'user_id' => $user_id, 'token' => $otp])->delete();

            foreach ($user->validTokens as $token_obj) {
                try {
                    $token_obj->delete();
                } catch (Exception $e) {
                    //do nothing, it's already bad token for various reasons
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Your password is successfully updated.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Verification code is invalid.'
        ]);
    }
}

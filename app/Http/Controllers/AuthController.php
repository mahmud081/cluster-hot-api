<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Mail;
use Validator;
use DB;
use App\Models\Token;
use App\Rules\MatchCurrentPassword;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['login', 'register', 'otp-verification']]);
        $this->middleware('jwt.xauth', ['except' => ['login', 'register', 'refresh', 'otp-verification']]);
        $this->middleware('jwt.xrefresh', ['only' => ['refresh']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$access_token = auth()->claims(['xtype' => 'auth'])->attempt($credentials)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        return $this->respondWithToken($access_token);
    }

    /**
     * Register a User and issue login token
     *
     * @param string $name, $email, $password, $password_confirmation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ]);
        }

        $name = $request->name;
        $email = $request->email;

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($request->input('password'))
        ]);

        //$verification_code = Str::random(30); //Generate verification code
        $verification_code = random_int(100000, 999999);
        DB::table('user_verifications')->insert(['user_id' => $user->id, 'token' => $verification_code, 'email' => $email]);

        $subject = 'Please verify your email address.';
        Mail::send(
            'email.verify',
            ['name' => $name, 'verification_code' => $verification_code],
            function ($mail) use ($email, $name, $subject) {
                $mail->from(getenv('MAIL_FROM_ADDRESS'), getenv('MAIL_FROM_NAME'));
                $mail->to($email, $name);
                $mail->subject($subject);
            }
        );

        return response()->json([
            'success' => true,
            'message' => $user,
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $access_token_obj = Token::findByValue(auth()->getToken()->get());
        $refresh_token_obj = Token::findPairByValue(auth()->getToken()->get());
        auth()->logout();
        $access_token_obj->status = 'INVALID';
        $access_token_obj->save();

        auth()->setToken($refresh_token_obj->value)->logout();
        $refresh_token_obj->status = 'INVALID';
        $refresh_token_obj->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ]);
        }

        $name = $request->name;
        $phone = $request->phone;
        $address = $request->address;
        $country = $request->country;

        $user = auth()->user();
        $user->name = $name;
        $user->phone = $phone;
        $user->address = $address;
        $user->country = $country;
        try {
            $user->save();
            return response()->json(['success' => true, 'message' => $user]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
        }
    }

    public function logoutall()
    {
        foreach (auth()->user()->validTokens as $token_obj) {
            try {
                auth()->setToken($token_obj->value)->invalidate(true);
                $token_obj->status = 'INVALID';
                $token_obj->save();
            } catch (Exception $e) {
                //do nothing, it's already bad token for various reasons
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out from all devices'
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', new MatchCurrentPassword],
            'new_password' => 'required|string|min:6|same:new_confirm_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ]);
        }

        $new_password = $request->new_password;

        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $user->update(['password' => Hash::make($new_password)]);
        foreach ($user->validTokens as $token_obj) {
            try {
                $token_obj->delete();
            } catch (Exception $e) {
                //do nothing, it's already bad token for various reasons
            }
        }
        //$this->logoutall();
        return response()->json([
            'success' => true,
            'message' => 'Password is successfully updated.'
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $access_token = auth()->claims(['xtype' => 'auth'])->refresh(true, true);
        auth()->setToken($access_token);

        return $this->respondWithToken($access_token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($access_token)
    {
        $response_array = [
            'user_id' => auth()->user()->id,
            'access_token' => $access_token,
            'token_type' => 'bearer',
            'access_expires_in' => auth()->factory()->getTTL() * 60,
        ];

        $access_token_obj = Token::create([
            'user_id' => auth()->user()->id,
            'value' => $access_token, //or auth()->getToken()->get();
            'jti' => auth()->payload()->get('jti'),
            'type' => auth()->payload()->get('xtype'),
            'payload' => auth()->payload()->toArray(),
        ]);

        $refresh_token = auth()->claims([
            'xtype' => 'refresh',
            'xpair' => auth()->payload()->get('jti')
        ])->setTTL(auth()->factory()->getTTL() * 3)->tokenById(auth()->user()->id);

        $response_array += [
            'refresh_token' => $refresh_token,
            'refresh_expires_in' => auth()->factory()->getTTL() * 60
        ];

        $refresh_token_obj = Token::create([
            'user_id' => auth()->user()->id,
            'value' => $refresh_token,
            'jti' => auth()->setToken($refresh_token)->payload()->get('jti'),
            'type' => auth()->setToken($refresh_token)->payload()->get('xtype'),
            'pair' => $access_token_obj->id,
            'payload' => auth()->setToken($refresh_token)->payload()->toArray(),
        ]);

        $access_token_obj->pair = $refresh_token_obj->id;
        $access_token_obj->save();

        return response()->json($response_array);
    }

    public static function findPairByValue($token)
    {
        $token_obj = self::findByValue($token);
        return Token::find($token_obj->pair);
    }
}

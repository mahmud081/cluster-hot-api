<?php

namespace App\Http\Middleware;

use App\Models\Token;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;


class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            $token_obj = Token::findByValue(auth()->getToken()->get());

            if ($token_obj) {
                //OUR APP DID NOT ISSUED THIS TOKEN, POSSIBLE SECURITY BREACH
                $token_obj->status = 'INVALID';
                $token_obj->save();

                $refresh_token_obj = Token::findPairByValue(auth()->getToken()->get());
                $refresh_token_obj->status = 'INVALID';
                $refresh_token_obj->save();
            }

            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['success' => false, 'message' => 'Invalid Token'], 403);
            } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['success' => false, 'message' => 'Invalid Token'], 401);
            } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException) {
                return response()->json(['success' => false, 'message' => 'Invalid Token'], 400);
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid Token'], 404);
            }
        }

        $token_obj = Token::findByValue(auth()->getToken()->get());

        if (!$token_obj) {
            //OUR APP DID NOT ISSUED THIS TOKEN, POSSIBLE SECURITY BREACH
            return response()->json(['success' => false, 'message' => 'Invalid Token'], 403);
        }
        return $next($request);
    }
}

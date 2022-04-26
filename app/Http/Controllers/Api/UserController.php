<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Log;

class UserController extends Controller
{
  public function authenticate(Request $request) {
    $validated = $request->validate([
      'email' => 'required|string|email|max:255',
      'password' => 'required|string|min:6',
    ]);
    $credentials = $request->only('email', 'password');
    try {
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'invalid_credentials'], 400);
        }
    } catch (JWTException $e) {
        return response()->json(['error' => 'could_not_create_token'], 500);
    }
    return response()->json(compact('token'));
  }

  public function getAuthenticatedUser() {
    try {
      if (!$user = JWTAuth::parseToken()->authenticate()) {
              return response()->json(['user_not_found'], 404);
      }
    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
    }
    return response()->json(compact('user'));
  }


  public function register(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:6|confirmed',
    ]);

    $user = User::create([
      'name' => $request->get('name'),
      'email' => $request->get('email'),
      'password' => Hash::make($request->get('password')),
    ]);

    $token = JWTAuth::fromUser($user);

    return response()->json(compact('user','token'),201);
  }
  public function logout( Request $request ) {

    $token = $request->header( 'Authorization' );

    try {
        JWTAuth::parseToken()->invalidate( $token );

        return response()->json( [
            'error'   => false,
            'message' => trans( 'auth.logged_out' )
        ] );
    } catch ( TokenExpiredException $exception ) {
        return response()->json( [
            'error'   => true,
            'message' => trans( 'auth.token.expired' )

        ], 401 );
    } catch ( TokenInvalidException $exception ) {
        return response()->json( [
            'error'   => true,
            'message' => trans( 'auth.token.invalid' )
        ], 401 );

    } catch ( JWTException $exception ) {
        return response()->json( [
            'error'   => true,
            'message' => trans( 'auth.token.missing' )
        ], 500 );
    }
  }
}
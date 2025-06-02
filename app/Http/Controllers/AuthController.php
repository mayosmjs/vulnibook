<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\VulnJWT;

/**
 * Class AuthControllerv1
 * @package App\Http\Controllers
 *
 * This controller contains vulnerable authentication methods for demonstration purposes.
 * It is not secure and should not be used in production.
 */

class AuthController extends Controller
{


    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, //Plaintext
        ]);

        $token = VulnJWT::generateToken($user);
        return response()->json(['token' => $token]);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)
            ->where('password', $request->password)
            ->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = VulnJWT::generateToken($user);
        return response()->json(['token' => $token]);
    }


    public function me(Request $request)
    {
        $payload = $request->get('auth_user');

        $user = User::find($payload['id']);

        return response()->json([
            'user' => $user
        ]);
        

    }


    public function updateProfile(Request $request)
    {
        $payload = $request->get('auth_user');
        // Check if 'id' is passed in request to impersonate another user
        $targetUserId = $request->input('id', $payload['id']); // default to self
        // Fetch user directly from DB (no authorization checks)
        $user = User::find($targetUserId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        // Update profile — no validation
        $user->name = $request->input('name', $user->name);
        $user->email = $request->input('email', $user->email);
        $user->password = $request->input('password', $user->password); // plaintext
        $user->save();
        return response()->json(['message' => 'Profile updated', 'user' => $user]);
    }



    public function promoteUser(Request $request)
    {
        $payload = $request->get('auth_user');
        //Check only the 'role' in the JWT (vulnerable)
        if (($payload['role'] ?? '') !== 'admin') {
            return response()->json(['error' => 'Unauthorized – must be admin'], 403);
        }
    
        $targetUserId = $request->input('id');
    
        if (!$targetUserId) {
            return response()->json(['error' => 'No user ID provided'], 400);
        }

        $user = User::find($targetUserId);
    
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        //Anyone with a forged token that says 'admin' can promote anyone
        $user->role = 'admin';
        $user->save();
    
        return response()->json([
            'message' => "User {$user->email} promoted to admin",
            'user' => $user
        ]);
    }
    



    public function forgotPassword(Request $request)
{
    $email = $request->input('email');

    $user = \App\Models\User::where('email', $email)->first();
    if (!$user) {
        // Leak info about existence of account (A06: Information Exposure)
        return response()->json(['error' => 'Email not found'], 404);
    }

    //Weak token not cryptographically secure
    $token = substr(md5(time() . $email), 0, 16);
    $user->password_reset_token = $token;
    $user->save();

    // Send link (we'll just return it for demo)
    $link = url("/api/reset-password?token={$token}");
    return response()->json([
        'message' => 'Reset link generated (vulnerably)',
        'reset_link' => $link
    ]);
}



public function resetPassword(Request $request)
{
    $token = $request->input('token');
    $newPassword = $request->input('password');

    // Find user by token (vulnerable to token guessing)
    $user = User::where('password_reset_token', $token)->first();
    if (!$user) {
        return response()->json(['error' => 'Invalid or expired token'], 400);
    }

    // Update password (plaintext)
    $user->password = $newPassword;
    $user->password_reset_token = null; // Clear token
    $user->save();

    return response()->json(['message' => 'Password reset successfully']);  

}

   
    


}

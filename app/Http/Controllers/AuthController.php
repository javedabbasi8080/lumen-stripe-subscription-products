<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Exception;

/**
 * @group User Authentication
 *
 * APIs for user authentication
 */

class AuthController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'logout']]);
    }


    /**
     * Login user
     *
     * @bodyParam email string required Example: admin@admin.com
     * @bodyParam password string required Example: password
     */
    public function login(Request $request)
    {

        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        try {

            $credentials = $request->only(['email', 'password']);

            if (!$token = Auth::attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'Unauthorized'
                ], 401);
            }

            return $this->respondWithToken($token);
            //code...
        } catch (Exception $e) {

            return response()->json([
                'status'  => false,
                'data'    => $e->getMessage(),
                'message' => 'error occurred'
            ], 500);
        }
    }

    /**
     * Get current User.
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {

        try {
            return response()->json([
                'status' => true,
                'data' => auth()->user(),
                'message' => 'Successfully'
            ]);
            //code...
        } catch (Exception $e) {

            return response()->json([
                'status'  => false,
                'data'    => $e->getMessage(),
                'message' => 'error occurred'
            ], 500);
        }
    }

    /**
     * Assign role to user
     *
     * @authenticated
     * @bodyParam user_id integer required Example: 1
     * @bodyParam role_id integer required Example: 2
     */
    public function assignRole(Request $request)
    {
        // Validate the request parameters
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id'
        ]);

        try {

            // Check if the authenticated user is an admin
            if (!auth()->user()->hasRole('superadmin')) {

                return response()->json([
                    'status'  => false,
                    'data'    => null,
                    'message' => 'Only superadmin can assign roles.'
                ], 403);
            }

            // Retrieve the user and role models
            $user = User::find($request->user_id);
            $role = Role::find($request->role_id);

            // Check if the user and role exist
            if (!$user || !$role) {
                return response()->json([
                    'status'  => false,
                    'data'    => null,
                    'message' => 'User or role not found.'
                ], 403);
            }

            // Assign the role to the user
            $user->roles()->sync([$role->id]);


            return response()->json([
                'status'  => true,
                'data'    => $user,
                'message' => 'Role assigned successfully.'
            ], 200);

            //code...
        } catch (Exception $e) {

            return response()->json([
                'status'  => false,
                'data'    => $e->getMessage(),
                'message' => 'error occurred'
            ], 500);
        }
    }



    /**
     * user subscribed plan
     *
     * @authenticated
     */
    public function usersubscribePlan()
    {
        try {
            $sub = Subscription::with('product')->where('user_id',auth()->user()->id)->first();
            return response()->json([
                'status' => true,
                'data' => $sub,
                'message' => 'Successfully'
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status'  => false,
                'data'    => $e->getMessage(),
                'message' => 'error occurred'
            ], 500);
        }
    }



    /**
     * Logout
     *
     * @authenticated
     */
    public function logout()
    {
        try {
            auth()->logout();

            return response()->json([
                'status' => true,
                'data' => null,
                'message' => 'Successfully logged out'
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status'  => false,
                'data'    => $e->getMessage(),
                'message' => 'error occurred'
            ], 500);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {

        $user = auth()->user();
        $userWithRole = $user->load('roles'); // Assuming the relation is named 'role' in your User model
        return response()->json([
            'status' => true,
            'data' => [
                'token_type' => 'bearer',
                'token' => $token,
                'user' => $userWithRole
            ],
            'message' => 'Logged in Successfully'
        ]);
    }
}

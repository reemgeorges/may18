<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTrait;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator ;

use function PHPUnit\Framework\isEmpty;

class AuthController
{
    use GeneralTrait;
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4',
        ]);

        if($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }
        try {
            $roles = Role::whereIn('id', $request['roles_id'])->get();
            if($roles->count()==0) {
                return $this->errorResponse('No roles with such ids', 500);
            }
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $user->roles()->attach($roles);
            $data['token'] = $user->createToken('MyApp')->plainTextToken;
            $data['name'] = $user->name;
            $data['user'] = $user;
            return $this->successResponse($data, 'User is registered successfully.');
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator =Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        if($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            $user = User::where('email', $request['email'])->first();

            if (!$user || !Hash::check($request ['password'], $user->password)) {
                return $this->errorResponse('incorrect username or password', 400);
            }
            $data['token'] = $user->createToken('apiToken')->plainTextToken;
            $data['name'] = $user->name;
            return $this->successResponse($data, 'User has logged in successfully.');

        } catch(\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }

    }

  public function logout(Request $request)
  {
      auth('sanctum')->user()->tokens->delete();

      return $this->successResponse([], 'User has logged out successfully.');
  }

}

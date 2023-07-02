<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Traits\GeneralTrait;
use Illuminate\Http\Request;
class UserController extends Controller
{
    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try{
            $data=User::with('roles')->get();
            $msg='All users are right here';
            return $this->successResponse(new UserCollection($data),$msg);

        }
        catch (\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function getUsersByRole()
   {//to be continued
       $data=User::whereRelation('roles','role_name','=','admin')->get();
       return new UserCollection($data);
   }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show( $id)
    {

        try{//to be continued
            $data=User::with('roles')->findOrFail($id);
            $msg='Got you the user you are looking for';
            return $this->successResponse($data,$msg);
        }
        catch (\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateRoles(Request $request, User $user)
    {
        //tobe continued

        try{
            $data=$user->roles()->sync($request['roles_id']);
            $msg='The user roles are updated successfully';
            return $this->successResponse($data,$msg);
        }
        catch (\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    public function getUsersWithReviews()
    {
        $usersWithReviews = User::withCount('reviews')
            ->orderByDesc('reviews_count')
            ->get();

        $userResource = UserResource::collection($usersWithReviews);

        return $this->successResponse($userResource, 'Users with reviews retrieved successfully');
    }



}


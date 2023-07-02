<?php

namespace App\Http\Controllers\reviews;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\ReviewCollection;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\GeneralTrait;
use Illuminate\Support\Facades\Auth;

class ReviewsController extends Controller
{
    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $reviews = Review::with('user', 'product')->get();

            $reviewCollection = ReviewResource::collection($reviews);

            return $this->successResponse($reviewCollection, 'Reviews retrieved successfully');


        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['user_id']=Auth::id();//ليجيب id من المستخدم المتدخل
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'comment' => ['required', 'string'],
            'star' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {

            $review = Review::create($request->only(['user_id', 'product_id', 'comment', 'start']));

            $message = 'Review created successfully';
            return $this->successResponse(new ReviewResource($review), $message, 201);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $review = Review::with('user', 'product')->find($id);

            if (!$review) {
                return $this->errorResponse('Review not found', 404);
            }

            $msg = 'Review retrieved successfully';

            return $this->successResponse(new ReviewResource($review), $msg);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request['user_id'] = Auth::id(); // Get the user ID from the authenticated user

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'comment' => 'required|string',
            'start' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            $review = Review::find($id);
            if (!$review) {
                return $this->errorResponse('Review not found', 404);
            }
            if (Auth::id() !== $review->user_id) {
                return $this->errorResponse('You are not authorized to update this review', 403);
            }
            $review->update($request->all());

            $msg = 'The review is updated successfully';
            return $this->successResponse(new ReviewResource($review->load('user', 'product')), $msg);

        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $review = Review::find($id);
            if (!$review) {
                return $this->errorResponse('Review not found', 404);
            }
            $review->delete();
            $msg='Review deleted successfully';
            return $this->successResponse($review, $msg);

        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }
    public function getUserReviews($userId)
    {
        try {
            $reviews = Review::where('user_id', $userId)->with('product')->get();
            return $this->successResponse(ReviewResource::collection($reviews));
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    public function getProductReviews($productId)
    {
        try {
            $reviews = Review::where('product_id', $productId)->with('user')->get();
            return $this->successResponse(ReviewResource::collection($reviews));
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }
}

<?php

namespace App\Http\Controllers\review;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $reviews = Review::all();


            return ReviewResource::collection($reviews);

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

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'comment' => 'required|string',
            'start' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            $review = Review::create($request->all());

            $msg='review is created successfully';
            return $this->successResponse(new ReviewResource($review), $msg, 201);


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
            $review = Review::find($id);
            if (!$review) {

                return $this->errorResponse('Review not found', 404);
            }
            $msg='done';

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

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
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
            $review->update($request->all());

            $msg='The review is updated successfully';
            return $this->successResponse(new ReviewResource($review), $msg);

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
            $reviews = Review::where('user_id', $userId)->get();
            return $this->successResponse(ReviewResource::collection($reviews));
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    public function getProductReviews($productId)
    {
        try {
            $reviews = Review::where('product_id', $productId)->get();
            return $this->successResponse(ReviewResource::collection($reviews));
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }
}

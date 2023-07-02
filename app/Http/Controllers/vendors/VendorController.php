<?php

namespace App\Http\Controllers\vendors;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $vendors = Vendor::with('products');
            $vendorCollection = VendorResource::collection($vendors);
            return $this->successResponse($vendorCollection, 'Vendors retrieved successfully');
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
            'name_vendor' => ['required', 'string'],
            'products' => ['nullable', 'array'],
            'products.*' => ['integer', 'exists:products,id'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            $vendor = Vendor::create([
                'name_vendor' => $request->input('name_vendor'),
            ]);

            // إنشاء العلاقة بين البائع والمنتجات في الجدول المشترك
            if ($request->has('products')) {
                $products = $request->input('products');
                $vendor->products()->attach($products);
            }

            $msg = 'Vendor created successfully';
            return $this->successResponse(new VendorResource($vendor), $msg);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor)
    {
        try {
            return $this->successResponse(new VendorResource($vendor));
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validator = Validator::make($request->all(), [
            'name_vendor' => ['required', 'string'],
            'products' => ['nullable', 'array'],
            'products.*' => ['integer', 'exists:products,id'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            $vendor->update([
                'name_vendor' => $request->input('name_vendor'),
            ]);

            // تحديث العلاقة بين البائع والمنتجات في الجدول المشترك
            if ($request->has('products')) {
                $products = $request->input('products');
                $vendor->products()->sync($products);
            }

            $msg = 'Vendor updated successfully';
            return $this->successResponse(new VendorResource($vendor), $msg);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendor $vendor)
    {
        try {
            // حذف العلاقة بين المنتج والبائع في الجدول المشترك
            $vendor->products()->detach();

            // حذف البائع نفسه
            $vendor->delete();

            $msg = 'Vendor deleted successfully';
            return $this->successResponse($msg);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }
}

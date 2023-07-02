<?php

namespace App\Http\Controllers\orders;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTrait;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

class OrderController extends Controller
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
            $orders = Order::with('user', 'products')->get();

            $orderCollection = OrderResource::collection($orders);

            return $this->successResponse($orderCollection, 'Order retrieved successfully');


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
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }
        try {
            $order = new Order();
            // $order->quantity = $request->input('quantity');

            $user = User::findOrFail($request->input('user_id'));
            $order->user()->associate($user)->save();


            $order->products()->attach($request->input('product_id'), ['quantity' => $request->input('quantity')]);

            $msg = 'Order created successfully';
            return $this->successResponse((new OrderResource($order))->load('products', 'user'), $msg);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }

    }

    /**
     * Display the specified resource.
     *a
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        try {
            $order->load('products', 'user');
            return $this->successResponse(new OrderResource($order));
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $request['user_id']=Auth::id();
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'products' => ['required', 'array'],
            'products.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        if ($order->user_id !== Auth::id()) {
            return $this->errorResponse('You are not authorized to update this order', 403);
        }

        try {
            // تحديث بيانات الطلب
            $order->update([
                'user_id' => $request->input('user_id'),
            ]);

            // تحديث العلاقة بين المنتجات والطلب
            $products = $request->input('products');
            $productIds = collect($products)->pluck('product_id')->toArray();
            $quantities = collect($products)->pluck('quantity')->toArray();

            $order->products()->sync(array_combine($productIds, $quantities));

            $msg = 'The order is updated successfully';
            return $this->successResponse((new OrderResource($order))->load('products', 'user'), $msg);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        try {
            // حذف العلاقة بين المنتجات والطلب من الجدول الثالث
            $order->products()->detach();

            // حذف الطلب نفسه
            $order->delete();

            $msg = 'The order is deleted successfully';
            return $this->successResponse($msg);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

}

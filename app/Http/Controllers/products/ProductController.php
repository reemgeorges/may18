<?php

namespace App\Http\Controllers\products;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTrait;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Validator;
use function Nette\Utils\isEmail;
use function PHPUnit\Framework\isEmpty;

class ProductController extends Controller
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
           $msg='all products are Right Here';
           $data=Product::with('category')->get();
           return $this->successResponse($data,$msg);
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
    public function store(Request $request)
    {

        $validator=FacadesValidator::make($request->all(),[
            'product_name'=>'required|regex:/[a-zA-Z\s]+/',
                'desc'=>'required|string',
                'price'=>'required|numeric'
            ]
        );
                if($validator->fails()){
            return $this->errorResponse($validator->errors(),422);
        }
      try {
            $category = Category::firstOrCreate([
                'category_name' => $request->category_name
            ]);
            $product = Product::create($request->all());
            $product->category()->associate($category)->save();
           $data=$product;
           $msg='product is created successfully';
            return $this->successResponse($data,$msg,201);
        }
        catch (\Exception $ex)
        {
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Poduct  $poduct
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        try{
            $data=Product::with('category')->find($id);
            if(!$data)
                return $this->errorResponse('No product with such id',404);


            $msg='Got you the product you are looking for';
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
     * @param  \App\Models\Product  $poduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try{
            $data=Product::find($id);
            if(!$data)
                return $this->errorResponse('No product with such id',404);

            $data->update($request->all());
            $data->save();
            $msg='The product is updated successfully';
            return $this->successResponse($data,$msg);
        }
        catch (\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Poduct  $poduct
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $data=Product::find($id);
            if(!$data)
                return $this->errorResponse('No product with such id',404);

            $data->delete();
            $msg='The product is deleted successfully';
            return $this->successResponse($data,$msg);
        }
        catch (\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    public function  filterProductsByCategory($letter)
    {
        try {
            $data= Product::whereRelation('category','category_name','like',$letter.'%')->with('category')->get();
            $msg='Got data Successfully';
            return $this->successResponse($data,$msg);
        }
    catch (\Exception $ex)
    { return $this->errorResponse($ex->getMessage(),500); }
    }
}

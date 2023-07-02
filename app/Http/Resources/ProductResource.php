<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

    return [
        'id' => $this->id,
        'category' => $this->category->category_name,
        'product_name' => $this->product_name,
        'desc' => $this->desc,
        'price' => $this->price,
        'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
    ];
    }
}

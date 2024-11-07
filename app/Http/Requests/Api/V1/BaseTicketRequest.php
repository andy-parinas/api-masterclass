<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseTicketRequest extends FormRequest
{

    public function mappedAttributes()
    {
        $attributeMap = [
            'data.attributes.title' => 'title',
            'data.attributes.description' => 'description',
            'data.attributes.status' => 'status',
            'data.relationships.user.data.id' => 'user_id'
        ];

        $attributesToUpdate = [];
        foreach($attributeMap as $key => $attribute){
            if($this->has($key)){
                $attributesToUpdate[$attribute] = $this->input($key);
            }
        }

        return $attributesToUpdate;
    }


    public function messages(): array
    {
        return [
            'data.attributes.status' => 'The status value is invalid. Please use: A, C, H, or X'
        ];
    }
}

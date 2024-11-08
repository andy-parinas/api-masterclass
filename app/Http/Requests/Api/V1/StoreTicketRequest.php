<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;
use App\Rules\UserMustExist;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreTicketRequest extends BaseTicketRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'data' => 'required|array',
            'data.attributes' => 'required|array',
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,C,H,X',
            'data.realtionships' => 'required|array',
            'data.relationships.user' => 'required|array',
            'data.relationships.user.data' => 'required|array',
            'data.relationships.user.data.id' => 'required|integer|exists:users,id', // This is the same as the  UserMustExist custom Rules that was created.


        ];

        $user = Auth::user();

        if($this->routeIs('api.v1.tickets.store')){
            if($user->tokenCan(Abilities::CreateOwnTicket)){
                $rules['data.relationships.user.data.id'] .= '|size:' . $user->id;
            }
        }


        return $rules;
    }
}

<?php

namespace App\Http\Requests;

use App\Traits\HasJsonFailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductSellRequest extends FormRequest
{
    use HasJsonFailedValidation;

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
        $product_id = (int) $this->route('id');
        return [
            'from_user' => ['required', "exists:product_user,user_id,product_id,$product_id"],
            'to_user' => ['required', Rule::exists('users', 'id')],
            'price' => ['required', 'numeric'],
            'quantity' => ['required', 'numeric'],
        ];

    }
}

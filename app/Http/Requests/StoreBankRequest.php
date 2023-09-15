<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreBankRequest
 *
 * @package App\Http\Requests
 *
 * @property string $bank_name
 * @property string $account_name
 * @property string $account_number
 */
class StoreBankRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array>
     */
    public function rules(): array
    {
        return [
            'bank_name' => ['required'],
            'branch_name' => ['required'],
            'account_name' => ['required'],
            'account_number' => ['required'],
            'status' => ['required'],
        ];
    }
}

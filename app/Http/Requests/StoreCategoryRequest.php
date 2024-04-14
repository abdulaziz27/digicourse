<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // form request hanya bisa diakses oleh owner
        return $this->user()->hasAnyRole(['owner']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // php artisan make:request StoreCategoryRequest
        // digunakan sebagai proses validasi data yang dilempar dari ui form pengguna
        // kalo berhasil baru ke proses penyimpanan datanya

        return [
            // even th laravel 11 doesnt need this but, lebih baik bikin request tersendiri
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['required', 'image', 'mimes:png,jpg,jpeg,svg'],
            
        ];
    }
}

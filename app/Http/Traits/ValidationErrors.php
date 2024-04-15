<?php
namespace App\Traits;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

trait ValidationErrors {
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'code' =>422,
            'status' => 'false',
            'message' =>   collect($validator->errors())->flatten(1)
        ], 422));
    }
}

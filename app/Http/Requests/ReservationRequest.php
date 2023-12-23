<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'   => ['required', 'string', 'max:190'],
            'email'  => [
                'required',
                'email',
                'max:190'
            ],
            'phone'        => ['required', 'string', 'max:20'],
            'no_of_guests' => ['required', 'Integer'],
            'reservation_date_time' => ['required', 'string'],
        ];
    }
}

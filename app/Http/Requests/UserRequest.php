<?php
namespace App\Http\Requests;

class UserRequest extends FormRequest
{

    /**
     * @var array
     */
    private $validationRules = [
        'first_name' => 'required|min:3|max:25',
        'last_name' => 'required|min:3|max:25'
    ];

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
    public function rules(): array
    {
        # create
        if ($this->isMethod('post')) {
            return $this->validationRules + [
                    'password' => 'required|min:6',
                    'email' => 'required|email|unique:users'
                ];
        }

        # update
        ## get_route_id() is a helper method

        $userId = get_route_id($this->route());

        return $this->validationRules + [
                'password' => 'sometimes|min:6',
                'email' => 'required|email|unique:users,email,'.$userId,
            ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [];
    }
}
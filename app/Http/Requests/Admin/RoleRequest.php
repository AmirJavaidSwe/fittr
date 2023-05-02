<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;


class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user() ? true : false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch (strtoupper($this->method())) {
            case 'POST': // for store method
                return [
                    'name' => [
                        'required',
                        'string',
                        'max:191',
                        Rule::unique('roles', 'name')->whereNull('deleted_at')
                    ]
                ];
                break;
                case 'PUT': // for update method

                    $role = Role::where('slug', request()->slug)->first();
                    return [
                        'name' => [
                            'required',
                            'string',
                            'max:191',
                            Rule::unique('roles', 'name')->ignore($role->id)->whereNull('deleted_at')
                        ]
                ];
                break;
                case 'PATCH': // for update method

                    $role = Role::where('slug', request()->slug)->first();
                    return [
                        'name' => [
                            'required',
                            'string',
                            'max:191',
                            Rule::unique('roles', 'name')->ignore($role->id)->whereNull('deleted_at')
                        ]
                ];
                break;
            default:
                return [];
                break;
        }
    }
}

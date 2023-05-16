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
        //redo
        return auth()->user() ? true : false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //redo all, this can be just a single line only!

        switch (strtoupper($this->method())) {
            case 'POST': // for store method
                return [
                    'title' => [
                        'required',
                        'string',
                        'max:191',
                        Rule::unique('roles', 'title')
                        ->where('source', auth()->user()->source)
                        ->where('business_id', auth()->user()->business_id)
                        ->whereNull('deleted_at')
                    ]
                ];
                break;
                case 'PUT': // for update method

                    $role = Role::where('slug', request()->slug)->first();
                    return [
                        'title' => [
                            'required',
                            'string',
                            'max:191',
                            Rule::unique('roles', 'title')->ignore($role->id)
                            ->where('source', auth()->user()->source)
                            ->where('business_id', auth()->user()->business_id)
                            ->whereNull('deleted_at')
                        ]
                ];
                break;
                case 'PATCH': // for update method

                    $role = Role::where('slug', request()->slug)->first();
                    return [
                        'title' => [
                            'required',
                            'string',
                            'max:191',
                            Rule::unique('roles', 'title')->ignore($role->id)
                            ->where('source', auth()->user()->source)
                            ->where('business_id', auth()->user()->business_id)
                            ->whereNull('deleted_at')
                        ]
                ];
                break;
            default:
                return [];
                break;
        }
    }
}

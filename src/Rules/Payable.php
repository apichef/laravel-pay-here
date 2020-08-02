<?php

namespace ApiChef\PayHere\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class Payable implements Rule
{
    public function passes($attribute, $value)
    {
        /** @var Builder $query */
        $query = call_user_func("{$value['type']}::query()");

        return $query->where('id', $value['id'])->exists();
    }

    public function message()
    {
        return 'Invalid payable.';
    }
}

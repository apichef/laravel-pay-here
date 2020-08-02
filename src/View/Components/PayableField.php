<?php

namespace ApiChef\PayHere\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class PayableField extends Component
{
    public string $type;

    public string $id;

    public function __construct(Model $payable)
    {
        $this->type = get_class($payable);
        $this->id = $payable->getKey();
    }

    public function render()
    {
        return view('pay-here::payable-field');
    }
}

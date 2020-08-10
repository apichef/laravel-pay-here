<?php

namespace ApiChef\PayHere\Tests\App;

use ApiChef\PayHere\Buyer;
use ApiChef\PayHere\Subscriber;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Buyer;
    use Subscriber;
}

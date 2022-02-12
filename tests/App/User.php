<?php

namespace ApiChef\PayHere\Tests\App;

use ApiChef\PayHere\Buyer;
use ApiChef\PayHere\Subscriber;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Buyer;
    use Subscriber;
    use HasFactory;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}

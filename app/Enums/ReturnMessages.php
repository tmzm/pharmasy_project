<?php

namespace App\Enums;

enum ReturnMessages : string
{
    case NotFound = 'We didnt found what you are looking for';
    case Error = 'Uncaught error happened';
    case ValidateError = 'Your inserts are not valid';
    case Ok = 'Your action has been done';
    case UnAuth = 'Unauthenticated';
}

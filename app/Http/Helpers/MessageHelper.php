<?php

namespace App\Http\Helpers;

use App\Enums\ReturnMessages;
use Illuminate\Http\Exceptions\HttpResponseException;

trait MessageHelper
{
    public function ok($data = null, $token = null)
    {
        throw new HttpResponseException(self::apiResponse(200,ReturnMessages::Ok->value,$data,$token));
    }

    public function unHandledError()
    {
        throw new HttpResponseException(self::apiResponse(500,ReturnMessages::Error->value));
    }

    public function unAuth()
    {
        throw new HttpResponseException(self::apiResponse(401,ReturnMessages::UnAuth->value));
    }

    public function notFound()
    {
        throw new HttpResponseException(self::apiResponse(404,ReturnMessages::NotFound->value));
    }
}

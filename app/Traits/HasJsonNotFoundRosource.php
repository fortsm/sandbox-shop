<?php

namespace App\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;

trait HasJsonNotFoundRosource
{
    /**
     * Handle a failed resource query.
     *
     * @param mixed $resource
     * @param mixed $resourceClass
     * @param string $subject
     * @return void
     * @throws HttpResponseException
     */
    public function checkFound(mixed $resource, string $resourceClass, string $subject): void
    {
        if (!($resource instanceof $resourceClass)) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => "$subject not found",
            ], 404));
        }
    }
}

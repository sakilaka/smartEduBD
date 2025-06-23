<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class PaymentException extends Exception
{
    protected $statusCode;
    protected $errors;

    public function __construct($message = "Payment Error", $statusCode = 0, Exception $previous = null, $errors = [])
    {
        $this->statusCode = $statusCode;
        $this->errors = $errors;
        parent::__construct($message, $statusCode, $previous);
    }

    /**
     * Render the exception for the API response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(Request $request)
    {
        return response()->json([
            'status' => 'error',
            'message' => $this->getMessage(),
            'errors' => $this->errors,
        ], $this->statusCode);
    }
}

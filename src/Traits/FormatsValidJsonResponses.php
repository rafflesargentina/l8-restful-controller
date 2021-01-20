<?php

namespace RafflesArgentina\RestfulController\Traits;

use Illuminate\Support\MessageBag;

trait FormatsValidJsonResponses
{
    /**
     * Return a valid 500 Internal Server Error json response.
     *
     * @param Exception $exception The exception object.
     * @param string    $message   The response message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validInternalServerErrorJsonResponse($exception, $message = 'Error')
    {
        return response()->json(
            [
                'exception' => class_basename($exception),
                'file' => basename($exception->getFile()),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ], 500, [], JSON_PRETTY_PRINT
        );
    }

    /**
     * Return a valid 404 Not found json response.
     *
     * @param string $message  The response message.
     * @param string $redirect The redirection url.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validNotFoundJsonResponse($message = 'Not found', $redirect = null)
    {
        return response()->json(
            [
                'code' => '404',
                'message' => $message,
                'errors' => [],
                'redirect' => $redirect,
            ], 404, [], JSON_PRETTY_PRINT
        );
    }

    /**
     * Return a valid 200 Success json response.
     *
     * @param string $message  The response message.
     * @param array  $data     The passed data.
     * @param string $redirect The redirection url.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validSuccessJsonResponse($message = 'Success', $data = [], $redirect = null)
    {
        return response()->json(
            [
                'code' => '200',
                'message' => $message,
                'data' => $data,
                'errors' => [],
                'redirect' => $redirect,
            ], 200, [], JSON_PRETTY_PRINT
        );
    }

    /**
     * Return a valid 422 Unprocessable entity json response.
     *
     * @param \Illuminate\Support\MessageBag $errors   The message bag errors.
     * @param string                         $message  The response message.
     * @param string                         $redirect The redirection url.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validUnprocessableEntityJsonResponse(MessageBag $errors, $message = 'Unprocessable Entity', $redirect = null)
    {
        return response()->json(
            [
                'code' => '422',
                'message' => $message,
                'errors' => $errors,
                'redirect' => $redirect,
            ], 422, [], JSON_PRETTY_PRINT
        );
    }
}

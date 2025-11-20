<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Success response
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function success($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Error response
     *
     * @param string $message
     * @param int $statusCode
     * @param string|null $errorCode
     * @param mixed $errors
     * @return JsonResponse
     */
    public static function error(
        string $message = 'Error',
        int $statusCode = 400,
        ?string $errorCode = null,
        $errors = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errorCode) {
            $response['error'] = [
                'code' => $errorCode
            ];
        }

        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Created response (201)
     *
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    public static function created($data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    /**
     * No content response (204)
     *
     * @return JsonResponse
     */
    public static function noContent(): JsonResponse
    {
        return response()->json([], 204);
    }

    /**
     * Accepted response (202) - for async operations
     *
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    public static function accepted($data = null, string $message = 'Request accepted for processing'): JsonResponse
    {
        return self::success($data, $message, 202);
    }

    /**
     * Bad request response (400)
     *
     * @param string $message
     * @param string|null $errorCode
     * @return JsonResponse
     */
    public static function badRequest(string $message = 'Bad request', ?string $errorCode = 'BAD_REQUEST'): JsonResponse
    {
        return self::error($message, 400, $errorCode);
    }

    /**
     * Unauthorized response (401)
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function unauthorized(string $message = 'Unauthenticated'): JsonResponse
    {
        return self::error($message, 401, 'UNAUTHENTICATED');
    }

    /**
     * Forbidden response (403)
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function forbidden(string $message = 'Access denied'): JsonResponse
    {
        return self::error($message, 403, 'FORBIDDEN');
    }

    /**
     * Not found response (404)
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::error($message, 404, 'NOT_FOUND');
    }

    /**
     * Validation error response (422)
     *
     * @param array $errors
     * @param string $message
     * @return JsonResponse
     */
    public static function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return self::error($message, 422, 'VALIDATION_ERROR', $errors);
    }

    /**
     * Server error response (500)
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function serverError(string $message = 'Internal server error'): JsonResponse
    {
        return self::error($message, 500, 'INTERNAL_ERROR');
    }

    /**
     * Too many requests response (429)
     *
     * @param string $message
     * @param int|null $retryAfter
     * @return JsonResponse
     */
    public static function tooManyRequests(string $message = 'Too many requests', ?int $retryAfter = null): JsonResponse
    {
        $response = response()->json([
            'success' => false,
            'message' => $message,
            'error' => [
                'code' => 'RATE_LIMIT_EXCEEDED',
                'retry_after' => $retryAfter
            ]
        ], 429);

        if ($retryAfter) {
            $response->header('Retry-After', $retryAfter);
        }

        return $response;
    }

    /**
     * Paginated response
     *
     * @param mixed $paginator
     * @param string $message
     * @return JsonResponse
     */
    public static function paginated($paginator, string $message = 'Data retrieved successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ]
        ], 200);
    }
}
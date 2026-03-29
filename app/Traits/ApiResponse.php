<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Send a successful response
     */
    public function success($data = null, $message = 'Succès', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Send a created response
     */
    public function created($data = null, $message = 'Ressource créée avec succès'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], 201);
    }

    /**
     * Send an error response
     */
    public function error($message = 'Erreur', $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Send a not found response
     */
    public function notFound($message = 'Ressource non trouvée'): JsonResponse
    {
        return $this->error($message, 404);
    }

    /**
     * Send an unauthorized response
     */
    public function unauthorized($message = 'Non autorisé'): JsonResponse
    {
        return $this->error($message, 401);
    }

    /**
     * Send a forbidden response
     */
    public function forbidden($message = 'Accès refusé'): JsonResponse
    {
        return $this->error($message, 403);
    }

    /**
     * Send a validation error response
     */
    public function validationError($errors): JsonResponse
    {
        return $this->error('Erreur de validation', 422, $errors);
    }

    /**
     * Send a resource already exists response
     */
    public function conflict($message = 'Ressource déjà existante'): JsonResponse
    {
        return $this->error($message, 409);
    }

    /**
     * Send a paginated response
     */
    public function paginated($data, $message = 'Succès', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
            ],
        ], $code);
    }
}

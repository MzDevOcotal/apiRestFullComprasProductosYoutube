<?php

namespace App\Http\Responses;

/**Clase para generar Respuesta */
class ApiResponse
{

    /**Método para enviar una respuestas Satisfactoria al Navegador */
    public static function success($message = 'Success', $statusCode = 200, $data = [])
    {
        return response()->json([
            'message' => $message,
            'statusCode' => $statusCode,
            'error' => false,
            'data' => $data
        ], $statusCode);
    }

    /**Método para enviar una respuestas de Error al Navegador */
    public static function error($message = 'Error', $statusCode, $data = [])
    {
        return response()->json([
            'message' => $message,
            'statusCode' => $statusCode,
            'error' => true,
            'data' => $data
        ], $statusCode);
    }
}

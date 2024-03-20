<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Validation\ValidationException;
use Exception;

class CategoriaController extends Controller
{
    public function index()
    {
        try {
            /**Llamado a las categorías. Línea 15 llama una función de Respuesta estructurada en el archivo ApiResponse.php */
            $categorias = Categoria::all();
            return ApiResponse::success('Categorias obtenidas', 200, $categorias);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            /**Validando los datos que llegan */
            $request->validate([
                'nombre' => 'required|unique:categorias ' //Se valida por que no puede ser null
            ]);

            /**Creando la categoría */
            $categoria = Categoria::create($request->all());
            /**Retornando Respuesta de Ingreso correcto de los datos */
            return ApiResponse::success('Categoría Creada Exitosamente', 201, $categoria);

        } catch(ValidationException $e) {
            return ApiResponse::error('Error de Validación: '.$e->getMessage(), 422);
        }
    }

    public function show($id)
    {
        # code
    }

    public function update(Request $request, $id)
    {
        # code
    }

    public function destroy($id)
    {
        # code
    }

    public function productosPorCategoria($id)
    {
        # code
    }
}

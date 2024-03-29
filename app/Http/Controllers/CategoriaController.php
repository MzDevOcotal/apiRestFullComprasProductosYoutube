<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Responses\ApiResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
                'nombre' => 'required|unique:categorias' //Se valida por que no puede ser null
            ]);

            /**Creando la categoría */
            $categoria = Categoria::create($request->all());
            /**Retornando Respuesta de Ingreso correcto de los datos */
            return ApiResponse::success('Categoría Creada Exitosamente', 201, $categoria);
        } catch (ValidationException $e) {
            /**Retornando Respuesta de Error de Validación */
            return ApiResponse::error('Error de Validación: ' . $e->getMessage(), 422);
        }
    }

    public function show($id)
    {
        try {
            $categoria = Categoria::findorFail($id);
            return ApiResponse::success('Categoría Obtenida Exitosamente', 200, $categoria);
        } catch (ModelNotFoundException $e) { //Excepción de un registro no encontrado en el Modelo
            return ApiResponse::error('Categoría No Encontrada | ' . $e->getMessage(), 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $categoria = Categoria::findorFail($id);

            /**Validando los datos que llegan */
            $request->validate([
                //Regla que permite ignorar el nombre actual, para que no se considere como duplicado
                'nombre' => ['required', Rule::unique('categorias')->ignore($categoria)]
            ]);
            /**Actualizando la categoría */
            $categoria->update($request->all());
            /**Retornando Respuesta de Actualización correcta de los datos */
            return ApiResponse::success('Categoría Actualizada Exitosamente', 200, $categoria);
        } catch (ModelNotFoundException $e) {
            //Excepción de un registro no encontrado en el Modelo
            return ApiResponse::error('Categoría No Encontrada', 404);
            //Excepción de un error de validación
        } catch (Exception $e) {
            return ApiResponse::error('Error: ' . $e->getMessage(), 422);
        }
    }

    public function destroy($id)
    {
        try {
            $categoria = Categoria::findorFail($id);
            $categoria->delete();
            return Apiresponse::success('Categoría Eliminada Existosamente', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoría No Encontrada', 404);
        }
    }

    public function productosPorCategoria($id)
    {
        # code
    }
}

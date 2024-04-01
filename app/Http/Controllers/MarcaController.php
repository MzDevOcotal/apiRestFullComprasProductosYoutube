<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Responses\ApiResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MarcaController extends Controller
{
    public function index()
    {
        try {
            $marcas = Marca::all();
            return ApiResponse::success('Marcas Obtenidas Correctamente', 200, $marcas);
        } catch (Exception $e) {
            return ApiResponse::error('Error al obtener las Marcas ', $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            //Se valida que el nombre no sea nullo y que sea único en la tabla marcas.
            $request->validate([
                'nombre' => 'required|unique:marcas'
            ]);
            //Se crea el registro en la tabla marcas y se almacena en la variable $marcas
            $marcas = Marca::create($request->all());
            //Se retorna la respuesta de la creación del registro
            return ApiResponse::success('Marca Creada Satisfactoriamente', 201, $marcas);
        } catch (ValidationException $e) {
            //Se retorna la respuesta de error de validación
            return ApiResponse::error('Error de Validación: ' . $e->getMessage(), 422);
        }
    }

    public function show($id)
    {
        try {
            $marcas = Marca::findorFail($id);
            return ApiResponse::success('Marca Obtenida Satisfactoriamente', 200, $marcas);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Marca No Encontrada', 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $marcas = Marca::findorFail($id);

            $request->validate([
                'nombre' => ['required', Rule::unique('marcas')->ignore($marcas)]
            ]);
            $marcas->update($request->all());
            return ApiResponse::success('Marca Actualizada Satisfactoriamente', 200, $marcas);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Marca No Encontrada', 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error al actualizar la Marca', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $marcas = Marca::findorFail($id);
            $marcas->delete();
            return ApiResponse::success('Marca Eliminada Satisfactoriamente', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Marca no Encontrada', 404);
        }
    }

    public function productosPorMarca($id)
    {
        try {
            $marca = Marca::with('productos')->findorFail($id);
            return ApiResponse::success('Productos de la Marca Obtenidos Correctamente', 200, $marca);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Marca no Encontrada', 404);
        }
    }
}

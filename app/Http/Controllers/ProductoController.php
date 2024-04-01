<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Responses\ApiResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductoController extends Controller
{
    public function index()
    {
        try {
            $productos = Producto::with('marca', 'categoria')->get();
            return ApiResponse::success('Productos Listados Correctamente', 200, $productos);
        } catch (Exception $e) {
            return ApiResponse::error('Error al listar los productos', 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|unique:productos', //Se valida por que no puede ser null o repetido
                'precio' => 'required|numeric|between:0,999999.99', //Se valida por que no puede ser null y debe ser un número
                'cantidad_disponible' => 'required|integer',
                'categoria_id' => 'required|exists:categorias,id', //Se valida por que no puede ser null y debe existir en la tabla categorias el id
                'marca_id' => 'required|exists:marcas,id' //Se valida por que no puede ser null y debe existir en la tabla marcas el id
            ]);
            $productos = Producto::create($request->all());
            return ApiResponse::success('Producto Creado Correctamente', 201, $productos);
        } catch (ValidationException $e) {
            $errores = $e->validator->errors()->toArray(); //Se obtienen los errores de validación

            /**CAMBIAR NOMBRE DEL CAMPO ERRADO EN REPORTE DE VALIDACIÓN */

            //Si existe el error de categoria_id, se cambia a categoria
            if (isset($errores['categoria_id'])) {
                $errores['categoria'] = $errores['categoria_id'];
                unset($errores['categoria_id']);
            }

            //Si existe el error de marca_id, se cambia a marca
            if (isset($errores['marca_id'])) {
                $errores['marca'] = $errores['marca_id'];
                unset($errores['marca_id']);
            }
            return ApiResponse::error('Error de Validación: ', 422, $errores);
        }
    }

    public function show($id)
    {
        try {
            //Se busca el producto por el id
            $productos = Producto::findorFail($id);
            /*Se retorna la respuesta con los datos de la marca y la categoría
            $productos = Producto::with('marca', 'categoria')->findorFail($id);*/
            return ApiResponse::success('Producto Obtenido Correctamente', 200, $productos);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Producto No Encontrado | ' . $e->getMessage(), 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $productos = Producto::findorFail($id);
            $request->validate([
                'nombre' => ['required', Rule::unique('productos')->ignore($productos)],
                'precio' => 'required|numeric|between:0,999999.99',
                'cantidad_disponible' => 'required|integer',
                'categoria_id' => 'required|exists:categorias,id',
                'marca_id' => 'required|exists:marcas,id'
            ]);
            $productos->update($request->all());
            return ApiResponse::success('Producto Actualizado Correctamente', 200, $productos);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Producto No Encontrado | ' . $e->getMessage(), 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error al actualizar el producto' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $productos = Producto::findorFail($id);
            $productos->delete();
            return ApiResponse::success('Producto Eliminado Correctamente', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Producto No Encontrado | ' . $e->getMessage(), 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error al eliminar el producto ' . $e->getMessage(), 500);
        }
    }
}

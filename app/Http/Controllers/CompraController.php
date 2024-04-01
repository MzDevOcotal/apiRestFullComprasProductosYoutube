<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Compra;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CompraController extends Controller
{
    public function index()
    {
        try {
            $listacompras = Compra::all(); //Se obtienen todas las compras
            return ApiResponse::success('Lista de compras', 200, $listacompras); //Se retorna un mensaje de éxito con la lista de compras
        } catch (Exception $e) {
            return ApiResponse::error('Error en la consulta: ' . $e->getMessage(), 500); //Se retorna un mensaje de error
        }
    }

    public function store(Request $request)
    {
        try {
            //Productos enviados de la compra en el request
            $productos = $request->input('productos'); //Array de productos que se envía en el request

            //Validando que se envíen productos y no venga vacío
            if (empty($productos)) { //Si no hay productos
                return ApiResponse::error('No se han enviado productos', 400);
            }

            //Validando que los productos sean un array
            $datosvalidos = Validator::make($request->all(), [
                'productos' => 'required|array', //Se valida que sea un array
                'productos.*.producto_id' => 'required|integer|exists:productos,id', //Se valida que sea un entero y que exista en la tabla productos
                'productos.*.cantidad' => 'required|integer|min:1' //Se valida que sea un entero y que sea mayor a 0
            ]);

            //Si los datos no son válidos
            if ($datosvalidos->fails()) {
                return ApiResponse::error('Datos no válidos en la lista de productos', 400, $datosvalidos->errors()); //Se retorna un error con los datos no válidos
            }

            //Validar que no existan productos duplicados
            $productosRepetidos = array_column($productos, 'producto_id'); //Se obtiene un array con los productos repetidos
            if (count($productosRepetidos) !== count(array_unique($productosRepetidos))) { //Si la cantidad de productos repetidos es diferente a la cantidad de productos únicos
                return ApiResponse::error('No se pueden enviar productos duplicados', 400);
            }

            //Inicializando variables para el cálculo de la compra
            $totalPagar = 0;
            $subtotal = 0;
            $compraItems = [];

            //Recorriendo los productos del request para calcular el total a pagar
            foreach ($productos as $producto) {
                $productoB = Producto::find($producto['producto_id']); //Se busca el producto en la base de datos a través del id
                //Validando si el id del producto anteriormente buscaso no existe
                if (!$productoB) {
                    return ApiResponse::error('Producto no encontrado', 404);
                }

                //Validando que la cantidad solicitada no sea mayor a la cantidad disponible
                if ($productoB->cantidad_disponible < $producto['cantidad']) {
                    return ApiResponse::error('No hay suficiente Stock del producto', 404);
                }

                //Actualizando la cantidad disponible en la tabla producto.
                $productoB->cantidad_disponible -= $producto['cantidad']; //Se resta la cantidad solicitada a la cantidad disponible
                $productoB->save(); //Se guarda la cantidad disponible

                //Calculando los Importes (Subtotal y Total a Pagar)
                $subtotal = $productoB->precio * $producto['cantidad']; //Se calcula el subtotal
                $totalPagar += $subtotal; //Se suma el subtotal al total a pagar

                //Se almacenan los datos de la compra en un array
                $compraItems[] = [
                    'producto_id' => $productoB->id,
                    'cantidad' => $producto['cantidad'],
                    'precio' => $productoB->precio,
                    'subtotal' => $subtotal
                ];
            } //Fin del foreach

            //Se crea la compra en la tabla compras
            $compra = Compra::create([
                'subtotal' => $totalPagar,
                'total' => $totalPagar
            ]);

            //Asociar los productos a la compra con sus cantidades y sus subtotales
            $compra->productos()->attach($compraItems); //Se asocian los productos a la compra
            return ApiResponse::success('Compra realizada con éxito', 201, $compra); //Se retorna un mensaje de éxito


        } catch (QueryException $e) { //Excepción de error en la consulta
            return ApiResponse::error('Error en la consulta: ' . $e->getMessage(), 500);
        } catch (Exception $e) { //Excepción de error general
            return ApiResponse::error('Error en la compra: ' . $e->getMessage(), 500);
        }
    }

    public function compraUnica($id)
    {
        try {
            $compra = Compra::findorFail($id); //Se busca la compra por el id
            if (!$compra) { //Si no existe la compra
                return ApiResponse::error('Compra no encontrada', 404);
            }
            return ApiResponse::success('Compra encontrada', 200, $compra); //Se retorna un mensaje de éxito con la compra
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Compra no encontrada', 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error en la consulta: ' . $e->getMessage(), 500);
        }
    }
}

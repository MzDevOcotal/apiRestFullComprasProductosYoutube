<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::all();
        return ApiResponse::success('Categorias obtenidas', 200, $categorias);
    }

    public function store(Request $request)
    {
        # code
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

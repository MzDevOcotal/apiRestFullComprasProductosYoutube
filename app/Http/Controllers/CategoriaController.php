<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        return Categoria::all();
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

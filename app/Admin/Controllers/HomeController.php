<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Programa;
use OpenAdmin\Admin\Layout\Content;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        // Obtener los programas desde la base de datos
        $programas = Programa::all();

        return $content
            ->title('Dashboard')
            ->description('Listado de Programas')
            ->view('admin.dashboard', ['programas' => $programas]);
    }

}

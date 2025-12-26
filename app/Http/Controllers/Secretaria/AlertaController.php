<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\Turma;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    public function index()
    {
        $alertas = Alerta::orderBy('created_at', 'desc')->get();
        return view('secretaria.alertas.index', compact('alertas'));
    }

    public function create()
    {
        $turmas = Turma::all();
        return view('secretaria.alertas.create', compact('turmas'));
    }

    public function store(Request $request)
    {
        $parametros = [];
        
        // Define parâmetros baseado no tipo de alerta
        switch ($request->tipo_alerta) {
            case 'faltas_consecutivas':
                $parametros['dias_consecutivos'] = $request->dias_consecutivos;
                break;
            case 'percentual_faltas':
                $parametros['percentual_limite'] = $request->percentual_limite;
                break;
            case 'dia_especifico':
                $parametros['data_especifica'] = $request->data_especifica;
                break;
        }

        Alerta::create([
            'tipo_alerta' => $request->tipo_alerta,
            'descricao' => $request->descricao,
            'parametros' => $parametros
        ]);
        
        return redirect()->route('secretaria.alertas.index')->with('success', 'Alerta criado com sucesso!');
    }

    public function destroy($id)
    {
        Alerta::findOrFail($id)->delete();
        return redirect()->route('secretaria.alertas.index');
    }
}

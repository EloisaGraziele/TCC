<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use App\Models\CalendarioEscolar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarioController extends Controller
{
    public function index()
    {
        // Mostrar apenas calendários ativos
        $calendarios = CalendarioEscolar::select('ano', 'ativo')
            ->where('ativo', true)
            ->distinct()
            ->orderBy('ano', 'desc')
            ->get()
            ->groupBy('ano')
            ->map(function($grupo) {
                return $grupo->first();
            });
            
        return view('secretaria.calendario.index', compact('calendarios'));
    }

    public function edit($ano)
    {
        // Mostrar apenas se o calendário estiver ativo
        $primeiroRegistro = CalendarioEscolar::where('ano', $ano)->where('ativo', true)->first();
        
        if (!$primeiroRegistro) {
            return redirect()->route('secretaria.calendario.index')
                ->with('error', 'Calendário não encontrado ou não está ativo.');
        }

        $calendario = CalendarioEscolar::where('ano', $ano)
            ->where('ativo', true)
            ->orderBy('data')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->data)->format('m');
            });

        return view('secretaria.calendario.edit', compact('calendario', 'ano'));
    }

    public function update(Request $request, $ano)
    {
        $validated = $request->validate([
            'eventos' => 'array',
            'eventos.*.data' => 'required|date',
            'eventos.*.tipo_dia' => 'required|in:letivo,feriado,sabado_letivo,ponto_facultativo,evento,reuniao,ferias',
            'eventos.*.descricao' => 'nullable|string|max:255'
        ]);

        $eventosProcessados = 0;
        if (isset($validated['eventos'])) {
            foreach ($validated['eventos'] as $evento) {
                $resultado = CalendarioEscolar::where('ano', $ano)
                    ->where('ativo', true)
                    ->where('data', $evento['data'])
                    ->update([
                        'tipo_dia' => $evento['tipo_dia'],
                        'descricao' => $evento['descricao'] ?: ($evento['tipo_dia'] === 'ferias' ? 'Férias' : null)
                    ]);
                
                if ($resultado) {
                    $eventosProcessados++;
                }
            }
        }

        return redirect()->back()->with('success', "Calendário atualizado com sucesso! {$eventosProcessados} evento(s) processado(s).");
    }
}

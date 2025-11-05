<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\CalendarioEscolar;
use Carbon\Carbon;

class CalendarioController extends Controller
{
    private function checkAuth()
    {
        if (!Session::get('admin_logged_in')) {
            return redirect()->route('admin.login');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        
        $calendarios = CalendarioEscolar::select('ano', 'ativo')
            ->distinct()
            ->orderBy('ano', 'desc')
            ->get()
            ->groupBy('ano')
            ->map(function($grupo) {
                return $grupo->first();
            });
            
        return view('admin.calendario-index', compact('calendarios'));
    }

    public function create()
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        
        return view('admin.calendario-create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $validated = $request->validate([
            'ano' => 'required|integer|min:2024|max:2030'
        ]);

        $ano = $validated['ano'];

        // Verificar se já existe calendário para este ano
        if (CalendarioEscolar::where('ano', $ano)->exists()) {
            return redirect()->back()->withErrors(['ano' => 'Calendário para o ano ' . $ano . ' já existe.']);
        }

        // Gerar todos os dias do ano
        $dataInicio = Carbon::createFromDate($ano, 1, 1);
        $dataFim = Carbon::createFromDate($ano, 12, 31);

        $diasParaInserir = [];
        $dataAtual = $dataInicio->copy();

        while ($dataAtual->lte($dataFim)) {
            // Determinar tipo do dia baseado no dia da semana
            $tipoDia = 'letivo';
            $descricao = null;
            
            if ($dataAtual->isSaturday()) {
                $tipoDia = 'sabado';
                $descricao = 'Sábado';
            } elseif ($dataAtual->isSunday()) {
                $tipoDia = 'domingo';
                $descricao = 'Domingo';
            }
            
            $diasParaInserir[] = [
                'ano' => $ano,
                'data' => $dataAtual->format('Y-m-d'),
                'tipo_dia' => $tipoDia,
                'descricao' => $descricao,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $dataAtual->addDay();
        }

        // Inserir dados com tratamento de erro
        try {
            foreach ($diasParaInserir as $dia) {
                CalendarioEscolar::create($dia);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['erro' => 'Erro ao criar calendário: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.calendario.index')
            ->with('success', 'Calendário base para ' . $ano . ' criado com sucesso! Agora você pode adicionar feriados e eventos.');
    }

    public function show($ano)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $calendario = CalendarioEscolar::where('ano', $ano)
            ->orderBy('data')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->data)->format('m');
            });

        return view('admin.calendario-show', compact('calendario', 'ano'));
    }

    public function edit($ano)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $calendario = CalendarioEscolar::where('ano', $ano)
            ->orderBy('data')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->data)->format('m');
            });

        return view('admin.calendario-edit', compact('calendario', 'ano'));
    }

    public function update(Request $request, $ano)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        // Debug: ver dados recebidos
        \Log::info('Dados recebidos:', $request->all());

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
                    ->where('data', $evento['data'])
                    ->update([
                        'tipo_dia' => $evento['tipo_dia'],
                        'descricao' => $evento['descricao'] ?: ($evento['tipo_dia'] === 'ferias' ? 'Férias' : null)
                    ]);
                
                if ($resultado) {
                    $eventosProcessados++;
                }
                
                \Log::info('Evento processado:', [
                    'data' => $evento['data'],
                    'tipo' => $evento['tipo_dia'],
                    'descricao' => $evento['descricao'],
                    'resultado' => $resultado
                ]);
            }
        }

        return redirect()->back()->with('success', "Calendário atualizado com sucesso! {$eventosProcessados} evento(s) processado(s).");
    }

    public function destroy($ano)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        CalendarioEscolar::where('ano', $ano)->delete();

        return redirect()->route('admin.calendario.index')
            ->with('success', 'Calendário ' . $ano . ' deletado com sucesso!');
    }

    public function updateStatus(Request $request, $ano)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $validated = $request->validate([
            'status' => 'required|boolean'
        ]);

        CalendarioEscolar::where('ano', $ano)->update([
            'ativo' => $validated['status']
        ]);

        $statusTexto = $validated['status'] ? 'ativado' : 'desativado';
        
        return redirect()->route('admin.calendario.index')
            ->with('success', 'Calendário ' . $ano . ' ' . $statusTexto . ' com sucesso!');
    }
}
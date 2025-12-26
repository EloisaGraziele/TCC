<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Turma;

class TurmaController extends Controller
{
    /**
     * Exibe a lista de turmas.
     */
    public function index()
    {
        return view('secretaria.turmas.index');
    }

    /**
     * Exibe o formulário de criação de turma.
     */
    public function create()
    {
        return view('secretaria.turmas.create');
    }

    /**
     * Armazena uma nova turma.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ano' => 'required|string|max:255',
            'turma' => 'required|string|max:255',
        ]);

        $turma = Turma::create($validated);
        return redirect()->route('secretaria.gerenciamento.index')->with('success', 'Turma cadastrada com sucesso!');
    }

    /**
     * Exibe uma turma específica.
     */
    public function show($id)
    {
        return view('secretaria.turmas.show');
    }

    /**
     * Exibe o formulário de edição de turma.
     */
    public function edit($id)
    {
        return view('secretaria.turmas.edit');
    }

    /**
     * Atualiza uma turma.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'ano' => 'required|string|max:255',
            'turma' => 'required|string|max:255',
        ]);
        
        $turma = Turma::findOrFail($id);
        $turma->update($validated);
        
        return redirect()->route('secretaria.gerenciamento.index')->with('success', 'Turma atualizada com sucesso!');
    }

    /**
     * Remove uma turma.
     */
    public function destroy($id)
    {
        $turma = Turma::findOrFail($id); $turma->delete();
        return redirect()->route('secretaria.gerenciamento.index')->with('success', 'Turma excluída com sucesso!');
    }
}
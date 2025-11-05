<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispositivo;

class DispositivoController extends Controller
{
    /**
     * Exibe a lista de dispositivos.
     */
    public function index()
    {
        return view('secretaria.dispositivos.index');
    }

    /**
     * Exibe o formulário de criação de dispositivo.
     */
    public function create()
    {
        return view('secretaria.dispositivos.create');
    }

    /**
     * Armazena um novo dispositivo.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mac_address' => 'required|string|max:255',
            'autorizado' => 'boolean',
        ]);

        $dispositivo = Dispositivo::create($validated);
        return redirect()->route('secretaria.gerenciamento.index')->with('success', 'Dispositivo cadastrado com sucesso!');
    }

    /**
     * Exibe um dispositivo específico.
     */
    public function show($id)
    {
        return view('secretaria.dispositivos.show');
    }

    /**
     * Exibe o formulário de edição de dispositivo.
     */
    public function edit(Dispositivo $dispositivo)
    {
        // Não usado - edição via modal
        return redirect()->route('secretaria.gerenciamento.index');
    }

    /**
     * Atualiza um dispositivo.
     */
    public function update(Request $request, Dispositivo $dispositivo)
    {
        $validated = $request->validate([
            'mac_address' => 'required|string|max:255',
            'autorizado' => 'boolean',
        ]);

        $dispositivo->update($validated);
        return redirect()->route('secretaria.gerenciamento.index')->with('success', 'Dispositivo atualizado com sucesso!');
    }

    /**
     * Remove um dispositivo.
     */
    public function destroy(Dispositivo $dispositivo)
    {
        $dispositivo->delete();
        return redirect()->route('secretaria.gerenciamento.index')->with('success', 'Dispositivo excluído com sucesso!');
    }
}
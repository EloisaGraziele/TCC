<style>
@media print {
    /* Oculta tudo exceto o crachá */
    body * {
        visibility: hidden;
    }
    
    #cracha-print, #cracha-print * {
        visibility: visible;
    }
    
    #cracha-print {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    body {
        background: white !important;
        margin: 0 !important;
        padding: 0 !important;
    }
}
</style>

@extends('layouts.admin')

@section('content')
<div class="container py-5">

    <!-- Mensagem de sucesso -->
    @if (session('success'))
        <div class="alert alert-success text-center fw-bold rounded-3 mb-4 no-print">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-center">
        <div id="cracha-print" class="card shadow-lg border-0" 
             style="width: 400px; height: 550px; border-radius: 20px; background-color: #ffffff; position: relative; overflow: hidden;">

            <!-- Faixa superior -->
            <div style="background-color: #55B4F8; height: 80px; display: flex; align-items: center; justify-content: center;">
                <h4 class="text-white fw-bold mb-0">Crachá de Identificação</h4>
            </div>

            <!-- Corpo -->
            <div class="text-center p-4 d-flex flex-column justify-content-center" style="height: 390px;">
                <!-- QR Code -->
                <div class="mb-3">
                    {!! $qrCodeSvg !!}
                </div>
                
                <!-- Texto informativo -->
                <p class="text-muted mb-0" style="font-size: 14px;">Escaneie para registrar presença</p>
            </div>

            <!-- Rodapé -->
            <div class="text-center" style="background-color: #55B4F8; height: 60px; display: flex; align-items: center; justify-content: center;">
                <p class="text-white mb-0">Sistema de Presença Escolar</p>
            </div>
        </div>
    </div>

    <!-- Botões -->
    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-success px-4 py-2 fw-bold rounded-3 me-2">Imprimir</button>
        <button onclick="salvarPDF()" class="btn btn-primary px-4 py-2 fw-bold rounded-3 me-2">Salvar PDF</button>
        <a href="{{ route('secretaria.aluno.create') }}" class="btn btn-warning px-4 py-2 fw-bold rounded-3">Voltar</a>
    </div>

    <script>
        function salvarPDF() {
            alert('Para salvar em PDF:\n1. Clique em "Imprimir"\n2. Escolha "Salvar como PDF" como destino\n3. Clique em "Salvar"');
            window.print();
        }
    </script>

</div>
@endsection
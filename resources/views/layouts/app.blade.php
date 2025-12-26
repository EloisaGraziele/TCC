<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Presença - Login</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <meta name="csrf-token" content="{{ csrf_token() }}"> </head>
<body class="min-h-screen bg-gray-100 flex">
    
    <div class="flex-1 bg-blue-900 text-white flex items-center justify-center">
        <h1 class="text-6xl font-extrabold text-center leading-tight">
            <span class="text-blue-300">SISTEMA</span>
            <span class="text-green-400"> DE PRESENÇA</span>
        </h1>
    </div>

    <div class="flex-1 flex flex-col justify-center items-center bg-gray-100 relative">

        <div class="absolute top-0 left-0 w-full bg-green-500 p-4 flex justify-end shadow-md">
            <button class="bg-white text-green-500 font-bold px-4 py-2 rounded hover:bg-gray-100">
                Assistência
            </button>
        </div>

        <div class="bg-white shadow-lg rounded-lg w-full max-w-md p-8 z-10 mt-16">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Login do Usuário</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400" required autofocus>
                </div>
                
                </form>
        </div>
    </div>
</body>
</html>
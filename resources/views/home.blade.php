@extends('layouts.public')

@section('title', 'NSINC - Inicio')

@section('content')
<br> <br> <br> 
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-center min-h-[60vh]">
            <div class="text-center">
                <div class="mb-10">
                    <div class="flex justify-center mb-6">
                    
                        <img src="/assets/escudo.png" alt="Estados Unidos Mexicanos" />
                    </div>
                    <h1 class="text-4xl font-bold text-burgundy mb-4">
                        Nuevo  <br> 
                        Sistema de Información de Normatividad de Comunicación
                    </h1>
                    <p class="text-xl text-gray-600 mb-8">
                        B I E N V E N I D O S
                    </p>
                </div>

                <div class="flex justify-center">
                    <a
                        href="{{ route('filament.admin.auth.login') }}"
                        class="inline-block px-8 py-4 text-lg font-semibold text-white transition-all duration-300 bg-burgundy rounded-lg hover:bg-opacity-90 hover:scale-110 hover:-translate-y-1 shadow-lg hover:shadow-xl"
                    >
                        Entrar
                    </a>
                </div>

               
            </div>
        </div>
    </div>
@endsection

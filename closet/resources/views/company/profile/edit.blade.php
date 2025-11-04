<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("Editar Perfil da Empresa") }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route("company.profile.update") }}" enctype="multipart/form-data">
                        @csrf
                        @method("PUT")

                        <!-- Nome da Empresa -->
                        <div>
                            <x-label for="name" :value="__("Nome da Empresa")" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old("name", $company->name)" required autofocus />
                            @error("name")
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Descrição (Bio) -->
                        <div class="mt-4">
                            <x-label for="description" :value="__("Descrição")" />
                            <textarea id="description" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="description" rows="5">{{ old("description", $company->description) }}</textarea>
                            @error("description")
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Logo da Empresa -->
                        <div class="mt-4">
                            <x-label for="logo" :value="__("Logo da Empresa")" />
                            <input id="logo" class="block mt-1 w-full" type="file" name="logo" />
                            @if ($company->logo)
                                <img src="{{ Storage::url($company->logo) }}" alt="Logo da Empresa" class="mt-2 h-20 w-20 object-cover rounded-full">
                            @endif
                            @error("logo")
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __("Salvar") }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


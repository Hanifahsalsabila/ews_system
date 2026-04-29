<x-bidan-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pencatatan Pemeriksaan Rutin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(!$sesiAktif)
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            Tidak ada sesi Posyandu yang sedang berjalan. Hubungi Admin atau Kader untuk membuka sesi baru.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Ibu Hamil -->
                <a href="{{ route('bidan.pemeriksaan.ibu-hamil') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 hover:border-pink-300 hover:shadow-md transition-all">
                    <div class="p-8 flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 bg-pink-100 text-pink-600 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Pemeriksaan Ibu Hamil</h3>
                        <p class="text-gray-500">Input Berat Badan, Tekanan Darah, LILA, TFU, DJJ, dll.</p>
                    </div>
                </a>

                <!-- Balita -->
                <a href="{{ route('bidan.pemeriksaan.balita') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all">
                    <div class="p-8 flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Pemeriksaan Anak/Balita</h3>
                        <p class="text-gray-500">Input Tinggi Badan, Berat Badan, Lingkar Kepala & Imunisasi.</p>
                    </div>
                </a>
            </div>

        </div>
    </div>
</x-bidan-layout>

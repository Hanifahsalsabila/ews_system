<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Kader Posyandu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Selamat datang, Kader Posyandu!</h3>
                    <p class="text-gray-600 mb-6">Pilih menu navigasi di atas untuk memulai operasional seperti pendaftaran Sesi, Presensi Warga, atau Input 5 Meja.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="#" class="block p-4 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition">
                            <h4 class="font-bold text-emerald-800 text-md">Buka Sesi Baru</h4>
                            <p class="text-sm text-emerald-600 mt-1">Buat jadwal posyandu bulan ini.</p>
                        </a>
                        <a href="#" class="block p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                            <h4 class="font-bold text-blue-800 text-md">Absensi Kehadiran</h4>
                            <p class="text-sm text-blue-600 mt-1">Catat warga yang datang ke posyandu.</p>
                        </a>
                        <a href="#" class="block p-4 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition">
                            <h4 class="font-bold text-purple-800 text-md">Input 5 Meja</h4>
                            <p class="text-sm text-purple-600 mt-1">Masukkan hasil ukur balita & stunting.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

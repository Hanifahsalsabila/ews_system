<x-kader-layout>
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-emerald-600 mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                <span class="font-bold text-sm tracking-widest uppercase">Meja 1</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Absensi Kehadiran</h1>
            <p class="text-slate-500 mt-1 font-medium">Scan QR Code warga atau masukkan NIK secara manual.</p>
        </div>
        <div>
            <!-- Button removed due to Kader restricted access -->
        </div>
    </div>

    @if(!$activeSession)
        <div class="bg-rose-50 border border-rose-200 rounded-3xl p-8 text-center">
            <h2 class="text-xl font-bold text-rose-800 mb-2">Tidak Ada Sesi Aktif</h2>
            <p class="text-rose-600 font-medium">Anda tidak dapat menerima pendaftaran. Harap hubungi Admin atau Bidan untuk membuka sesi posyandu hari ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
            
            <!-- Scanner Area -->
            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                    <!-- Scanner Container -->
                    <div id="reader-container" class="rounded-2xl overflow-hidden bg-slate-900 border-4 border-slate-100 mb-6 relative">
                        <div id="reader" width="100%"></div>
                    </div>

                    <div class="flex justify-center mb-6">
                        <button id="toggle-camera" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span id="camera-btn-text">Aktifkan Kamera Scanner</span>
                        </button>
                    </div>

                    <div class="relative flex py-4 items-center">
                        <div class="flex-grow border-t border-slate-200"></div>
                        <span class="flex-shrink-0 mx-4 text-slate-400 font-bold text-sm">ATAU MANUAL</span>
                        <div class="flex-grow border-t border-slate-200"></div>
                    </div>

                    <form action="{{ route('kader.pendaftaran.store') }}" method="POST">
                        @csrf
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Input NIK Warga</label>
                            <div class="flex gap-3">
                                <input type="text" name="nik" required placeholder="Masukkan 16 digit NIK..." class="flex-1 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-medium text-sm">
                                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 px-6 rounded-xl transition shadow-lg shadow-slate-900/20 whitespace-nowrap">
                                    Catat Hadir
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Scans List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm h-full">
                    <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center justify-between">
                        Terakhir Hadir
                        <span class="bg-emerald-100 text-emerald-700 py-1 px-3 rounded-lg text-xs tracking-widest">{{ $recentAbsensi->count() }} Orang</span>
                    </h3>

                    @if($recentAbsensi->isEmpty())
                        <div class="text-center py-10">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                            <p class="text-slate-500 font-medium">Belum ada warga yang terdaftar hadir pada sesi ini.</p>
                        </div>
                    @else
                        <div class="space-y-4" id="recent-list">
                            @foreach($recentAbsensi as $absen)
                                <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 animate-fade-in">
                                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center font-bold">
                                        {{ substr($absen->warga->nama, 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-slate-800 truncate">{{ $absen->warga->nama }}</p>
                                        <p class="text-xs text-slate-500 font-medium font-mono mt-0.5">{{ $absen->warga->nik }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $absen->created_at->format('H:i') }}</p>
                                        <p class="text-xs font-bold text-emerald-600 mt-0.5">Hadir</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <x-slot name="scripts">
            <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    let html5QrcodeScanner = null;
                    const toggleBtn = document.getElementById('toggle-camera');
                    const btnText = document.getElementById('camera-btn-text');
                    let isScanning = false;

                    const sessionId = "{{ $activeSession->id }}";
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    function onScanSuccess(decodedText, decodedResult) {
                        // Pause scanning to prevent multiple fast requests
                        if(html5QrcodeScanner) {
                            html5QrcodeScanner.pause();
                        }

                        // Extract the encrypted token from the QR URL.
                        // QR codes now contain URLs like: http://domain.com/v/ENCRYPTED_TOKEN
                        let nikOrToken = decodedText;
                        if (decodedText.includes('/v/')) {
                            nikOrToken = decodedText.split('/v/')[1];
                        }
                        
                        // Send to server
                        fetch("{{ route('kader.pendaftaran.scan') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken,
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({
                                nik: nikOrToken,
                                sesi_id: sessionId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                alert('✅ ' + data.message);
                                window.location.reload();
                            } else {
                                alert('⚠️ ' + data.message);
                                if(html5QrcodeScanner) html5QrcodeScanner.resume();
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            alert("Terjadi kesalahan sistem saat memproses QR Code.");
                            if(html5QrcodeScanner) html5QrcodeScanner.resume();
                        });
                    }

                    function startScanner() {
                        html5QrcodeScanner = new Html5QrcodeScanner(
                            "reader", 
                            { fps: 10, qrbox: {width: 250, height: 250}, aspectRatio: 1.0 }
                        );
                        html5QrcodeScanner.render(onScanSuccess);
                        isScanning = true;
                        btnText.innerText = "Matikan Kamera";
                        toggleBtn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                        toggleBtn.classList.add('bg-rose-600', 'hover:bg-rose-700');
                    }

                    function stopScanner() {
                        if (html5QrcodeScanner) {
                            html5QrcodeScanner.clear().then(() => {
                                isScanning = false;
                                btnText.innerText = "Aktifkan Kamera Scanner";
                                toggleBtn.classList.remove('bg-rose-600', 'hover:bg-rose-700');
                                toggleBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
                            }).catch(error => {
                                console.error("Failed to clear scanner. ", error);
                            });
                        }
                    }

                    toggleBtn.addEventListener('click', () => {
                        if (isScanning) {
                            stopScanner();
                        } else {
                            startScanner();
                        }
                    });
                });
            </script>
            <style>
                /* Overrides for html5-qrcode styles */
                #reader button {
                    background-color: #f8fafc;
                    border: 1px solid #cbd5e1;
                    padding: 8px 16px;
                    border-radius: 8px;
                    font-weight: 600;
                    color: #334155;
                    cursor: pointer;
                    margin: 4px;
                }
                #reader a { color: #10b981; }
                #reader select {
                    background-color: #f8fafc;
                    border: 1px solid #cbd5e1;
                    padding: 8px;
                    border-radius: 8px;
                    margin-bottom: 10px;
                }
            </style>
        </x-slot>
    @endif
</x-kader-layout>

<div id="pkCheckoutModal"
     class="no-print fixed inset-0 z-50 hidden items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="pkCheckoutTitle">
    <div id="pkCheckoutBackdrop"
         class="fixed inset-0 bg-[#0F0F10]/45 backdrop-blur-[2px] transition-opacity duration-200 opacity-0"></div>

    <div id="pkCheckoutPanel"
         class="relative w-full max-w-[400px] bg-white rounded-[26px] overflow-hidden shadow-[0_24px_48px_-12px_rgba(15,15,16,0.28)] border border-[#E5E7EB] transition-all duration-200 opacity-0 translate-y-2 scale-95">
        <div class="bg-[#0F0F10] text-white px-5 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-full bg-white text-[#0F0F10] flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                    </svg>
                </span>
                <span id="pkCheckoutTitle" class="text-xs font-bold tracking-tight">Konfirmasi Parkir Keluar</span>
            </div>
            <div class="flex items-center gap-2">
                <span id="pkCoId" class="rounded-full bg-white/15 px-2.5 py-0.5 font-mono text-[11px] font-semibold">#0</span>
                <button type="button" id="pkCoCloseX" aria-label="Tutup"
                        class="w-7 h-7 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="flex-1 relative bg-white border-2 border-[#111827] rounded-xl py-2.5 px-5 text-center shadow-[0_2px_6px_rgba(15,15,16,0.06)]">
                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-[#D1D5DB]"></span>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-[#D1D5DB]"></span>
                    <span id="pkCoPlat" class="text-base font-extrabold tracking-widest text-[#111827] uppercase">-</span>
                </div>
                <span id="pkCoJenis" class="shrink-0 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 text-xs font-semibold capitalize">
                    Motor
                </span>
            </div>

            <div class="rounded-2xl bg-[#FAFAFA] border border-[#E5E7EB]/80 p-4 text-xs space-y-2">
                <div class="flex justify-between gap-2">
                    <span class="text-[#6B7280]">Area Parkir</span>
                    <span id="pkCoArea" class="font-medium text-[#111827] text-right">-</span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-[#6B7280]">Waktu Masuk</span>
                    <span id="pkCoMasuk" class="font-medium text-[#111827] tabular-nums text-right">-</span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-[#6B7280]">Estimasi Durasi</span>
                    <span id="pkCoDurasi" class="font-semibold text-[#111827] tabular-nums text-right">1 jam</span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-[#6B7280]">Tarif / Jam</span>
                    <span id="pkCoTarif" class="font-medium text-[#111827] tabular-nums text-right">Rp 0</span>
                </div>

                <div class="pt-2.5 border-t border-dashed border-[#D1D5DB] flex items-center justify-between">
                    <span class="font-semibold text-[#111827]">Estimasi Total</span>
                    <span id="pkCoTotal" class="text-base font-extrabold text-[#0F0F10] tabular-nums">Rp 0</span>
                </div>
            </div>

            <form id="pkCoForm" method="POST" action="#" class="flex items-center justify-end gap-2 pt-0.5">
                @csrf
                <button type="button" id="pkCoCancel"
                        class="rounded-full border border-[#D1D5DB] bg-white px-4 py-2 text-xs font-medium text-[#111827] hover:border-[#0F0F10] transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="rounded-full bg-[#0F0F10] text-white px-5 py-2 text-xs font-semibold hover:bg-[#262626] transition-colors shadow-pill">
                    Konfirmasi &amp; Cetak Struk
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('pkCheckoutModal');
        if (!modal) return;

        const backdrop = document.getElementById('pkCheckoutBackdrop');
        const panel = document.getElementById('pkCheckoutPanel');
        const form = document.getElementById('pkCoForm');

        const elId = document.getElementById('pkCoId');
        const elPlat = document.getElementById('pkCoPlat');
        const elJenis = document.getElementById('pkCoJenis');
        const elArea = document.getElementById('pkCoArea');
        const elMasuk = document.getElementById('pkCoMasuk');
        const elDurasi = document.getElementById('pkCoDurasi');
        const elTarif = document.getElementById('pkCoTarif');
        const elTotal = document.getElementById('pkCoTotal');

        const badgeClasses = {
            motor: 'shrink-0 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 text-xs font-semibold capitalize',
            mobil: 'shrink-0 rounded-full bg-sky-50 text-sky-700 border border-sky-200 px-3 py-1 text-xs font-semibold capitalize',
            lainnya: 'shrink-0 rounded-full bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1 text-xs font-semibold capitalize',
        };

        const formatRp = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');

        function openCheckoutModal(btn) {
            const action = btn.getAttribute('data-action');
            const id = btn.getAttribute('data-id') || '0';
            const plat = btn.getAttribute('data-plat') || '-';
            const jenis = (btn.getAttribute('data-jenis') || 'lainnya').toLowerCase();
            const area = btn.getAttribute('data-area') || '-';
            const tarif = Number(btn.getAttribute('data-tarif') || 0);
            const masukLabel = btn.getAttribute('data-masuk-label') || '-';
            const masukIso = btn.getAttribute('data-masuk-iso');

            let hours = 1;
            if (masukIso) {
                const diffMins = Math.max(0, Math.floor((Date.now() - new Date(masukIso).getTime()) / 60000));
                hours = Math.max(1, Math.ceil(diffMins / 60));
            }

            form.action = action;
            elId.textContent = '#' + id;
            elPlat.textContent = plat;
            elJenis.textContent = jenis;
            elJenis.className = badgeClasses[jenis] || badgeClasses.lainnya;
            elArea.textContent = area;
            elMasuk.textContent = masukLabel;
            elDurasi.textContent = hours + ' jam (min. 1 jam)';
            elTarif.textContent = formatRp(tarif);
            elTotal.textContent = formatRp(hours * tarif);

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('opacity-0', 'translate-y-2', 'scale-95');
            });
        }

        function closeCheckoutModal() {
            backdrop.classList.add('opacity-0');
            panel.classList.add('opacity-0', 'translate-y-2', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 180);
        }

        document.querySelectorAll('[data-checkout-trigger]').forEach((btn) => {
            btn.addEventListener('click', () => openCheckoutModal(btn));
        });

        document.getElementById('pkCoCancel').addEventListener('click', closeCheckoutModal);
        document.getElementById('pkCoCloseX').addEventListener('click', closeCheckoutModal);
        backdrop.addEventListener('click', closeCheckoutModal);
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeCheckoutModal();
            }
        });
    });
</script>
@endpush

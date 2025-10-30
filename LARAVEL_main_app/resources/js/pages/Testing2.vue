<script setup lang="ts">
import { useDefineProps } from '@/composables/useAppLayout';
import { useLayout } from '@/composables/useLayout';
import { initializedThemeStore } from '@/composables/usePinia';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import html2pdf from 'html2pdf.js';
import Badge from 'volt-components/Badge.vue';
import { formatDecimal, parseDate } from '@/composables/Helper';

const props = defineProps({
    ...useDefineProps('Testing2'),
});
const { isDarkMode, toggleDarkMode } = useLayout();
initializedThemeStore().initializeTheme();
// VARIABLE SECTIONS
const showDarkButton = ref(false);

// FUNCTION SECTIONS
function toggleButton(){
    toggleDarkMode();
    toggleDarkButton();
}
function toggleDarkButton(){
    showDarkButton.value = !showDarkButton.value
}

function printPDF(){
    const element = document.getElementById('containerPDF');
    setTimeout(() => {
        html2pdf(element, {
            margin:       10,
            filename:     'traceProduct.pdf',
            image:        { type: 'jpeg', quality: 1 },
            html2canvas:  {
                scale: 2,
                scrollY: 0,
                useCORS: true
            },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        });
    }, 500);

}

</script>
<template>
    <Head :title="props.title" />
    <div class="fixed -left-1 top-20">
        <div class="bg-surface-100 hover:bg-surface-300 dark:hover:bg-surface-700 dark:bg-surface-900 border border-surface shadow rounded-r-full py-2 px-1 flex items-center cursor-pointer" @click="toggleDarkButton">
            <i class="pi pi-chevron-right"></i>
        </div>
    </div>
    <div class="fixed transition-all duration-300 ease-in-out top-5" :class="{ '': showDarkButton, '-translate-x-full': !showDarkButton }">
        <button
            type="button"
            class="w-15 h-15 border-1 border-surface-300 dark:border-surface-800 bg-surface-100 hover:bg-surface-300 dark:bg-surface-900 dark:hover:bg-surface-700 cursor-pointer flex items-center justify-center rounded-full transition-all text-surface-900 dark:text-surface-0 focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-surface-0 dark:focus-visible:ring-offset-surface-950"
            @click="toggleButton"
        >
            <i style="font-size: 1.3rem" :class="['pi text-base', { 'pi-moon': isDarkMode, 'pi-sun': !isDarkMode }]" />
        </button>
    </div>

    <main id="containerPDF" class="min-w-full min-h-screen main-container rounded-none">
        <Badge class="flex items-center gap-1 cursor-pointer" @click="printPDF" data-html2canvas-ignore>
            <i class="pi pi-file"></i>
            <span>PDF</span>
        </Badge>
        <div class="flex flex-col gap-3">
            <div class="flex-1 border-b-2 border-surface py-2">
                <h1 class="text-3xl font-bold">TRACE PRODUCT</h1>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="flex-1 flex flex-col justify-start gap-3">
                    <div class="flex-1 flex flex-col gap-2 p-3 border border-surface rounded">
                        <span class="w-fit text-xl font-bold border-b-2 border-surface">Trace</span>
                        <!-- <div class="flex gap-4 items-center">
                            <span class="min-w-[100px] font-medium">Gambar</span>
                            <span>:</span>
                            <span><img src="storage/images/master_produk/1747811176_2_products.png" class="max-w-30" alt="sertifikasi"></span>
                        </div> -->
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[120px] font-medium">QR Code</span>
                            <span>:</span>
                            <span><img src="storage/images/produksi/1747851256_PRD2505220114242_42_qr.jpg" alt="QR Code" class="max-w-20 shadow"></span>
                        </div>
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[120px] font-medium">Gambar Produk</span>
                            <span>:</span>
                            <span><img src="storage/images/master_produk/1747811176_2_products.png" alt="Gambar Produk" class="max-w-20 shadow"></span>
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col gap-2 p-3 border border-surface rounded">
                        <span class="w-fit text-xl font-bold border-b-2 border-surface">Produk</span>
                        <!-- <div class="flex gap-4 items-center">
                            <span class="min-w-[100px] font-medium">Gambar</span>
                            <span>:</span>
                            <span><img src="storage/images/master_produk/1747811176_2_products.png" class="max-w-30" alt="sertifikasi"></span>
                        </div> -->
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[100px] font-medium">Kode Produk</span>
                            <span>:</span>
                            <span>PK001</span>
                        </div>
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[100px] font-medium">Nama Produk</span>
                            <span>:</span>
                            <span>Kunyit Asam</span>
                        </div>
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[100px] font-medium">Kategori</span>
                            <span>:</span>
                            <span>Jamu</span>
                        </div>
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[100px] font-medium">Sertifikasi</span>
                            <span>:</span>
                            <span><img src="storage/images/sertifikasi/1746952053_1_sertifikasi.png" class="max-w-10" alt="sertifikasi"></span>
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col gap-2 p-3 border border-surface rounded">
                        <span class="w-fit text-xl font-bold border-b-2 border-surface">Distribusi</span>
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[150px] font-medium">Kode Distribusi</span>
                            <span>:</span>
                            <span>DIST1203921903</span>
                        </div>
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[150px] font-medium">Nama Retailer</span>
                            <span>:</span>
                            <span>TOKO JAYA</span>
                        </div>
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[150px] font-medium">Tanggal Pengiriman</span>
                            <span>:</span>
                            <span>{{ parseDate('2023-01-01', 'DD MMM YYYY') }}</span>
                        </div>
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[150px] font-medium">Alamat</span>
                            <span>:</span>
                            <span>alamat</span>
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col gap-2 p-3 border border-surface rounded">
                        <span class="w-fit text-xl font-bold border-b-2 border-surface">Produksi</span>
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[145px] font-medium">Nomor Batch</span>
                            <span>:</span>
                            <span class="wrap-break-word">PRD120938190283</span>
                        </div>
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[145px] font-medium">Mulai Produksi</span>
                            <span>:</span>
                            <span>{{ parseDate('2023-01-01', 'DD MMM YYYY') }}</span>
                        </div>
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[145px] font-medium">Selesai Produksi</span>
                            <span>:</span>
                            <span>{{ parseDate('2023-01-01', 'DD MMM YYYY') }}</span>
                        </div>
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[145px] font-medium">Tanggal Kadaluarsa</span>
                            <span>:</span>
                            <span>{{ parseDate('2023-01-01', 'DD MMM YYYY') }}</span>
                        </div>
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[145px] font-medium">Jumlah Produksi</span>
                            <span>:</span>
                            <span>123</span>
                        </div>
                        <div class="flex gap-4 items-center">
                            <span class="min-w-[145px] font-medium">Diproduksi Oleh</span>
                            <span>:</span>
                            <span>Ngetes</span>
                        </div>
                    </div>
                </div>
                <div class="flex-1 flex flex-col justify-start gap-3">
                    <div class="flex-1 flex flex-col gap-2 p-3 border border-surface rounded">
                        <span class="w-fit text-xl font-bold border-b-2 border-surface">Bahan Baku</span>
                        <!-- foreach here -->
                        <div>
                            <span class=" font-bold">1. Kunyit</span>
                            <div class="pl-[1.1rem]">
                                <div class="flex gap-4 items-center">
                                    <span class="min-w-[150px] font-medium">Kode LOT</span>
                                    <span>:</span>
                                    <span>Kunyit Asam</span>
                                </div>
                                <div class="flex gap-4 items-center">
                                    <span class="min-w-[150px] font-medium">Nama Supplier</span>
                                    <span>:</span>
                                    <span>JAMU</span>
                                </div>
                                <div class="flex gap-4 items-center">
                                    <span class="min-w-[150px] font-medium">Jumlah Pakai</span>
                                    <span>:</span>
                                    <span>{{ formatDecimal(10000) }} gram</span>
                                </div>
                                <div class="flex gap-4 items-center">
                                    <span class="min-w-[150px] font-medium">Jumlah Kadaluarsa</span>
                                    <span>:</span>
                                    <span>{{ parseDate('2023-01-01', 'DD MMM YYYY') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
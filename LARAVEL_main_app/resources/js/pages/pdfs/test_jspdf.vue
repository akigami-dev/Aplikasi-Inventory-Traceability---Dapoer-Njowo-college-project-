<script setup lang="ts">
import Button from 'primevue/button';
import { jsPDF } from 'jspdf';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    title: String,
});

const pdf = new jsPDF();

const head = ['nomor batch','nama produk', 'jumlah produksi', 'lokasi penyimpanan', 'rentang produksi', 'kadaluarsa', 'status produksi', 'bahan baku']
const body = [
    {
        'nomor batch': 'PRD01927512334',
        'nama produk': 'Kunyit Asam',
        'jumlah produksi': '1000',
        'lokasi penyimpanan': 'Gudang 1',
        'rentang produksi': '2022-01-01',
        'kadaluarsa': '2022-01-31',
        'status produksi': 'Produksi',
        'bahan baku': "1. Kunyit\n2. Asam\n3. Air",
    }
]

function exportPDF() {
    const doc = new jsPDF({ orientation: 'landscape'});
    doc.text('LAPORAN PRODUKSI', 10, 10);
    // doc.setFontSize(10);

    doc.table(10, 15, body, head, { padding: 3, fontSize: 10, headerBackgroundColor: '#1abc9c' });

    // doc.save("laporan-produksi.pdf");
    doc.output('dataurlnewwindow');
}

</script>

<template>
    <Head :title="title" />
    <main class="main-container ">

    </main>
    <Button label="test" @click="exportPDF" />
</template>
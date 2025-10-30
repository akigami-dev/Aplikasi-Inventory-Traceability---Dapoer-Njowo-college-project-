<script setup lang="ts">
import { useDefineProps } from '@/composables/useAppLayout';
import { Head } from '@inertiajs/vue3';
import { formatDecimal, openImage, parseDate, parseNumber } from '@/composables/Helper';
import Divider from 'volt-components/Divider.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import LayoutPdf_portrait from '../LayoutPdf_portrait.vue';
import Button from 'primevue/button';
import LayoutPdf_landscape from '../LayoutPdf_landscape.vue';

const props = defineProps({
    ...useDefineProps('Testing2'),
    dataLaporan: Object,
});

const filename = parseDate((new Date()).toString(), 'YYMMDDHHmmss') + '_' + (props?.dataLaporan?.nomor_batch ?? '') + '_DetailProduksi';
const baseurl = window.location.origin;

const selectedBahanBaku = props.dataLaporan.bahan_baku.map(item => ({
        kode_bahan: item.bahan_baku.kode_bahan,
        nama_bahan: item.bahan_baku.nama_bahan,
        jumlah_penggunaan: formatDecimal(Number(item.jumlah_penggunaan)),
        satuan: item.satuan,
        kode_lot: item.kode_lot,
        nama_supplier: item.bahan_baku.supplier.nama_supplier,
        kode_supplier: item.bahan_baku.supplier.kode_supplier,
    }));

</script>
<template>
    {{ console.log('filename: ', filename ) }}
    <Head :title="props.title" />
    {{ console.log('PROPS: ', props) }}
    <LayoutPdf_portrait :route="'owner.laporan'" :params="{'laporan': 'produksi'}" :filename="filename">
        <div class="flex flex-col gap-3">
            <div class="flex-1 border-b-2 border-surface py-2 flex justify-between items-end">
                <h1 class="text-3xl font-bold">LAPORAN DETAIL PRODUKSI </h1>
                <span class="text-xs text-surface-400">Dibuat tanggal: {{ parseDate((new Date()).toString(), 'DD/MM/YYYY HH:mm') }}</span>
            </div>
            <div class="flex flex-col">
                <header class="flex justify-between gap-4 border-b border-surface-200 dark:border-surface-700 py-3">
                    <div class="flex-1 flex gap-2 text-xs">
                        <div class="w-fit flex flex-col gap-2 mr-10">
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Nomor Batch</span> 
                                <span class="flex-1 font-medium">
                                : {{ dataLaporan.nomor_batch }}
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Nama Produk</span> 
                                <span class="flex-1 font-medium">
                                : {{ dataLaporan.produk.kode_produk }} - {{ dataLaporan.produk.nama_produk }}
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Jumlah Produksi</span> 
                                <span class="flex-1 font-medium">
                                : {{ dataLaporan.kuantitas }}
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">QR Code</span> 
                                <span class="flex-1 font-medium">
                                <img v-if="dataLaporan.qrcode_path" :src="dataLaporan.qrcode_path" alt="QR Code" class="w-20 cursor-pointer" @click="openImage(dataLaporan.qrcode_path)">
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Label</span> 
                                <span class="flex-1 font-medium">
                                <img v-if="dataLaporan.label_path" :src="dataLaporan.label_path" alt="QR Code" class="w-10 cursor-pointer" @click="openImage(dataLaporan.label_path)">
                                </span>
                            </div>
                        </div>
                    </div>
                    <Divider layout="vertical" />
                    <div class="flex-1 flex flex-col gap-2 text-xs">
                        <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Lokasi Penyimpanan</span> 
                                <span class="flex-1 font-medium">
                                : {{ dataLaporan.lokasi_penyimpanan.name }} ({{ dataLaporan.suhu_penyimpanan + ' °C' }})
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Mulai Produksi</span> 
                                <span class="flex-1 font-medium">
                                : {{ dataLaporan.mulai_produksi ? parseDate(dataLaporan.mulai_produksi, 'DD/MM/YYYY HH:mm') : '-' }}
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Selesai Produksi</span> 
                                <span class="flex-1 font-medium">
                                : {{ dataLaporan.selesai_produksi ? parseDate(dataLaporan.selesai_produksi, 'DD/MM/YYYY HH:mm') : '-' }}
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Tanggal Kadaluarsa</span> 
                                <span class="flex-1 font-medium">
                                : {{ dataLaporan.tanggal_kadaluarsa ? parseDate(dataLaporan.tanggal_kadaluarsa, 'DD/MM/YYYY HH:mm') : '-' }}
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Status Produksi</span> 
                                <span class="flex-1 font-medium">
                                : {{ dataLaporan.status }}
                                </span>
                            </div>
                    </div>
                </header>
                <main class="flex flex-col main-container gap-4">
                    <span class="flex items-center gap-2">
                        <span class="font-bold dark:text-surface-400 text-sm">Pengunaan Bahan Baku</span>
                        <!-- <i class="pi pi-history dark:text-surface-400" size="0.5rem"></i> -->
                    </span>
                    <DataTable class="text-xs avoid-page-break" striped-rows :value="selectedBahanBaku">
                        <Column style="min-width: 0rem; max-width: 4rem;" class="wrap-break-word" header="#">
                            <template #body="{ index }">
                                <span class="font-bold">{{ index + 1 }}</span>
                            </template>
                        </Column>
                        <Column style="min-width: 0rem; max-width: 9rem;" class="wrap-break-word" field="kode_lot" header="Kode LOT Bahan Baku"></Column>
                        <Column style="min-width: 0rem; max-width: 7rem;" class="wrap-break-word" field="nama_bahan" header="Nama Bahan">
                            <template #body="{ data }">
                                {{ data.nama_bahan }}
                            </template>
                        </Column>
                        <Column style="min-width: 0rem; max-width: 7rem;" class="wrap-break-word" header="Jumlah Penggunaan">
                            <template #body="{ data }">
                                {{ data.jumlah_penggunaan }} {{ data.satuan }}
                            </template>
                        </Column>
                        <Column style="min-width: 0rem; max-width: 7rem;" class="wrap-break-word" field="nama_supplier" header="Nama Supplier"></Column>
                        <template #empty>
                            <div class="text-center py-5 text-surface-500">
                                Data Tidak Ditemukan
                            </div>
                        </template>
                    </DataTable>
                </main>
            </div>
        </div>
    </LayoutPdf_portrait>
</template>
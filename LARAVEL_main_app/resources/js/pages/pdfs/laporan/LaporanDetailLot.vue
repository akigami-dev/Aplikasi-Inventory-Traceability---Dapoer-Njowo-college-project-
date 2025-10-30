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

const filename = parseDate((new Date()).toString(), 'YYMMDDHHmmss') + '_' + (props?.dataLaporan?.kode_lot ?? '') + '_DetailLot';
const baseurl = window.location.origin;

</script>
<template>
    {{ console.log('filename: ', filename ) }}
    <Head :title="props.title" />
    {{ console.log('PROPS: ', props) }}
    <LayoutPdf_portrait :route="'owner.laporan'" :params="{'laporan': 'lot bahan baku'}" :filename="filename">
        <div class="flex flex-col gap-3">
            <div class="flex-1 border-b-2 border-surface py-2 flex justify-between items-end">
                <h1 class="text-3xl font-bold">LAPORAN DETAIL LOT </h1>
                <span class="text-xs text-surface-400">Dibuat tanggal: {{ parseDate((new Date()).toString(), 'DD/MM/YYYY HH:mm') }}</span>
            </div>
            <div class="flex flex-col">
                <header class="flex justify-between gap-4 border-b border-surface-200 dark:border-surface-700 py-3">
                    <div class="flex-1 flex gap-2 text-xs">
                        <div class="w-fit flex flex-col gap-2 mr-10">
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Kode Lot</span> 
                                <span class="flex-1 font-medium">
                                : {{ dataLaporan.kode_lot }}
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Nama Bahan</span> 
                                <span class="flex-1 font-medium">
                                : {{ dataLaporan.bahan_baku.nama_bahan }}
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Tanggal Kadaluarsa</span> 
                                <span class="flex-1 font-medium flex gap-2">
                                    <span>: {{ parseDate(dataLaporan.tanggal_kadaluarsa, 'DD/MM/YYYY') }}</span>
                                    <!-- <span :class="dataLaporan.status.toLowerCase() == 'tersedia' ? 'text-green-500' : 'text-red-500'" class="font-bold">{{ dataLaporan.status.toUpperCase() }}</span> -->
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Jumlah Tersisa</span> 
                                <span class="flex-1 font-medium">
                                : {{ formatDecimal(dataLaporan.jumlah) }} {{ dataLaporan.satuan }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="w-fit h-fit flex items-center justify-center border border-surface rounded-full bg-surface-50 dark:bg-surface-950 -rotate-20">
                        <img :src="baseurl + '/storage/images/assets/status_lot/' + dataLaporan.status.toLowerCase() + '.png'" class="max-w-20" alt="status">
                    </div>
                    <Divider layout="vertical" />
                    <div class="flex-1 flex flex-col gap-2 text-xs">
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Supplier Bahan</span> 
                            <span class="flex-1 font-medium">
                            : {{ dataLaporan.bahan_baku.supplier.nama_supplier }}
                            </span>
                        </div>
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Kode Supplier</span> 
                            <span class="flex-1 font-medium">
                            : {{ dataLaporan.bahan_baku.supplier.kode_supplier }}
                            </span>
                        </div>
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Tanggal Restok</span> 
                            <span class="flex-1 font-medium">
                            : {{ parseDate(dataLaporan.restok_bahan_baku.tanggal_restok, 'DD/MM/YYYY') }}
                            </span>
                        </div>
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Jumlah Restok</span> 
                            <span class="flex-1 font-medium">
                            : {{ formatDecimal(dataLaporan.restok_bahan_baku.jumlah_restok) }} {{ dataLaporan.restok_bahan_baku.satuan }}
                            </span>
                        </div>
                    </div>
                </header>
                <main class="flex flex-col main-container gap-4">
                    <span class="flex items-center gap-2">
                        <span class="font-bold dark:text-surface-400 text-sm">History Penggunaan</span>
                        <i class="pi pi-history dark:text-surface-400" size="0.5rem"></i>
                    </span>
                    <DataTable class="text-xs avoid-page-break" striped-rows :value="dataLaporan.lot_penggunaan_logs">
                        <Column field="nomor_batch" header="Nomor Batch"></Column>
                        <Column header="Tanggal Penggunaan">
                            <template #body="{ data }">
                                {{ parseDate(data.created_at, 'DD/MM/YYYY') }}
                            </template>
                        </Column>
                        <Column header="Jumlah">
                            <template #body="{ data }">
                                {{ formatDecimal(data.jumlah) }} {{ 'gram' }}
                            </template>
                        </Column>
                        <Column field="user_name" header="User"></Column>
                        <!-- <Column field="action" header="Action"></Column> -->
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
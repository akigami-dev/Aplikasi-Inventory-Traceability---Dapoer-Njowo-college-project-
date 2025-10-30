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

const filename = parseDate((new Date()).toString(), 'YYMMDDHHmmss') + '_' + (props?.dataLaporan?.kode_distribusi ?? '') + '_DetailDistribusi';
const baseurl = window.location.origin;

</script>
<template>
    {{ console.log('filename: ', filename ) }}
    <Head :title="props.title" />
    {{ console.log('PROPS: ', props) }}
    <LayoutPdf_portrait :route="'owner.laporan'" :params="{'laporan': 'distribusi'}" :filename="filename">
        <div class="flex flex-col gap-3">
            <div class="flex-1 border-b-2 border-surface py-2 flex justify-between items-end">
                <h1 class="text-3xl font-bold">LAPORAN DETAIL DISTRIBUSI </h1>
                <span class="text-xs text-surface-400">Dibuat tanggal: {{ parseDate((new Date()).toString(), 'DD/MM/YYYY HH:mm') }}</span>
            </div>
            <div class="flex flex-col">
                <header class="flex justify-between gap-4 border-b border-surface-200 dark:border-surface-700 py-3">
                    <div class="flex-1 flex gap-2 text-xs">
                        <div class="w-fit flex flex-col gap-2 mr-10">
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Kode Distribusi</span> 
                                <span class="flex-1 font-medium">
                                : {{ dataLaporan.kode_distribusi }}
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Nomor Batch</span> 
                                <span class="flex-1 font-medium">
                                : {{ dataLaporan.produksi.nomor_batch }}
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Nama Produk</span> 
                                <span class="flex-1 font-medium">
                                : {{ dataLaporan.produksi.produk.nama_produk }}
                                </span>
                            </div>
                            
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Total ter-Recall</span> 
                                <span class="flex-1 font-medium">
                                : {{ dataLaporan.recall_produk.reduce((total, item) => total + item.jumlah_recall, 0) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <Divider layout="vertical" />
                    <div class="flex-1 flex flex-col gap-2 text-xs">
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Nama Retailer</span> 
                            <span class="flex-1 font-medium">
                            : {{ dataLaporan.nama_retailer }}
                            </span>
                        </div>
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Tanggal Pengiriman</span> 
                            <span class="flex-1 font-medium">
                                <span>: {{ parseDate(dataLaporan.tanggal_pengiriman, 'DD/MM/YYYY') }}</span>
                                <!-- <span :class="dataLaporan.status.toLowerCase() == 'tersedia' ? 'text-green-500' : 'text-red-500'" class="font-bold">{{ dataLaporan.status.toUpperCase() }}</span> -->
                            </span>
                        </div>
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Alamat</span> 
                            <span class="flex-1 font-medium">
                            : {{ dataLaporan.alamat }}
                            </span>
                        </div>
                    </div>
                </header>
                <main class="flex flex-col main-container gap-4">
                    <span class="flex items-center gap-2">
                        <span class="font-bold dark:text-surface-400 text-sm">History Recall Produk</span>
                        <i class="pi pi-history dark:text-surface-400" size="0.5rem"></i>
                    </span>
                    <DataTable class="text-xs avoid-page-break" striped-rows :value="dataLaporan.recall_produk">
                        <Column style="min-width: 0rem; max-width: 4rem;" class="wrap-break-word" header="#">
                            <template #body="{ index }">
                                <span class="font-bold">{{ index + 1 }}</span>
                            </template>
                        </Column>
                        <Column style="min-width: 0rem; max-width: 7rem;" field="kode_recall" header="Kode Recall"></Column>
                        <Column style="min-width: 0rem; max-width: 7rem;" field="tanggal_recall" header="Tanggal Recall">
                            <template #body="{ data }">
                                {{ parseDate(data.tanggal_recall, 'YYYY MMM DD') }}
                            </template>
                        </Column>
                        <Column style="min-width: 0rem; max-width: 4rem;" field="jumlah_recall" header="Jumlah Recall">
                            <template #body="{ data }">
                                <span class="font-bold text-center">{{ data.jumlah_recall }}</span>
                            </template>
                        </Column>
                        <Column style="min-width: 0rem; max-width: 7rem;" field="alasan_recall" header="Alasan Recall">
                            <template #body="{ data }">
                                <span class="font-medium text-primary capitalize">{{ data.alasan_recall }}</span>
                            </template>
                        </Column>
                        <Column style="min-width: 0rem; max-width: 10rem;" field="keterangan" header="Keterangan" class="max-w-xs wrap-break-word">
                            <template #body="{ data }">
                                <div class="text-justify">
                                    {{ data.keterangan }}
                                </div>
                            </template>
                        </Column>
                        <Column style="min-width: 0rem; max-width: 7rem;" field="user.name" header="Direcall oleh"></Column>
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
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

const filename = parseDate((new Date()).toString(), 'YYMMDDHHmmss') + '_LaporanDistribusi';


</script>
<template>
    {{ console.log('filename: ', filename ) }}
    <Head :title="props.title" />
    {{ console.log('PROPS: ', props) }}
    <LayoutPdf_portrait :route="'owner.laporan'" :params="{'laporan': 'distribusi'}" :filename="filename">
        <div class="flex flex-col gap-3">
            <div class="flex-1 border-b-2 border-surface py-2 flex justify-between items-end">
                <h1 class="text-3xl font-bold">LAPORAN DISTRIBUSI</h1>
                <span class="text-xs text-surface-400">Dibuat tanggal: {{ parseDate((new Date()).toString(), 'DD/MM/YYYY HH:mm') }}</span>
            </div>
            <div class="flex flex-col gap-3">
                <DataTable class="shadow !overflow-auto text-xs avoid-page-break" show-gridlines striped-rows :value="dataLaporan">
                    <Column style="min-width: 0rem; max-width: 9rem;" class="!p-1" field="kode_distribusi" header="Kode Distribusi"></Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" class="!p-1" field="produksi.nomor_batch" header="Nomor Batch"></Column>
                    <Column style="min-width: 0rem; max-width: 4rem;" class="!p-1 wrap-break-word" field="produksi.produk.nama_produk" header="Nama Produk"></Column>
                    <Column style="min-width: 0rem; max-width: 5rem;" class="!p-1 wrap-break-word" field="nama_retailer" header="Nama Retailer"></Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" class="!p-1" header="Tanggal Pengiriman">
                        <template #body="{ data }">
                            {{ parseDate(data.tanggal_pengiriman, 'DD/MM/YYYY') }}
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 6rem; width: 0;" class="!p-1 wrap-break-word" field="jumlah_tersisa" header="Jumlah Tersisa"></Column>
                    <Column style="min-width: 8rem; max-width: 12rem; width: 0;" class="!p-1 wrap-break-word" field="alamat" header="Alamat"></Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" class="!p-1" header="Total ter-Recall">
                        <template #body="{ data }">
                            {{ data.recall_produk.reduce((total, item) => total + item.jumlah_recall, 0) }}
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
    </LayoutPdf_portrait>
</template>
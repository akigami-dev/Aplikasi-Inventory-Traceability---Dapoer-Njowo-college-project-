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

const filename = parseDate((new Date()).toString(), 'YYMMDDHHmmss') + '_LaporanLotBahanBaku';


</script>
<template>
    {{ console.log('filename: ', filename ) }}
    <Head :title="props.title" />
    {{ console.log('PROPS: ', props) }}
    <LayoutPdf_portrait :route="'owner.laporan'" :params="{'laporan': 'lot bahan baku'}" :filename="filename">
        <div class="flex flex-col gap-3">
            <div class="flex-1 border-b-2 border-surface py-2 flex justify-between items-end">
                <h1 class="text-3xl font-bold">LAPORAN LOT BAHAN BAKU</h1>
                <span class="text-xs text-surface-400">Dibuat tanggal: {{ parseDate((new Date()).toString(), 'DD/MM/YYYY HH:mm') }}</span>
            </div>
            <div class="flex flex-col gap-3">
                <DataTable class="shadow !overflow-auto text-xs avoid-page-break" show-gridlines striped-rows :value="dataLaporan">
                    <Column style="min-width: 0rem; max-width: 9rem;" class="!p-1" field="kode_lot" header="Kode Lot"></Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" class="!p-1" field="restok_bahan_baku.kode_restok" header="Kode Restok"></Column>
                    <Column style="min-width: 0rem; max-width: 6rem;" class="!p-1 wrap-break-word" header="Bahan Baku">
                        <template #body="{ data }">
                            {{ data.bahan_baku.kode_bahan }} - {{ data.bahan_baku.nama_bahan }}
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" class="!p-1" header="Status">
                        <template #body="{ data }">
                            <span class="font-bold">
                                {{ data.status.toUpperCase() }}
                            </span>
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" class="!p-1" header="Tanggal Kadaluarsa">
                        <template #body="{ data }">
                            {{ parseDate(data.tanggal_kadaluarsa, 'DD/MM/YYYY') }}
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 5rem;" class="!p-1 wrap-break-word" header="Jumlah Tersisa">
                        <template #body="{ data }">
                            {{ formatDecimal(data.jumlah) }} {{ data.satuan }}
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
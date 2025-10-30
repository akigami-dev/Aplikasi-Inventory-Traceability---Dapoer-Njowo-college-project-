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

const filename = parseDate((new Date()).toString(), 'YYMMDDHHmmss') + '_LaporanProduksi';


</script>
<template>
    {{ console.log('filename: ', filename ) }}
    <Head :title="props.title" />
    {{ console.log('PROPS: ', props) }}
    <LayoutPdf_landscape :route="'owner.laporan'" :params="{'laporan': 'produksi'}" :filename="filename">
        <div class="flex flex-col gap-3">
            <div class="flex-1 border-b-2 border-surface py-2 flex justify-between items-end">
                <h1 class="text-3xl font-bold">LAPORAN PRODUKSI</h1>
                <span class="text-xs text-surface-400">Dibuat tanggal: {{ parseDate((new Date()).toString(), 'DD/MM/YYYY HH:mm') }}</span>
            </div>
            <div class="flex flex-col gap-3">
                <DataTable class="shadow !overflow-auto text-xs avoid-page-break" show-gridlines striped-rows :value="dataLaporan" id="thetable">
                    <Column style="min-width: 0rem; max-width: 9rem;" class="!p-1" field="nomor_batch" header="Nomor Batch"></Column>
                    <Column style="min-width: 0rem; max-width: 7rem;" class="!p-1 wrap-break-word" field="produk.nama_produk" header="Nama Produk"></Column>
                    <!-- <Column style="min-width: 0rem; max-width: 12rem;" class="!p-1" header="Bahan Baku">
                        <template #body="{ data }">
                            <div class="flex mb-2" v-for="(item, index) in data.bahan_baku" :key="index">
                                <div class="w-1 h-1 bg-primary rounded-full mr-3 flex-shrink-0 self-center"></div>
                                <span class="font-medium min-w-10">{{ item.lot_bahan_baku.restok_bahan_baku.master_bahan_baku.nama_bahan }}: </span>
                                <span>{{ item.lot_bahan_baku.restok_bahan_baku.master_bahan_baku.supplier.nama_supplier }}({{ parseNumber(item.jumlah_penggunaan) }} {{ item.satuan }})</span>
                                <span></span>
                            </div>
                        </template>
                    </Column> -->
                    <Column style="min-width: 0rem; max-width: 4rem;" class="!p-1" field="kuantitas" header="Jumlah Produksi"></Column>
                    <Column style="min-width: 0rem; max-width: 6rem;" class="!p-1" header="Lokasi Penyimpanan">
                        <template #body="{ data }">
                            <div class="flex gap-1 font-medium">
                                <span>{{ data.lokasi_penyimpanan.name }}</span>
                                <span class="font-semibold wrap-break-word">({{ data.suhu_penyimpanan }} °C)</span>
                            </div>
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 6rem;" class="!p-1" header="Rentang Produksi">
                        <template #body="{ data }">
                            {{ data.mulai_produksi ? parseDate(data.mulai_produksi, 'DD/MM/YYYY HH:mm') : '-' }} s/d {{ data.selesai_produksi ? parseDate(data.selesai_produksi, 'DD/MM/YYYY HH:mm') : '-' }}
                            <!-- {{ data.mulai_produksi }} s/d {{ data.selesai_produksi }} -->
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" class="!p-1" field="tanggal_kadaluarsa" header="Kadaluarsa">
                        <template #body="{ data }">
                            {{ data.tanggal_kadaluarsa ? parseDate(data.tanggal_kadaluarsa, 'DD/MM/YYYY') : '-' }}
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" class="!p-1" field="status" header="Status Produksi"></Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" class="!p-1" header="QR Code">
                        <template #body="{ data }">
                            <img v-tooltip.top="`Lihat QR Code`" v-if="data.qrcode_path" :src="data.qrcode_path" alt="QR Code" class="w-15 shadow cursor-pointer" @click="openImage(data.qrcode_path)">
                            <span v-else>-</span>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>
                    </template>
                </DataTable>
                <!-- <table
                id="thetable"
                class="rounded shadow text-xs border border-gray-300 border-collapse"
                >
                    <thead>
                        <tr>
                            <th class="text-left border border-gray-300 px-2 py-1">Nomor Batch</th>
                            <th class="text-left border border-gray-300 px-2 py-1">Nama Produk</th>
                            <th class="text-left border border-gray-300 px-2 py-1">Bahan Baku</th>
                            <th class="text-left border border-gray-300 px-2 py-1">Jumlah Produksi</th>
                            <th class="text-left border border-gray-300 px-2 py-1">Lokasi Penyimpanan</th>
                            <th class="text-left border border-gray-300 px-2 py-1">Rentang Produksi</th>
                            <th class="text-left border border-gray-300 px-2 py-1">Kadaluarsa</th>
                            <th class="text-left border border-gray-300 px-2 py-1">Status Produksi</th>
                            <th class="text-left border border-gray-300 px-2 py-1">QR Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in dataLaporan" :key="index" class="border border-gray-300">
                            <td class="border border-gray-300 px-2 py-1">{{ item.nomor_batch }}</td>
                            <td class="border border-gray-300 px-2 py-1">{{ item.produk.nama_produk }}</td>
                            <td class="border border-gray-300 px-2 py-1">asd</td>
                            <td class="border border-gray-300 px-2 py-1">{{ item.kuantitas }}</td>
                            <td class="border border-gray-300 px-2 py-1">{{ item.lokasi_penyimpanan.name }} ({{ item.suhu_penyimpanan }} °C)</td>
                            <td class="border border-gray-300 px-2 py-1">{{ item.mulai_produksi }} - {{ item.selesai_produksi }}</td>
                            <td class="border border-gray-300 px-2 py-1">{{ item.tanggal_kadaluarsa }}</td>
                            <td class="border border-gray-300 px-2 py-1">{{ item.status }}</td>
                            <td class="border border-gray-300 px-2 py-1">
                                <img
                                v-tooltip.top="`Lihat QR Code`"
                                v-if="item.qrcode_path"
                                :src="item.qrcode_path"
                                alt="QR Code"
                                class="w-15 shadow cursor-pointer"
                                />
                                <span v-else>-</span>
                            </td>
                        </tr>
                    </tbody>
                </table> -->
            </div>
        </div>
    </LayoutPdf_landscape>
</template>
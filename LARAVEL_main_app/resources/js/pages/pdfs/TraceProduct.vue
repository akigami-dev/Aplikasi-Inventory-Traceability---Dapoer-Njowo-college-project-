<script setup lang="ts">
import { useDefineProps } from '@/composables/useAppLayout';
import { Head } from '@inertiajs/vue3';
import { formatDecimal, parseDate } from '@/composables/Helper';
import Divider from 'volt-components/Divider.vue';
import LayoutPdf_portrait from './LayoutPdf_portrait.vue';

const props = defineProps({
    ...useDefineProps('Testing2'),
    trace: Object
});

const filename = parseDate((new Date()).toString(), 'YYMMDDHHmmss') + '_' + (props?.trace?.produksi?.nomor_batch ?? '') + '_TraceProduct';

</script>
<template>
    {{ console.log('filename: ', filename ) }}
    <Head :title="props.title" />
    <LayoutPdf_portrait :route="'auth.trace'" :filename="filename">
        <div class="flex flex-col gap-3 text-sm">
            <div class="flex-1 border-b-2 border-surface py-2">
                <h1 class="text-3xl font-bold">TRACE PRODUCT</h1>
            </div>
            <div class="flex flex-col gap-3">
                <div class="flex-1 flex flex-wrap justify-start gap-3">
                    <div class="flex-1 flex flex-col justify-start gap-3">
                        <div class="flex-1 flex flex-col gap-2 p-3 border border-surface rounded">
                            <span class="w-fit text-xl font-bold border-b-2 border-surface">Trace</span>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[120px] font-medium text-primary">Gambar Produk</span>
                                <span>:</span>
                                <span><img :src="trace?.produk.gambar" alt="Gambar Produk" class="max-w-20 shadow"></span>
                            </div>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[120px] font-medium text-primary">QR Code</span>
                                <span>:</span>
                                <span><img :src="trace?.produksi.qrcode_path" alt="QR Code" class="max-w-20 shadow"></span>
                            </div>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[120px] font-medium text-primary">Label Produk</span>
                                <span>:</span>
                                <span><img :src="trace?.produksi.label_path" alt="Gambar Produk" class="max-w-20 shadow"></span>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col gap-2 p-3 border border-surface rounded">
                            <span class="w-fit text-xl font-bold border-b-2 border-surface">Produk</span>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[100px] font-medium text-primary">Kode Produk</span>
                                <span>:</span>
                                <span>{{ trace?.produk?.kode_produk }}</span>
                            </div>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[100px] font-medium text-primary">Nama Produk</span>
                                <span>:</span>
                                <span>{{ trace?.produk?.nama_produk }}</span>
                            </div>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[100px] font-medium text-primary">Kategori</span>
                                <span>:</span>
                                <span>{{ trace?.produk?.kategori.name }}</span>
                            </div>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[100px] font-medium text-primary border-b-1 border-surface">Sertifikasi</span>
                            </div>
                            <div class="flex flex-col gap-5 py-1" v-for="(sertifikasi, index) in trace?.produk?.sertifikasi" :key="index">
                                <div class="flex">
                                    <div class="w-2 h-2 bg-primary rounded-full mr-3 flex-shrink-0 self-center"></div>
                                    <span class="font-medium min-w-10">{{ sertifikasi.nama_sertifikasi }}</span>
                                    <Divider layout="vertical" />
                                    <span class="font-light">{{ sertifikasi.kode_sertifikasi }}</span>
                                </div>
                            </div>
                            <span v-if="trace?.produk?.sertifikasi.length <= 0" class="text-surface-400 text-sm">(Tidak memiliki sertifikasi)</span>
                        </div>
                        <div class="flex-1 flex flex-col justify-start gap-3">
                            <div class="flex-1 flex flex-col gap-2 p-3 border border-surface rounded">
                                <span class="w-fit text-xl font-bold border-b-2 border-surface">Recall Produk</span>
                                <!-- foreach here -->
                                <div v-for="(item, index) in trace?.distribusi?.recall_produk" :key="index" class="avoid-page-break border border-surface rounded p-1">
                                    <span class=" font-bold">#{{ index + 1 }}.</span>
                                    <div class="pl-[1.1rem]">
                                        <div class="flex gap-4 items-center">
                                            <span class="min-w-[100px] font-medium text-primary">Kode Recall</span>
                                            <span>:</span>
                                            <span>{{ item?.kode_recall }}</span>
                                        </div>
                                        <div class="flex gap-4 items-center">
                                            <span class="min-w-[100px] font-medium text-primary">Tanggal Recall</span>
                                            <span>:</span>
                                            <span>{{ parseDate(item?.tanggal_recall, 'DD MMM YYYY HH:ss') }}</span>
                                        </div>
                                        <div class="flex gap-4 items-center">
                                            <span class="min-w-[100px] font-medium text-primary">Jumlah Recall</span>
                                            <span>:</span>
                                            <span>{{ item?.jumlah_recall }}</span>
                                        </div>
                                        <div class="flex gap-4 items-center">
                                            <span class="min-w-[100px] font-medium text-primary">Alasan Recall</span>
                                            <span>:</span>
                                            <span>{{ item?.alasan_recall }}</span>
                                        </div>
                                        <div class="flex gap-4">
                                            <span class="min-w-[100px] font-medium text-primary">Keterangan</span>
                                            <span>:</span>
                                            <span>{{ item?.keterangan }}</span>
                                        </div>
                                        <div class="flex gap-4 items-center">
                                            <span class="min-w-[100px] font-medium text-primary">Direcall Oleh</span>
                                            <span>:</span>
                                            <span>{{ item?.user.name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <span v-if="trace?.distribusi.recall_produk.length <= 0" class="text-surface-400 text-sm">(Data recall tidak ditemukan atau kosong)</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col justify-start gap-3">
                        <div class="flex-1 flex flex-col gap-2 p-3 border border-surface rounded">
                            <span class="w-fit text-xl font-bold border-b-2 border-surface">Distribusi</span>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[150px] font-medium text-primary">Kode Distribusi</span>
                                <span>:</span>
                                <span>{{ trace?.distribusi?.kode_distribusi }}</span>
                            </div>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[150px] font-medium text-primary">Nama Retailer</span>
                                <span>:</span>
                                <span>{{ trace?.distribusi?.nama_retailer }}</span>
                            </div>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[150px] font-medium text-primary">Tanggal Pengiriman</span>
                                <span>:</span>
                                <span>{{ parseDate( trace?.distribusi?.tanggal_pengiriman, 'DD MMM YYYY') }}</span> 
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[150px] font-medium text-primary">Alamat</span>
                                <span>:</span>
                                <span>{{ trace?.distribusi?.alamat }}</span>
                            </div>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[150px] font-medium text-primary">Jumlah Distribusi Tersisa</span>
                                <span>:</span>
                                <span>{{ trace?.distribusi?.jumlah_tersisa }}</span>
                            </div>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[150px] font-medium text-primary">Jumlah Ter-recall</span>
                                <span>:</span>
                                <span>{{ trace?.distribusi?.recall_produk.reduce((total, item) => total + item.jumlah_recall, 0) }}</span>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col gap-2 p-3 border border-surface rounded">
                            <span class="w-fit text-xl font-bold border-b-2 border-surface">Produksi</span>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[150px] font-medium text-primary">Nomor Batch</span>
                                <span>:</span>
                                <span class="wrap-break-word">{{ trace?.produksi?.nomor_batch }}</span>
                            </div>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[150px] font-medium text-primary">Mulai Produksi</span>
                                <span>:</span>
                                <span>{{ parseDate( trace?.produksi?.mulai_produksi, 'DD MMM YYYY') }}</span>
                            </div>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[150px] font-medium text-primary">Selesai Produksi</span>
                                <span>:</span>
                                <span>{{ parseDate(trace?.produksi?.selesai_produksi, 'DD MMM YYYY') }}</span>
                            </div>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[150px] font-medium text-primary">Tanggal Kadaluarsa</span>
                                <span>:</span>
                                <span>{{ parseDate(trace?.produksi?.tanggal_kadaluarsa, 'DD MMM YYYY') }}</span>
                            </div>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[150px] font-medium text-primary">Lokasi Penyimpanan</span>
                                <span>:</span>
                                <span>{{ trace?.produksi?.lokasi_penyimpanan }} ({{ trace?.produksi?.suhu_penyimpanan }} °C)</span>
                            </div>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[150px] font-medium text-primary">Jumlah Produksi</span>
                                <span>:</span>
                                <span>{{ trace?.produksi?.kuantitas }}</span>
                            </div>
                            <div class="flex gap-4 items-center">
                                <span class="min-w-[150px] font-medium text-primary">Diproduksi Oleh</span>
                                <span>:</span>
                                <span>{{ trace?.produksi?.user_name }}</span>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col gap-2 p-3 border border-surface rounded">
                        <span class="w-fit text-xl font-bold border-b-2 border-surface">Bahan Baku</span>
                        <!-- foreach here -->
                        <div v-for="(item, index) in trace?.bahan_baku" :key="index" class="avoid-page-break border border-surface rounded p-1">
                            <span class=" font-bold capitalize">{{ index + 1 }}. {{ item.bahan_baku.nama_bahan }}</span>
                            <div class="pl-[1.1rem]">
                                <div class="flex gap-4 items-center">
                                    <span class="min-w-[150px] font-medium text-primary">Kode LOT</span>
                                    <span>:</span>
                                    <span>{{ item?.lot_bahan_baku?.kode_lot }}</span>
                                </div>
                                <div class="flex gap-4 items-center">
                                    <span class="min-w-[150px] font-medium text-primary">Nama Supplier</span>
                                    <span>:</span>
                                    <span>{{ item?.bahan_baku.supplier.nama_supplier }}</span>
                                </div>
                                <div class="flex gap-4 items-center">
                                    <span class="min-w-[150px] font-medium text-primary">Jumlah Pakai</span>
                                    <span>:</span>
                                    <span>{{ formatDecimal(item?.jumlah_penggunaan) }} {{ item?.satuan }}</span>
                                </div>
                                <div class="flex gap-4 items-center">
                                    <span class="min-w-[150px] font-medium text-primary">Jumlah Kadaluarsa</span>
                                    <span>:</span>
                                    <span>{{ parseDate(item?.bahan_baku?.tanggal_kadaluarsa, 'DD MMM YYYY') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </LayoutPdf_portrait>
</template>
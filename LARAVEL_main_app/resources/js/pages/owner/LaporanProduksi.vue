<script setup lang="ts">
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import LayoutLaporan from './LayoutLaporan.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputText from 'volt-components/InputText.vue';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { formatDecimal, openImage, parseDate, parseDateForDatePicker } from '@/composables/Helper';
import Paginator from 'primevue/paginator';
import Dialog from 'volt-components/Dialog.vue';
import Select from 'primevue/select';
import FilterMenu from '@/components-project/filterMenu.vue';
import SelectButton from 'primevue/selectbutton';
import DatePicker from 'primevue/datepicker';

const props = defineProps({
    ...useDefineProps("Template"),
    laporan: String,
    search: String,
    dataLaporan: Object,
    paginate: Number,
    filter: Object,
})

defineOptions({
    layout: useAppLayout()
})

// VARIABLE SECTIONS
const searchValue = ref(props.search ?? '');
const DataLaporan = ref(props?.dataLaporan?.data ?? []);
const DataMeta = ref(props?.dataLaporan?.meta ?? {});

const tests = ref();
const visibleLBB = ref(false);
const selectedBahanBaku = ref(null);

// FUNCTION SECTIONS
const changePaginate = (amount: number) => {
    router.get(window.location.pathname + window.location.search, {paginate: amount});
}

const exportPDF = (data, laporan = props.laporan) => {
    router.post(route('owner.laporan.export_pdf'), {
        laporan: laporan,
        dataLaporan: data,
    });
}

const sortOption = ref([
    'created at',
    'mulai produksi',
    'selesai produksi',
    'tanggal kadaluarsa',
    'status produksi',
    'jumlah produksi',
])

const rangeOption = ref([
    'mulai produksi',
    'selesai produksi',
    'tanggal kadaluarsa',
])

const filters = ref({
    sort: {
        field: props.filter?.sort?.field ?? 'created at',
        order: props.filter?.sort?.order ?? 'desc',
    },
    range: {
        field: props.filter?.range?.field ?? null,
        time: props.filter?.range?.time ? [parseDateForDatePicker(props.filter?.range?.time[0]), parseDateForDatePicker(props.filter?.range?.time[1])] : null
    }
})

const submittedFilters = {};

function submitSearch(){
    console.log("Data Laporan: ", DataLaporan.value);
    console.log("tests: ", tests.value);
    formatFilters();
    router.get(route('owner.laporan'), {laporan: props.laporan, s: searchValue.value, page: DataMeta.value.current_page, filter: submittedFilters});
}

function formatFilters(){
    const range = filters.value.range;
    const sort = filters.value.sort;
    if (
        range.field &&
        Array.isArray(range.time) &&
        range.time[0] &&
        range.time[1]
    ) {
        submittedFilters.range = {
            field: range.field.toLowerCase(),
            time: [
                parseDate(range.time[0]),
                parseDate(range.time[1])
            ]
        }
    } else {
        delete submittedFilters.range;
    }
    if(sort.field && sort.order){
        submittedFilters.sort = {
            field: sort.field.toLowerCase(),
            order: sort.order,
        }
    }else{
        delete submittedFilters.sort;
    }
}

function toPage(page){
    router.get(route('owner.laporan'), {laporan: props.laporan, s: searchValue.value, page: page});
}

function openLBB(data:object){
    console.log("DATA: ", data);
    selectedBahanBaku.value = data.bahan_baku.map(item => ({
        kode_bahan: item.bahan_baku.kode_bahan,
        nama_bahan: item.bahan_baku.nama_bahan,
        jumlah_penggunaan: formatDecimal(Number(item.jumlah_penggunaan)),
        satuan: item.satuan,
        kode_lot: item.kode_lot,
        nama_supplier: item.bahan_baku.supplier.nama_supplier,
        kode_supplier: item.bahan_baku.supplier.kode_supplier,
    }));
    visibleLBB.value = true;
}

</script>
<template>
    <Head :title="props.title" />
    <LayoutLaporan :selected-laporan="laporan">
        <div class="flex flex-col gap-4">
            <div class="flex-1">
                <form @submit.prevent="submitSearch" class="flex gap-2">
                    <InputText v-model="searchValue" placeholder="Cari..." />
                    <Button label="Search" type="submit" />
                </form>
            </div>
            
            <FilterMenu>
                <div class="flex gap-2 w-full">
                    <div class="flex gap-2 items-center p-2 rounded-sm shadow">
                        <span class="font-medium text-surface-500 dark:text-surface-400 capitalize">sort: </span>
                        <div class="flex gap-2 items-center">
                            <Select size="small" v-model="filters.sort.field" :options="sortOption" placeholder="select option">
                                <template #option="{option}">
                                    <span class="capitalize">{{ option }}</span>
                                </template>
                            </Select>
                            <Select size="small" v-model="filters.sort.order" :options="['asc', 'desc']"></Select>
                        </div>
                    </div>
                    <div class="flex gap-2 items-center p-2 rounded-sm shadow">
                        <span class="font-medium text-surface-500 dark:text-surface-400 capitalize">range: </span>
                        <div class="flex gap-2 items-center">
                            <Select size="small" v-model="filters.range.field" :options="rangeOption" placeholder="select option">
                                <template #option="{option}">
                                    <span class="capitalize">{{ option }}</span>
                                </template>
                            </Select>
                            <DatePicker v-model="filters.range.time" selectionMode="range" class="" size="small" showIcon iconDisplay="input" inputId="icondisplay" placeholder="dd/mm/yy - dd/mm/yy" date-format="dd/mm/yy" />
                            <i v-if="filters.range.time || filters.range.field" class="pi pi-times-circle text-red-400 hover:text-red-500 cursor-pointer" style="font-size: 1.2rem" @click="filters.range.time = null; filters.range.field = null"></i>
                        </div>
                    </div>
                </div>
                <Button label="Filter" size="small" @click="submitSearch" />
            </FilterMenu>
            
            <div class="flex-1">
                <Button icon="pi pi-file-pdf" label="PDF" severity="danger" outlined size="small" @click="exportPDF(DataLaporan)" />
            </div>
            <div class="flex-1">
                <DataTable class="rounded-lg shadow !overflow-auto text-sm" show-gridlines striped-rows :value="DataLaporan">
                    <!-- <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold border-b-2 dark:border-surface-400">Laporan Produksi</span>
                        </div>
                    </template> -->
                    <Column style="min-width: 0rem; max-width: 9rem;" field="nomor_batch" header="Nomor Batch"></Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" header="Gambar Produk">
                        <template #body="{ data }">
                            <img :src="data.produk.gambar" alt="image" class="w-15 rounded object-cover cursor-pointer" @click="openImage(data.produk.gambar)" />
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" header="Nama Produk">
                        <template #body="{ data }">
                            {{ data.produk.kode_produk }} - {{ data.produk.nama_produk }}
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" header="Bahan Baku" class="w-fit">
                        <template #body="{ data }">
                            <Button v-tooltip.top="'Lihat Bahan Baku'" size="small" icon="pi pi-eye" outlined rounded severity="success" class="mr-2" label="Detail" @click="openLBB(data)"/>
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" field="kuantitas" header="Jumlah Produksi"></Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" header="Lokasi Penyimpanan" >
                        <template #body="{ data }">
                            <div class="text-primary font-medium">
                                {{ data.lokasi_penyimpanan.name }}
                                <span :class="{'text-red-300': data.suhu_penyimpanan > 30, 'text-green-300': data.suhu_penyimpanan >= 0, 'text-blue-300': data.suhu_penyimpanan < 0}">({{ data.suhu_penyimpanan + ' °C' }})</span>
                            </div>
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" header="Mulai Produksi">
                        <template #body="{ data }">
                            {{ data.mulai_produksi ? parseDate(data.mulai_produksi, 'DD/MM/YYYY HH:mm') : '-' }}
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" header="Selesai Produksi">
                        <template #body="{ data }">
                            {{ data.selesai_produksi ? parseDate(data.selesai_produksi, 'DD/MM/YYYY HH:mm') : '-' }}
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" header="Tanggal Kadaluarsa">
                        <template #body="{ data }">
                            {{ data.tanggal_kadaluarsa ? parseDate(data.tanggal_kadaluarsa, 'DD/MM/YYYY') : '-' }}
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" field="status" header="Status Produksi"></Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" header="QR Code">
                        <template #body="{ data }">
                            <img v-tooltip.top="`Lihat QR Code`" v-if="data.qrcode_path" :src="data.qrcode_path" alt="QR Code" class="w-15 shadow cursor-pointer" @click="openImage(data.qrcode_path)">
                            <span v-else>-</span>
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" header="Label">
                        <template #body="{ data }">
                            <img v-tooltip.top="`Lihat Label`" v-if="data.label_path" :src="data.label_path" alt="QR Code" class="w-15 shadow cursor-pointer" @click="openImage(data.label_path)">
                            <span v-else>-</span>
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 9rem;" header="">
                        <template #body="{ data }">
                            <Button icon="pi pi-file-pdf" severity="danger" outlined size="small" @click="exportPDF(data, 'detail_produksi')" />
                        </template>
                    </Column>
                    <template v-if="DataLaporan.length <= 0" #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>
                    </template>
                    <template v-if="DataLaporan.length > 0" #footer>
                        
                        <Paginator :pt="{paginatorContainer: 'w-full'}" :first="(DataMeta.current_page - 1) * DataMeta.per_page" :rows="DataMeta.per_page" :totalRecords="DataMeta.total" @page="(e) => toPage(e.page + 1)">
                            <template #start="slotProps">
                                <div class="w-full flex items-center gap-4">
                                    <span>Showing {{ slotProps.state.page + 1 }} to {{ DataMeta.last_page }} of {{ DataMeta.total }} entries</span>
                                    <Select class="w-fit" :options="[10, 25, 50]" :default-value="paginate" placeholder="Select..." @value-change="(value) => changePaginate(value)" />
                                </div>
                            </template>
                        </Paginator>
                    </template>
                </DataTable>
            </div>
        </div>
    </LayoutLaporan>

    <!-- LBB Dialog -->
    <Dialog v-model:visible="visibleLBB" modal header="Lihat Bahan Baku" class="w-2/3" :draggable="false" :closable="true">
        <DataTable :value="selectedBahanBaku" pt:table="min-w-200">
            <Column field="kode_lot" header="Kode LOT Bahan Baku"></Column>
            <Column field="kode_bahan" header="Kode Bahan"></Column>
            <Column field="nama_bahan" header="Nama Bahan"></Column>
            <Column header="Jumlah Penggunaan">
                <template #body="{ data }">
                    {{ data.jumlah_penggunaan }} {{ data.satuan }}
                </template>
            </Column>
            <Column field="nama_supplier" header="Nama Supplier"></Column>
        </DataTable>
    </Dialog>
</template>
<script setup lang="ts">
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import LayoutLaporan from './LayoutLaporan.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { formatDatefromString, parseDate, parseDateForDatePicker } from '@/composables/Helper';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import InputText from 'volt-components/InputText.vue';
import Button from 'primevue/button';
import Paginator from 'primevue/paginator';
import Dialog from 'volt-components/Dialog.vue';
import Select from 'primevue/select';
import FilterMenu from '@/components-project/filterMenu.vue';
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

const visibleDRP = ref(false);
const selectedDistribusi = ref(null);

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
    'kode distribusi',
    'jumlah tersisa',
    'tanggal pengiriman',
    'nama retailer',
    'alamat',
])

const rangeOption = ref([
    'tanggal pengiriman',
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

function submitSearch(){
    console.log("DataLaporan: ", DataLaporan.value);
    formatFilters();
    router.get(route('owner.laporan'), {laporan: props.laporan, s: searchValue.value, filter: submittedFilters});
}

function openInfo(data){
    console.log(data);
    visibleDRP.value = true;
    selectedDistribusi.value = data;
}

function toPage(page){
    router.get(route('owner.laporan'), {laporan: props.laporan, s: searchValue.value, page: page});
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
                            <span class="text-2xl font-bold border-b-2 dark:border-surface-400">Data Distribusi</span>
                        </div>
                    </template> -->
                    <Column field="kode_distribusi" header="Kode Distribusi" class="w-1"></Column>
                    <Column field="produksi.nomor_batch" header="Nomor Batch" class="w-1"></Column>
                    <Column field="produksi.produk.nama_produk" header="Nama Produk" class="w-1">
                        <!-- <template #body="{ data }">
                            <div class="flex flex-col justify-start items-start gap-2">
                                <span class="text-md">{{ data.produksi.produk.nama_produk }}</span>
                            </div>
                        </template> -->
                    </Column>
                    <Column field="nama_retailer" header="Nama Retailer" class="w-1"></Column>
                    <Column field="tanggal_pengiriman" header="Tanggal Pengiriman" class="w-1">
                        <template #body="{ data }">
                            {{ formatDatefromString(data.tanggal_pengiriman) }}
                        </template>
                    </Column>
                    <Column field="jumlah_tersisa" header="Jumlah Tersisa" class="w-1"></Column>
                    <Column style="max-width: 15rem; width: 5rem;" field="alamat" header="Alamat" class="wrap-break-word"></Column>
                    <Column header="" class="w-1">
                        <template #body="{ data }">
                            <div class="flex flex-col justify-center items-center gap-1">
                                <Button size="small" icon="pi pi-history" label="Riwayat Recall" outlined severity="secondary" @click="openInfo(data)" />
                            </div>
                        </template>
                    </Column>
                    <template #footer>
                        <div v-if="DataLaporan.length <= 0" class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>
                        <Paginator v-if="DataLaporan.length > 0" :pt="{paginatorContainer: 'w-full'}" :first="(DataMeta.current_page - 1) * DataMeta.per_page" :rows="DataMeta.per_page" :totalRecords="DataMeta.total" @page="(e) => toPage(e.page + 1)">
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

    <Dialog v-model:visible="visibleDRP" header="Detail Riwayat Recall" class="w-3/4" :modal="true" dismissable-mask>
        <div class="flex flex-col">
            <span class="flex items-center gap-2 text-surface-400">
                <span class="font-bold">History Recall</span>
                <i class="pi pi-history" size="1rem"></i>
                <div class="flex-1">
                    <Button icon="pi pi-file-pdf" label="PDF" severity="danger" outlined size="small" @click="exportPDF(selectedDistribusi, 'detail_distribusi')" />
                </div>
            </span>
            <DataTable striped-rows :value="selectedDistribusi.recall_produk">
                <Column header="#">
                    <template #body="{ index }">
                        <span class="font-bold">{{ index + 1 }}</span>
                    </template>
                </Column>
                <Column field="kode_recall" header="Kode Recall"></Column>
                <Column field="tanggal_recall" header="Tanggal Recall">
                    <template #body="{ data }">
                        {{ parseDate(data.tanggal_recall, 'YYYY MMM DD') }}
                    </template>
                </Column>
                <Column field="jumlah_recall" header="Jumlah Recall">
                    <template #body="{ data }">
                        <span class="font-bold text-center">{{ data.jumlah_recall }}</span>
                    </template>
                </Column>
                <Column field="alasan_recall" header="Alasan Recall">
                    <template #body="{ data }">
                        <span class="font-medium text-primary capitalize">{{ data.alasan_recall }}</span>
                    </template>
                </Column>
                <Column field="keterangan" header="Keterangan" class="max-w-xs">
                    <template #body="{ data }">
                        <div class="text-justify">
                            {{ data.keterangan }}
                        </div>
                    </template>
                </Column>
                <Column field="user.name" header="Direcall oleh"></Column>
                <template #empty>
                    <div class="text-center py-5 text-surface-500">
                        Data Tidak Ditemukan
                    </div>                        
                </template>
            </DataTable>
        </div>
    </Dialog>
</template>
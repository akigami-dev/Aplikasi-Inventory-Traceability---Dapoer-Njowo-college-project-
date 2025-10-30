<script setup lang="ts">
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import LayoutLaporan from './LayoutLaporan.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputText from 'volt-components/InputText.vue';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { formatDecimal, parseDate, parseDateForDatePicker } from '@/composables/Helper';
import Paginator from 'primevue/paginator';
import Dialog from 'volt-components/Dialog.vue';
import Divider from 'volt-components/Divider.vue';
import Select from 'primevue/select';
import FilterMenu from '@/components-project/filterMenu.vue';
import DatePicker from 'primevue/datepicker';
import Badge from 'primevue/badge';

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

const visibleDL = ref(false);
const selectedDL = ref({});

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
    'kode lot',
    'status',
    'tanggal kadaluarsa',
    'jumlah tersisa',
])

const rangeOption = ref([
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
    router.get(route('owner.laporan'), {laporan: props.laporan, s: searchValue.value, page: DataMeta.value.current_page, filter: submittedFilters});
}

function toPage(page){
    router.get(route('owner.laporan'), {laporan: props.laporan, s: searchValue.value, page: page});
}

function openDL(data: object){
    console.log(data);
    visibleDL.value = true;
    selectedDL.value = data;
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
                    <Column field="kode_lot" header="Kode Lot"></Column>
                    <Column field="restok_bahan_baku.kode_restok" header="Kode Restok"></Column>
                    <Column header="Bahan Baku">
                        <template #body="{ data }">
                            {{ data.bahan_baku.kode_bahan }} - {{ data.bahan_baku.nama_bahan }}
                        </template>
                    </Column>
                    <Column header="Status">
                        <template #body="{ data }">
                            <span :class="data.status.toLowerCase() == 'tersedia' ? 'text-green-500' : 'text-red-500'" class="font-bold">
                                {{ data.status.toUpperCase() }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Tanggal Kadaluarsa">
                        <template #body="{ data }">
                            {{ parseDate(data.tanggal_kadaluarsa, 'DD/MM/YYYY') }}
                        </template>
                    </Column>
                    <Column header="Jumlah Tersisa">
                        <template #body="{ data }">
                            {{ formatDecimal(data.jumlah) }} {{ data.satuan }}
                        </template>
                    </Column>
                    <Column header="">
                        <template #body="{ data }">
                            <Button size="small" label="Detail" @click="openDL(data)"></Button>
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

        <Dialog v-model:visible="visibleDL" modal header="Detail LOT" class="w-3/4" :maximizable="true" :draggable="false" :dismissable-mask="true">
            <div class="flex flex-col">
                <div class="flex-1 items-end">
                    <Button icon="pi pi-file-pdf" label="PDF" severity="danger" outlined size="small" @click="exportPDF(selectedDL, 'detail_lot')" />
                </div>
                <header class="flex gap-4 border-b border-surface-200 dark:border-surface-700 py-3">
                    <div class="flex-1 flex gap-2 text-lg">
                        <div class="w-fit flex flex-col gap-2 mr-10">
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Kode Lot</span> 
                                <span class="flex-1 font-medium">
                                : {{ selectedDL.kode_lot }}
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Nama Bahan</span> 
                                <span class="flex-1 font-medium">
                                : {{ selectedDL.bahan_baku.nama_bahan }}
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Tanggal Kadaluarsa</span> 
                                <span class="flex-1 font-medium flex gap-2">
                                    <span>: {{ parseDate(selectedDL.tanggal_kadaluarsa, 'DD/MM/YYYY') }}</span>
                                    <!-- <span :class="selectedDL.status.toLowerCase() == 'tersedia' ? 'text-green-500' : 'text-red-500'" class="font-bold">{{ selectedDL.status.toUpperCase() }}</span> -->
                                </span>
                            </div>
                            <div class="flex gap-4">
                                <span class="min-w-[17ch] font-bold text-primary">Jumlah Tersisa</span> 
                                <span class="flex-1 font-medium">
                                : {{ formatDecimal(selectedDL.jumlah) }} {{ selectedDL.satuan }}
                                </span>
                            </div>
                        </div>
                        <div class="w-fit flex items-center justify-center p-4 border border-surface rounded-full bg-surface-50 dark:bg-surface-950 -rotate-20">
                            <img :src="'storage/images/assets/status_lot/' + selectedDL.status.toLowerCase() + '.png'" class="max-w-30" alt="status">
                        </div>
                    </div>
                    <Divider layout="vertical" />
                    <div class="flex-1 flex flex-col gap-2 text-lg">
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Supplier Bahan</span> 
                            <span class="flex-1 font-medium">
                            : {{ selectedDL.bahan_baku.supplier.nama_supplier }}
                            </span>
                        </div>
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Kode Supplier</span> 
                            <span class="flex-1 font-medium">
                            : {{ selectedDL.bahan_baku.supplier.kode_supplier }}
                            </span>
                        </div>
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Tanggal Restok</span> 
                            <span class="flex-1 font-medium">
                            : {{ parseDate(selectedDL.restok_bahan_baku.tanggal_restok, 'DD/MM/YYYY') }}
                            </span>
                        </div>
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Jumlah Restok</span> 
                            <span class="flex-1 font-medium">
                            : {{ formatDecimal(selectedDL.restok_bahan_baku.jumlah_restok) }} {{ selectedDL.restok_bahan_baku.satuan }}
                            </span>
                        </div>
                    </div>
                </header>
                <main class="flex flex-col main-container gap-4">
                    <span class="flex items-center gap-2">
                        <span class="font-bold dark:text-surface-400">History Penggunaan</span>
                        <i class="pi pi-history dark:text-surface-400" size="1rem"></i>
                    </span>
                    <DataTable class="text-sm" striped-rows :value="selectedDL.lot_penggunaan_logs">
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
                        <template v-if="DataLaporan.length <= 0" #empty>
                            <div class="text-center py-5 text-surface-500">
                                Data Tidak Ditemukan
                            </div>
                        </template>
                    </DataTable>
                </main>
            </div>
        </Dialog>
    </LayoutLaporan>
</template>
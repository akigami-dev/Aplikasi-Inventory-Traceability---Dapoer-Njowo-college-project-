<script setup lang="ts">
import { formatDatefromString, formatDecimal, parseDate, parseDateForDatePicker } from '@/composables/Helper';
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head, router } from '@inertiajs/vue3';
import Column from 'primevue/column';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Dialog from 'volt-components/Dialog.vue';
import Divider from 'volt-components/Divider.vue';
import InputText from 'volt-components/InputText.vue';
import { ref } from 'vue';
import DatePicker from 'primevue/datepicker';
import Select from 'primevue/select';
import Paginator from 'primevue/paginator';
import FilterMenu from '@/components-project/filterMenu.vue';

const props = defineProps({
    ...useDefineProps("Template"),
    lot_bahan_baku: Object,
    search: String,
    filter: Object,
    paginate: Number,
})

defineOptions({
    layout: useAppLayout(),
})

const toast = useToast();
const confirm = useConfirm();

// VARIABLE SECTIONS
const searchValue = ref(props.search ?? '');
const visibleDL = ref(false);

const selectedDL = ref();

// FUNCTION SECTIONS
function submitSearch(){
    formatFilters();
    router.get(route('stafAdmin.lot_bahan_baku'), {s: searchValue.value.trim(), filter: submittedFilters});
}

function openDL(data: object){
    visibleDL.value = true;
    selectedDL.value = data;
}

const test = [
    {
        id: 1,
        kode: "A",
        nama: "A"
    },
    {
        id: 2,
        kode: "B",
        nama: "B"
    }
]

function toPage(page){
    router.get(window.location.pathname + window.location.search, {s: searchValue.value, page: page});
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
const changePaginate = (amount: number) => {
    router.get(window.location.pathname + window.location.search, {paginate: amount});
}

const lot_bahan_baku = ref([...props.lot_bahan_baku?.data as any]);
const DataMeta = ref({...props.lot_bahan_baku?.meta as any});

// function reset(){
//     lot_bahan_baku.value = [...props.lot_bahan_baku?.data as any];
//     DataMeta.value = {...props.lot_bahan_baku?.meta as any};
// }
</script>
<template>
    {{ console.log("PROPS: ", props.lot_bahan_baku) }}
    <Head :title="props.title" />
    <main>
        <div class="main-container flex flex-col border dark:border-0">
            <div class="flex-1">
                <form @submit.prevent="submitSearch" class="flex gap-2">
                    <InputText v-model="searchValue" placeholder="Cari..." />
                    <Button label="Search" type="submit" />
                </form>
                <FilterMenu class="py-4">
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
                <DataTable class="rounded-lg shadow !overflow-auto text-sm" show-gridlines striped-rows :value="lot_bahan_baku">
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold border-b-1 border-surface-300 dark:border-surface-600">Data Lot Bahan Baku</span>
                        </div>
                    </template>
                    <Column field="kode_lot" header="Kode LOT"></Column>
                    <Column field="bahan_baku.nama_bahan" header="Nama Bahan"></Column>
                    <Column header="Tanggal Kadaluarsa">
                        <template #body="{ data }">
                            {{ parseDate(data.tanggal_kadaluarsa, 'DD MMM YYYY') }}
                        </template>
                    </Column>
                    <Column header="Status">
                        <template #body="{ data }">
                            <span :class="data.status.toLowerCase() == 'tersedia' ? 'text-green-500' : 'text-red-500'" class="font-bold">
                                {{ data.status.toUpperCase() }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Jumlah">
                        <template #body="{ data }">
                            {{ formatDecimal(data.jumlah) }} {{ data.satuan }}
                        </template>
                    </Column>
                    <Column header="">
                        <template #body="{ data }">
                            <Button size="small" label="Detail" @click="openDL(data)" />
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>                        
                    </template>
                    <template v-if="lot_bahan_baku.length > 0" #footer>
                        
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
    </main>
    <Dialog v-model:visible="visibleDL" modal header="Detail LOT" class="w-3/4" :maximizable="true" :draggable="false" :dismissable-mask="true">
        <div class="flex flex-col">
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
                            <span class="min-w-[17ch] font-bold text-primary">Kode Bahan</span> 
                            <span class="flex-1 font-medium">
                            : {{ selectedDL.bahan_baku.kode_bahan }}
                            </span>
                        </div>
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Tanggal Kadaluarsa</span> 
                            <span class="flex-1 font-medium flex gap-2">
                                <span>: {{ parseDate(selectedDL.tanggal_kadaluarsa, 'DD MMM YYYY') }}</span>
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
                        <img :src="'storage/images/assets/status_lot/' + selectedDL.status.toLowerCase() + '.png'" class="max-w-40" alt="status">
                    </div>
                </div>
                <Divider layout="vertical" />
                <div class="flex-1 flex flex-col gap-2 text-lg">
                    <div class="flex gap-4">
                        <span class="min-w-[17ch] font-bold text-primary">Kode Restok</span> 
                        <span class="flex-1 font-medium">
                        : {{ selectedDL.restok_bahan_baku.kode_restok }}
                        </span>
                    </div>
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
                        : {{ parseDate(selectedDL.restok_bahan_baku.tanggal_restok, 'DD MMM YYYY') }}
                        </span>
                    </div>
                    <div class="flex gap-4">
                        <span class="min-w-[17ch] font-bold text-primary">Jumlah Restok</span> 
                        <span class="flex-1 font-medium">
                        : {{ formatDecimal(selectedDL.restok_bahan_baku.jumlah_restok) }} {{ 'gram' }}
                        </span>
                    </div>
                </div>
            </header>
            <main class="flex flex-col main-container gap-4">
                <span class="flex items-center gap-2">
                    <span class="font-bold dark:text-surface-400">History Penggunaan</span>
                    <i class="pi pi-history dark:text-surface-400" size="1rem"></i>
                </span>
                <DataTable show-gridlines striped-rows :value="selectedDL.lot_penggunaan_logs">
                    <Column field="nomor_batch" header="Nomor Batch"></Column>
                    <Column header="Tanggal Penggunaan">
                        <template #body="{ data }">
                            {{ parseDate(data.created_at, 'YYYY MMM DD') }}
                        </template>
                    </Column>
                    <Column header="Jumlah">
                        <template #body="{ data }">
                            {{ formatDecimal(data.jumlah) }} {{ 'gram' }}
                        </template>
                    </Column>
                    <Column field="user_name" header="User"></Column>
                    <template #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>                        
                    </template>
                </DataTable>
            </main>
        </div>
    </Dialog>
</template>
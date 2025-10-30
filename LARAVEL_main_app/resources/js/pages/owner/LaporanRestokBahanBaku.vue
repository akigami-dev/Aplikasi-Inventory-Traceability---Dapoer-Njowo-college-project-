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

// FUNCTION SECTIONS
const changePaginate = (amount: number) => {
    router.get(window.location.pathname + window.location.search, {paginate: amount});
}

const sortOption = ref([
    'created at',
    'kode restok',
    'jumlah restok',
    'tanggal restok',
])

const rangeOption = ref([
    'tanggal restok',
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
    console.log("DataLaporan: ", DataLaporan.value);
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

const exportPDF = (data) => {
    router.post(route('owner.laporan.export_pdf'), {
        laporan: props.laporan,
        dataLaporan: data,
    });
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
                    <Column field="kode_restok" header="Kode Restok"></Column>
                    <Column header="Bahan">
                        <template #body="{ data }">
                            {{ data.master_bahan_baku.kode_bahan }} - {{ data.master_bahan_baku.nama_bahan }}
                        </template>
                    </Column>
                    <Column header="Jumlah Restok">
                        <template #body="{ data }">
                            {{ formatDecimal(data.jumlah_restok) }} {{ data.satuan }}
                        </template>
                    </Column>
                    <Column header="Tanggal Restok">
                        <template #body="{ data }">
                            {{ parseDate(data.tanggal_restok, 'DD/MM/YYYY') }}
                        </template>
                    </Column>
                    <Column header="Supplier">
                        <template #body="{ data }">
                            {{ data.master_bahan_baku.supplier.kode_supplier }} - {{ data.master_bahan_baku.supplier.nama_supplier }}
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
</template>
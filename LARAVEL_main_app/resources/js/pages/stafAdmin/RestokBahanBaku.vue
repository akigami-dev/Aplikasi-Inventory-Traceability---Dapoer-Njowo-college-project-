<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head, router, useForm } from '@inertiajs/vue3';
import Column from 'primevue/column';
import DatePicker from 'volt-components/DatePicker.vue';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Dialog from 'volt-components/Dialog.vue';
import InputNumber from 'volt-components/InputNumber.vue';
import InputText from 'volt-components/InputText.vue';
import SecondaryButton from 'volt-components/SecondaryButton.vue';
import Select from 'volt-components/Select.vue';
import { ref, watch } from 'vue';
import { addConfirm, formatDatefromString, formatDatefromTimestamp, formatDateTimeForLaravel, formatDecimal, parseDate, parseDateForDatePicker } from '@/composables/Helper';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import addToast from '@/composables/addToast';
import FilterMenu from '@/components-project/filterMenu.vue';
import Paginator from 'primevue/paginator';

const props = defineProps({
    ...useDefineProps("Restok Bahan Baku"),
    restok: Object,
    bahan_baku: Object,
    search: String,
    filter: Object,
    paginate: Number,
})

defineOptions({
    layout: useAppLayout()
})

const toast = useToast();
const confirm = useConfirm();

// VARIABLE SECTIONS
const searchValue = ref(props.search ?? '');

const visibleDR = ref(false);
const actionDR = ref('create');
const satuan = [
    'gram',
    // 'kg',
    'ml',
    // 'liter',
]
const formDR = useForm({
    _method: 'post',
    bahan_baku_id: '',
    supplier: '',
    jumlah_restok: '',
    satuan: satuan[0],
    tanggal_restok: new Date(),
    tanggal_kadaluarsa: '',
    status: 'pending',
});

const selectedRestokID = ref();

// FUNCTION SECTIONS
function submitSearch(){
    formatFilters();
    router.get(route('stafAdmin.restok_bahan_baku'), {s: searchValue.value.trim(), filter: submittedFilters});
}
function submitDR(){
    formDR.tanggal_restok = formatDateTimeForLaravel(formDR.tanggal_restok);
    formDR.tanggal_kadaluarsa = formatDateTimeForLaravel(formDR.tanggal_kadaluarsa);
    const submitRoute = actionDR.value === 'create' ? 'stafAdmin.restok_bahan_baku.create.restok' : 'stafAdmin.restok_bahan_baku.update.restok';
 
    formDR.post(route(submitRoute, {restokBahanBaku: selectedRestokID.value ?? undefined}), {
        onSuccess: (response) => {
            console.log("SUCCESS: ", response);
            if(response.props && response.props?.toast?.severity){
                addToast(toast, response.props.toast);
            }
            visibleDR.value = false;
        },
        onError: (response) => {
            console.log("ERROR: ", response);
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }else{
                addToast(toast, {
                    severity: 'error',
                    summary: 'Create Restok Failed!',
                    detail: 'Restok gagal ditambahkan',
                    life: 5000,
                });
            }
        },
        preserveUrl: true,
        preserveScroll: true,
    })
}

function openTDR() {
    visibleDR.value = true;
    actionDR.value = 'create';
    formDR._method = 'post';
}

function openEDR(data: object) {
    // if(data.status !== 'pending'){
    //     addToast(toast, {
    //         severity: 'info',
    //         summary: 'Update Restok!',
    //         detail: 'Restok tidak dapat diubah, karena bahan baku sudah digunakan dalam produksi atau dihapus',
    //         life: 5000,
    //     })
    //     return;
    // }
    
    selectedRestokID.value = data.id;
    formDR.bahan_baku_id = data.master_bahan_baku.id;
    formDR.jumlah_restok = data.jumlah_restok;
    formDR.tanggal_restok = data.tanggal_restok;
    formDR.satuan = data.satuan;
    formDR.tanggal_kadaluarsa = data.tanggal_kadaluarsa;
    formDR.status = data.status;

    visibleDR.value = true;
    actionDR.value = 'edit';
    formDR._method = 'put';
}

function popupDelete(data: object) {
    addConfirm(confirm, {
        message: 'Apakah anda yakin ingin menghapus data restok ini?',
        accept: () => {
            router.delete(route('stafAdmin.restok_bahan_baku.delete.restok', {restokBahanBaku: data.id}), {
                onSuccess: (response) => {
                    if(response.props?.toast){
                        addToast(toast, response.props?.toast);
                    }
                    reset();
                },
                onError: (response) => {
                    if(response.props?.toast){
                        addToast(toast, response.props?.toast);
                    }
                },
                preserveUrl: true,
                preserveScroll: true,
            });
        }
    })
}

watch(visibleDR, (newValue) => {
    if(newValue === false){
        formDR.reset();
        formDR.clearErrors();
        selectedRestokID.value = null;
        reset();
    }
})

const restok = ref([...props.restok?.data as any]);
const DataMeta = ref({...props.restok?.meta as any});
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
    router.get(window.location.pathname + window.location.search, {s: searchValue.value, page: page});
}

function reset(){
    restok.value = [...props.restok?.data as any];
    DataMeta.value = {...props.restok?.meta as any};
}
</script>
<template>
    {{ console.log("PROPS: ", props) }}
    
    <Head :title="props.title" />
    <main>
        <div class="main-container flex flex-col border dark:border-0">
            <div class="flex-1">
                <form @submit.prevent="submitSearch" class="flex gap-2">
                    <InputText v-model="searchValue" placeholder="Cari..." />
                    <Button label="Search" type="submit" />
                </form>
            </div>
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
            <div class="flex-1">
                <DataTable class="rounded-lg shadow !overflow-auto text-sm" show-gridlines striped-rows :value="restok" pt:table="min-w-200">
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold border-b-1 border-surface-300 dark:border-surface-600">Data Restok</span>
                            <Button label="Tambah Data Restok" @click="openTDR()" />
                        </div>
                    </template>
                    <Column field="kode_restok" header="Kode Restok" class="w-1"></Column>
                    <Column field="kode_lot" header="Kode Lot" class="w-1"></Column>
                    <Column field="master_bahan_baku.nama_bahan" header="Bahan Baku" class="w-1"></Column>
                    <Column field="master_bahan_baku.supplier.nama_supplier" header="Supplier" class="w-1"></Column>
                    <Column field="tanggal_restok" header="Tanggal Restok" class="w-1">
                        <template #body="{ data }">
                            {{ formatDatefromString(data.tanggal_restok) }}
                        </template>
                    </Column>
                    <Column field="tanggal_kadaluarsa" header="Tanggal Kadaluarsa" class="w-1">
                        <template #body="{ data }">
                            {{ formatDatefromString(data.tanggal_kadaluarsa) }}
                        </template>
                    </Column>
                    <Column field="jumlah_restok" header="Jumlah Restok" class="w-1">
                        <template #body="{ data }">
                            {{ formatDecimal(data.jumlah_restok) }} {{ data.satuan }}
                        </template>
                    </Column>
                    <!-- <Column field="satuan" header="Satuan" class="w-1"></Column> -->
                    <Column header="Action" class="w-1">
                        <template #body="{ data }">
                            <div class="flex justify-start items-center gap-2">
                                <!-- <Button v-tooltip.top="data.status !== 'pending' ? 'Restok tidak dapat diubah, karena bahan baku sudah digunakan dalam produksi atau dihapus' : ''" size="small" icon="pi pi-pencil" outlined rounded severity="info" class="mr-2" @click="openEDR(data)" :disabled="data.status !== 'pending'" /> -->
                                <Button size="small" icon="pi pi-pencil" outlined rounded severity="info" class="mr-2" @click="openEDR(data)" />
                                <Button size="small" icon="pi pi-trash" outlined rounded severity="danger" class="mr-2" @click="popupDelete(data)" />
                            </div>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>                        
                    </template>
                    <template v-if="restok?.length > 0" #footer>
                        
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
    <Dialog v-model:visible="visibleDR" modal :header="actionDR === 'create' ? 'Tambah Data Restok' : 'Edit Data Restok'" class="w-1/4" :draggable="false">
        <form class="flex flex-col gap-2" @submit.prevent="submitDR">
            <div class="flex gap-5">
                <div class="flex flex-col items-start">
                    <label for="bahan_baku_id" class="font-semibold">Bahan Baku</label>
                    <Select filter v-model="formDR.bahan_baku_id" :options="bahan_baku" option-label="nama_bahan" option-value="id" id="kategori" placeholder="Select Bahan Baku" class="flex-auto w-auto" autocomplete="off" :disabled="formDR.status !== 'pending'">
                        <template #option="slotProps">
                            <div class="flex items-center gap-2">
                                <span class="text-primary">{{ slotProps.option.nama_bahan }} </span>- {{ slotProps.option.supplier.nama_supplier }}
                            </div>
                        </template>
                    </Select>
                    <InputError :message="formDR.errors.bahan_baku_id" class="mt-2" />
                </div>
                <!-- <div class="flex flex-col items-start">
                    <label for="supplier" class="font-semibold">Supplier</label>
                    <span>{{ bahan_baku.find((item) => item.id === formDR.bahan_baku_id)?.supplier?.nama_supplier }}</span>
                    <InputError :message="formDR.errors.supplier" class="mt-2" />
                </div> -->
            </div>
            <div class="flex items-start gap-2">
                <div class="flex flex-col items-start flex-1">
                    <label for="jumlah_restok" class="font-semibold">Jumlah Restok</label>
                    <InputNumber v-model="formDR.jumlah_restok" id="jumlah_restok" :max-fraction-digits="2" locale="id-ID" placeholder="0.00" class="flex-auto w-full" autocomplete="off" :disabled="formDR.status !== 'pending'" />
                    <InputError :message="formDR.errors.jumlah_restok" class="mt-2" />
                </div>
                <div class="flex flex-col items-start">
                    <label for="satuan" class="font-semibold">Satuan</label>
                    <Select v-model:model-value="formDR.satuan" :options="satuan" class="flex-auto w-full" autocomplete="off" :disabled="formDR.status !== 'pending'" />
                    <InputError :message="formDR.errors.satuan" class="mt-2" />
                </div>
            </div>
            <div class="flex items-start gap-2">
                <div class="flex-col flex-1">
                    <label for="tanggal_restok" class="font-semibold">Tanggal Restok</label>
                    <DatePicker v-model="formDR.tanggal_restok" showIcon fluid iconDisplay="input" inputId="icondisplay" dateFormat="dd/mm/yy" placeholder="dd/mm/yy" />
                    <InputError :message="formDR.errors.tanggal_restok" class="mt-2" />
                </div>
                <div class="flex-col flex-1">
                    <label for="tanggal_kadaluarsa" class="font-semibold">Tanggal Kadaluarsa</label>
                    <DatePicker v-model="formDR.tanggal_kadaluarsa" showIcon fluid iconDisplay="input" inputId="icondisplay" dateFormat="dd/mm/yy" placeholder="dd/mm/yy" />
                    <InputError :message="formDR.errors.tanggal_kadaluarsa" class="mt-2" />
                </div>
            </div>
            <span v-if="formDR.status !== 'pending'" class="text-sm text-surface-500">
                Note: beberapa data tidak dapat digunakan dikarenakan bahan baku telah digunakan dalam produksi
            </span>
            
            <!-- <div class="flex flex-col items-start">
                <label for="tanggal_kadaluarsa" class="font-semibold">Tanggal Kadaluarsa</label>
                <DatePicker v-model="formDR.tanggal_kadaluarsa" id="tanggal_kadaluarsa" class="flex-auto w-full" />
                <input type="date" v-model="formDR.tanggal_kadaluarsa" class="border border-gray-300 text-sm rounded-md w-full p-2.5">
                <DatePicker v-model="formDR.tanggal_kadaluarsa" showIcon fluid iconDisplay="input" inputId="icondisplay" dateFormat="dd/mm/yy" placeholder="dd/mm/yy" />
                <InputError :message="formDR.errors.tanggal_kadaluarsa" class="mt-2" />
            </div> -->
            
            <div class="flex justify-end mt-4 gap-2">
                <SecondaryButton type="button" label="Cancel" @click="visibleDR = false" />
                <Button :loading="formDR.processing" type="submit" label="Save" />
            </div>
        </form>
    </Dialog>
</template>
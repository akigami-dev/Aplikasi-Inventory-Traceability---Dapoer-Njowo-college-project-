<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import addToast from '@/composables/addToast';
import { addConfirm, parseDate, parseDateForDatePicker, sleep } from '@/composables/Helper';
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head, router, useForm } from '@inertiajs/vue3';
import Column from 'primevue/column';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import DatePicker from 'volt-components/DatePicker.vue';
import Dialog from 'volt-components/Dialog.vue';
import Divider from 'volt-components/Divider.vue';
import InputNumber from 'volt-components/InputNumber.vue';
import InputText from 'volt-components/InputText.vue';
import SecondaryButton from 'volt-components/SecondaryButton.vue';
import Select from 'volt-components/Select.vue';
import Textarea from 'volt-components/Textarea.vue';
import { ref, watch } from 'vue';
import FilterMenu from '@/components-project/filterMenu.vue';
import Paginator from 'primevue/paginator';

const props = defineProps({
    ...useDefineProps("Template"),
    recall_produk: Object,
    distribusi: Object,
    alasan_recall: Object,
    search: String,
    user_id: Number,
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
const visibleTDR = ref(false);
const actionTDR = ref('create');
const originalDistLength = ref(props.distribusi?.length ?? 0);
const distribusi = ref([...props.distribusi as any]);

const selectedDistribusi = ref();

const selectedEdit = useForm({
    'recall_produk_id': '',
    'distribusi_id': '',
    'tanggal_recall': new Date(),
    'jumlah_recall': null,
    'alasan_recall' : '',
    'keterangan': '',
});
const formTDR = useForm({
    'recall_produk_id': '',
    'distribusi_id': '',
    'tanggal_recall': new Date(),
    'jumlah_recall': null,
    'alasan_recall' : '',
    'keterangan': '',
    '_method': 'post',
});


// FUNCTION SECTIONS
function openTDR(){
    visibleTDR.value = true;
    actionTDR.value = 'create';
}

function submitSearch(){
    formatFilters();
    router.get(route('stafAdmin.recall_produk'), {s: searchValue.value.trim(), filter: submittedFilters});
}

function openDetail(data: object) {
    console.log(data);
}

function openETDR(data: object){
    visibleTDR.value = true;
    actionTDR.value = 'edit';

    formTDR._method = 'put';
    formTDR.recall_produk_id = data.id;
    formTDR.distribusi_id = data.distribusi.id;
    formTDR.tanggal_recall = new Date(data.tanggal_recall);
    formTDR.jumlah_recall = data.jumlah_recall;
    formTDR.alasan_recall = data.alasan_recall;
    formTDR.keterangan = data.keterangan;

    selectedEdit.recall_produk_id = data.id;
    selectedEdit.distribusi_id = data.distribusi.id;
    selectedEdit.tanggal_recall = new Date(data.tanggal_recall);
    selectedEdit.jumlah_recall = data.jumlah_recall;
    selectedEdit.alasan_recall = data.alasan_recall;
    selectedEdit.keterangan = data.keterangan;

    if(!props.distribusi?.find(item => item.id == formTDR.distribusi_id)){
        distribusi.value?.push(data.distribusi);
    }
    selectedDistribusi.value = data.distribusi;
}

function submitTDR(){
    formTDR.tanggal_recall = parseDate(formTDR.tanggal_recall);
    const submitRoute = actionTDR.value === 'create' ? 'stafAdmin.recall_produk.create.recall' : 'stafAdmin.recall_produk.update.recall';
    formTDR.post(route(submitRoute, {recall: formTDR.recall_produk_id}), {
        onSuccess: (response) => {
            if(response.props?.toast){
                addToast(toast, response?.props?.toast);
            }else{
                addToast(toast, {
                    severity: 'success',
                    summary: 'Create Data Recall Produk Success!',
                    detail: 'Data berhasil disimpan',
                    life: 5000,
                });
            }
            visibleTDR.value = false;
        },
        onError: (response) => {
            if(response.props?.toast){
                addToast(toast, response?.props?.toast);
            }else{
                addToast(toast, {
                    severity: 'error',
                    summary: 'Create Data Recall Produk Failed!',
                    detail: 'Data gagal disimpan',
                    life: 5000,
                });
            }
        },
        preserveUrl: true,
        preserveState: true,
    });
}

function popupDelete (data: object) {
    addConfirm(confirm, {
        message: 'Apakah anda yakin ingin menghapus data recall produk ini?',
        accept: () => {
            router.delete(route('stafAdmin.recall_produk.delete.recall', {recall: data.id}), {
                onSuccess: (response) => {
                    if(response.props?.toast){
                        addToast(toast, response?.props?.toast);
                    }else{
                        addToast(toast, {
                            severity: 'success',
                            summary: 'Delete Data Recall Produk Success!',
                            detail: 'Data berhasil dihapus',
                            life: 5000,
                        });
                    }
                    reset();
                },
                onError: (response) => {
                    if(response.props?.toast){
                        addToast(toast, response?.props?.toast);
                    }else{
                        addToast(toast, {
                            severity: 'error',
                            summary: 'Delete Data Recall Produk Failed!',
                            detail: 'Data gagal dihapus',
                            life: 5000,
                        });
                    }
                },
                preserveUrl: true,
                preserveScroll: true,
            });
        }
    })
}

function reset(){
    formTDR.reset();
    formTDR.clearErrors();
    selectedEdit.reset()
    selectedDistribusi.value = null;
    distribusi.value = [...props.distribusi as any];
    recall_produk.value = [...props.recall_produk?.data as any];
    DataMeta.value = {...props.recall_produk?.meta as any};
    actionTDR.value = 'create';
    formTDR._method = 'post';
}

watch(visibleTDR, (newValue, oldValue) => {
    if(newValue === false){
        reset();
    }
})

function toPage(page){
    router.get(window.location.pathname + window.location.search, {s: searchValue.value, page: page});
}

const sortOption = ref([
    'created at',
    'kode recall',
    'tanggal recall',
    'jumlah recall',
    'alasan recall',
    'keterangan',
])

const rangeOption = ref([
    'tanggal recall',
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

const recall_produk = ref([...props.recall_produk?.data as any]);
const DataMeta = ref({...props.recall_produk?.meta as any});

</script>
<template>
    {{ console.log("PROPS: ", props) }}
    <Head :title="props.title" />
    <main class="relative">
        <!-- <div class="absolute inset-0 z-10 bg-surface-800 opacity-50 flex justify-center items-center" :hidden="!formTDR.processing">
            <div class="flex flex-col items-center gap-2">
                <span class="text-white text-lg font-semibold">Loading</span>
                <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
            </div>
        </div> -->
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
                <DataTable class="rounded-lg shadow !overflow-auto text-sm" show-gridlines striped-rows  :value="recall_produk" pt:table="min-w-200">
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold border-b-1 border-surface-300 dark:border-surface-600">Data Recall Produk</span>
                            <Button label="Tambah Data Recall" @click="openTDR()" />
                        </div>
                    </template>
                    <Column field="kode_recall" header="Kode Recall"></Column>
                    <Column field="distribusi.kode_distribusi" header="Kode Distribusi"></Column>
                    <Column field="tanggal_recall" header="Tanggal Recall">
                        <template #body="{ data }">
                            {{ parseDate(data.tanggal_recall, 'DD/MM/YYYY HH:mm') }}
                        </template>
                    </Column>
                    <Column field="jumlah_recall" header="Jumlah Recall">
                        <template #body="{ data }">
                            {{ data.jumlah_recall + ' Produk' }}
                        </template>
                    </Column>
                    <Column field="alasan_recall" header="Alasan Recall">
                        <template #body="{ data }">
                            <span class="capitalize text-primary font-medium">
                                {{ data.alasan_recall }}
                            </span>
                        </template>
                    </Column>
                    <Column style="max-width: 15rem; width: 12rem;" field="keterangan" header="Keterangan" class="max-w-xs wrap-break-word">
                        <template #body="{ data }">
                            <div class="text-justify">
                                {{ data.keterangan }}
                            </div>
                        </template>
                    </Column>
                    <Column field="user.name" header="Direcall oleh"></Column>
                    <Column>
                        <template #body="{ data }">
                            <!-- <Button label="Detail" size="small" @click="openDetail(data)" /> -->
                            <!-- <i class="pi pi-pencil text-blue-400 cursor-pointer" style="font-size: 1.2rem" @click="openETDR(data)"></i>
                            <i class="pi pi-trash text-red-400 cursor-pointer" style="font-size: 1.2rem" @click="popupDelete(data.id)"></i> -->
                            <div class="flex justify-start items-center gap-2">
                                <i class="pi pi-pencil text-blue-400 cursor-pointer" style="font-size: 1.2rem" @click="openETDR(data)"></i>
                                <i class="pi pi-trash text-red-400 cursor-pointer" style="font-size: 1.2rem" @click="popupDelete(data)"></i>
                            </div>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>                        
                    </template>
                    <template v-if="recall_produk?.length > 0" #footer>
                        
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

    <!-- Dialog Sections -->
     
    <!-- Tambah Dialog -->
    <Dialog v-model:visible="visibleTDR" modal :header="actionTDR === 'create' ? 'Tambah Data Recall Produk' : 'Edit Data Recall Produk'" class="w-1/2" :draggable="false">
        <form class="flex flex-col gap-2" @submit.prevent="submitTDR">
            <div class="flex">
                <div class="flex-1/2 flex flex-col gap-3">
                    <div class="flex flex-col items-start gap-1">
                        <label for="bahan_baku_id" class="font-semibold">Select Distribusi</label>
                        <Select required v-model="formTDR.distribusi_id" :options="distribusi" option-value="id" option-label="kode_distribusi" placeholder="Select Distribusi" class="w-full" autocomplete="off"
                        @value-change="() => {
                            selectedDistribusi = distribusi?.find(item => item.id == formTDR.distribusi_id);
                            formTDR.jumlah_recall = null;
                        }"
                        >
                            <template #option="slotProps">
                                <div class="flex flex-col items-start gap-1" :class="{'text-green-300' : slotProps.option.id === formTDR.distribusi_id}">
                                <!-- <div class="flex flex-col items-start gap-1"> -->
                                    <span class="font-semibold">Kode Distribusi: {{ slotProps.option.kode_distribusi }}</span>
                                    <span class="font-semibold">Nomor Batch: {{ slotProps.option.produksi.nomor_batch }}</span>
                                    <span class="font-semibold">Nama Retailer: {{ slotProps.option.nama_retailer }}</span>
                                    <span class="font-semibold">Jumlah Tersisa: {{ slotProps.option.jumlah_tersisa }} </span>
                                </div>
                            </template>
                        </Select>
                        <InputError :message="formTDR.errors.distribusi_id" class="mt-2" />
                    </div>
                    <div class="flex flex-col items-start gap-1">
                        <label for="tanggal_recall" class="font-semibold">Tanggal Recall</label>
                        <DatePicker required v-model="formTDR.tanggal_recall" showTime fluid class="w-full" />
                        <InputError :message="formTDR.errors.tanggal_recall" class="mt-2" />
                    </div>
                    <div class="flex flex-col items-start gap-1">
                        <label for="tanggal_recall" class="font-semibold">Jumlah Recall</label>

                        <InputNumber v-if="actionTDR === 'create'" required v-tooltip.left="'Maks. Jumlah: ' + (selectedDistribusi?.jumlah_tersisa ?? 0)" 
                        v-model="formTDR.jumlah_recall" 
                        :max="selectedDistribusi?.jumlah_tersisa ?? 0" 
                        class="w-full" 
                        :disabled="selectedDistribusi ? false : true" />

                        <InputNumber v-else 
                        required 
                        v-tooltip.left="'Maks. Jumlah: ' + (selectedEdit.distribusi_id === formTDR.distribusi_id ? selectedDistribusi?.jumlah_tersisa + selectedEdit.jumlah_recall : selectedDistribusi?.jumlah_tersisa)" 
                        v-model="formTDR.jumlah_recall" 
                        :max="(selectedEdit.distribusi_id === formTDR.distribusi_id ? selectedDistribusi?.jumlah_tersisa + selectedEdit.jumlah_recall : selectedDistribusi?.jumlah_tersisa)" 
                        class="w-full" 
                        :disabled="selectedDistribusi ? false : true" />

                        <InputError :message="formTDR.errors.jumlah_recall" class="mt-2" />
                    </div>
                    <div class="flex flex-col items-start gap-1">
                        <label for="tanggal_recall" class="font-semibold">Alasan Recall</label>
                        <Select required v-model="formTDR.alasan_recall" :options="alasan_recall" label-class="capitalize" editable class="w-full">
                            <template #option="slotProps">
                                <span class="font-semibold capitalize">{{ slotProps.option }}</span>
                            </template>
                        </Select>
                        <InputError :message="formTDR.errors.alasan_recall" class="mt-2" />
                    </div>
                    <div class="flex flex-col items-start gap-1">
                        <label for="tanggal_recall" class="font-semibold">Keterangan</label>
                        <Textarea required v-model="formTDR.keterangan" class="w-full" rows="5" />
                        <InputError :message="formTDR.errors.keterangan" class="mt-2" />
                    </div>
                </div>
                <Divider layout="vertical" />
                <div class="flex-2/3 flex flex-col items-start gap-4">
                    <span class="font-bold">Detail Distribusi</span>
                    <div v-if="selectedDistribusi" class="flex flex-col gap-2 text-lg">
                        <img :src="selectedDistribusi.produksi.produk.gambar" alt="Gambar Produk" class="max-w-30 rounded" />
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Kode Distribusi</span> 
                            <span>:</span>
                            <span class="flex-1 font-medium">
                            {{ selectedDistribusi?.kode_distribusi }}
                            </span>
                        </div>
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Nomor Batch</span> 
                            <span>:</span>
                            <span class="flex-1 font-medium">
                            {{ selectedDistribusi?.produksi.nomor_batch }}
                            </span>
                        </div>
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Nama Retailer</span> 
                            <span>:</span>
                            <span class="flex-1 font-medium">
                            {{ selectedDistribusi?.nama_retailer }}
                            </span>
                        </div>
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Jumlah Tersisa</span> 
                            <span>:</span>
                            <span class="flex-1 font-medium">
                            {{ selectedDistribusi?.jumlah_tersisa }}
                            </span>
                        </div>
                        <div class="flex gap-4">
                            <span class="min-w-[17ch] font-bold text-primary">Alamat Retailer</span> 
                            <span>:</span>
                            <span class="flex-1 font-medium">
                            {{ selectedDistribusi?.alamat }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-4 gap-2">
                <SecondaryButton type="button" label="Cancel" @click="visibleTDR = false" />
                <Button :loading="formTDR.processing" type="submit" label="Save" />
            </div>
        </form>
    </Dialog>
</template>
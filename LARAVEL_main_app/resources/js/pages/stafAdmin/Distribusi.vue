<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import addToast from '@/composables/addToast';
import { addConfirm, formatDatefromString, formatDateTimeForLaravel, parseDate, parseDateForDatePicker } from '@/composables/Helper';
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
import InputText from 'volt-components/InputText.vue';
import SecondaryButton from 'volt-components/SecondaryButton.vue';
import Select from 'volt-components/Select.vue';
import Textarea from 'volt-components/Textarea.vue';
import { ref, watch } from 'vue';
import FilterMenu from '@/components-project/filterMenu.vue';
import Paginator from 'primevue/paginator';

const props = defineProps({
    ...useDefineProps("Template"),
    distribusi: Object,
    produksi: Object,
    allProduksi: Object,
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
const visibleDD = ref(false);
const visibleDRP = ref(false);
const actionDD = ref();
const isDateDisabled = ref(false);

const selectedProduksi = ref();
const selectedDistribusi = ref();
const selectedDistribusiId = ref();
const searchValue = ref(props.search ?? '');

const formDD = useForm({
    kode_distribusi: '',
    produksi_id: '',
    nama_retailer: '',
    alamat: '',
    jumlah_tersisa: '',
    tanggal_pengiriman: new Date(),
    _method: 'post',
})

// FUNCTION SECTIONS
function submitDD(){
    formDD.tanggal_pengiriman = formatDateTimeForLaravel(formDD.tanggal_pengiriman);
    const submitRoute = actionDD.value === 'create' ? 'stafAdmin.distribusi.create.distribusi' : 'stafAdmin.distribusi.update.distribusi';
    formDD.post(route(submitRoute, {distribusi: selectedDistribusiId.value}), {
        preserveUrl: true,
        preserveScroll: true,
        onSuccess: (response) => {
            visibleDD.value = false;
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }
        },
        onError: (response) => {
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }else{
                addToast(toast, {
                    severity: 'error',
                    summary: 'Failed!',
                    detail: 'Distribusi gagal ditambahkan / diubah ',
                    life: 5000,
                });
            }
        }
    })
}

function openTDD(){
    visibleDD.value = true;
    actionDD.value = 'create';
    formDD._method = 'post';
}

function openEDD(data: object){
    visibleDD.value = true;
    actionDD.value = 'update';

    formDD._method = 'put';
    formDD.kode_distribusi = data.kode_distribusi;
    formDD.produksi_id = data.produksi.id;
    formDD.nama_retailer = data.nama_retailer;
    formDD.alamat = data.alamat;
    formDD.tanggal_pengiriman = new Date(data.tanggal_pengiriman);
    formDD.jumlah_tersisa = data.jumlah_tersisa;

    const tanggalPengiriman = parseDate(String(formDD.tanggal_pengiriman), undefined, );
    isDateDisabled.value = data.editable;

    selectedProduksi.value = props.allProduksi.find(item => item.id == formDD.produksi_id);
    selectedDistribusiId.value = data.id;
}

function popupDelete(data: object){
    addConfirm(confirm, {
        message: 'Apakah anda yakin ingin menghapus data distribusi ini?',
        accept: () => {
            router.delete(route('stafAdmin.distribusi.delete.distribusi', {distribusi: data.id}), {
                onSuccess: (response) => {
                    if(response.props?.toast){
                        addToast(toast, response.props.toast);
                    }
                    reset();
                },
                onError: (response) => {
                    if(response.props?.toast){
                        addToast(toast, response.props.toast);
                    }else{
                        addToast(toast, {
                            severity: 'error',
                            summary: 'Delete Data Distribusi Failed!',
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

function submitSearch(){
    formatFilters();
    router.get(route('stafAdmin.distribusi'), {s: searchValue.value.trim(), filter: submittedFilters});
}

function openInfo(data: object){
    console.log(data);
    visibleDRP.value = true;
    selectedDistribusi.value = data;
}

watch(visibleDD, (newValue, oldValue) => {
    if(newValue === false){
        formDD.reset();
        formDD.clearErrors();
        selectedProduksi.value = null;
        selectedDistribusiId.value = null;
        isDateDisabled.value = false;
        reset();
    }
})

function toPage(page){
    router.get(window.location.pathname + window.location.search, {s: searchValue.value, page: page});
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
const changePaginate = (amount: number) => {
    router.get(window.location.pathname + window.location.search, {paginate: amount});
}

const distribusi = ref([...props.distribusi?.data as any]);
const DataMeta = ref({...props.distribusi?.meta as any});
function reset(){
    distribusi.value = [...props.distribusi?.data as any];
    DataMeta.value = {...props.distribusi?.meta as any};
}

</script>
<template>

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
                <DataTable class="rounded-lg shadow !overflow-auto text-sm" show-gridlines striped-rows :value="distribusi" pt:table="min-w-200">
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold border-b-1 border-surface-300 dark:border-surface-600">Data Distribusi</span>
                            <Button label="Tambah Data Distribusi" @click="openTDD()" />
                        </div>
                    </template>
                    <Column style="min-width: 4rem; max-width: 10rem;" field="kode_distribusi" header="Kode Distribusi" class="w-1"></Column>
                    <Column style="min-width: 4rem; max-width: 10rem;" field="produksi.nomor_batch" header="Nomor Batch" class="w-1"></Column>
                    <Column style="min-width: 4rem; max-width: 10rem;" field="produksi.produk.nama_produk" header="Nama Produk" class="w-1">
                    </Column>
                    <Column style="min-width: 4rem; max-width: 10rem;" field="nama_retailer" header="Nama Retailer" class="w-1"></Column>
                    <Column style="min-width: 4rem; max-width: 10rem;" field="jumlah_tersisa" header="Jumlah Distribusi Tersisa" class="w-1"></Column>
                    <Column style="min-width: 4rem; max-width: 10rem;" field="tanggal_pengiriman" header="Tanggal Pengiriman" class="w-1">
                        <template #body="{ data }">
                            {{ formatDatefromString(data.tanggal_pengiriman) }}
                        </template>
                    </Column>
                    <Column style="max-width: 10rem; width: 5rem;" field="alamat" header="Alamat" class="wrap-break-word">
                    </Column>
                    <Column style="min-width: 4rem; max-width: 10rem;" header="Riwayat Recall" class="w-1">
                        <template #body="{ data }">
                            <!-- <div class="flex flex-col justify-center items-center gap-1">
                                <i v-tooltip.top="'Lihat Riwayat Recall'" class="pi pi-history text-primary-400 cursor-pointer" style="font-size: 1.2rem;" @click="openInfo(data)"></i>
                                <p class="font-medium text-primary-300 dark:text-primary-200">Riwayat Recall</p>
                            </div> -->

                            <Button size="small" icon="pi pi-history" label="Detail Recall" outlined severity="secondary" @click="openInfo(data)" />
                        </template>
                    </Column>
                    <Column style="min-width: 4rem; max-width: 10rem;" header="Action" class="w-1" >
                        <template #body="{ data }">
                            <div class="flex justify-start items-center gap-2">
                                <i class="pi pi-pencil text-blue-400 cursor-pointer" style="font-size: 1.2rem" @click="openEDD(data)"></i>
                                <i class="pi pi-trash text-red-400 cursor-pointer" style="font-size: 1.2rem" @click="popupDelete(data)"></i>
                            </div>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>                        
                    </template>
                    <template v-if="distribusi.length > 0" #footer>
                        
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

    <!-- DD Dialog -->
     <Dialog v-model:visible="visibleDD" modal :header="actionDD === 'create' ? 'Tambah Data Distribusi' : 'Edit Data Distribusi'" class="w-1/2" :draggable="false">
        <form class="flex flex-col gap-2" @submit.prevent="submitDD">
            <div class="flex">
                <div class="flex flex-col flex-1 gap-2">
                    <div class="flex flex-col items-start gap-1">
                        <label for="produksi_id" class="font-semibold">Nomor Batch</label>
                        <!-- CREATE -->
                        <Select v-if="actionDD === 'create'" v-model="formDD.produksi_id" :options="produksi" optionLabel="nomor_batch" option-value="id" placeholder="Select Batch..." class="flex-auto w-full" 
                        @change="(event) => {
                            selectedProduksi = produksi?.find(item => item.id == formDD.produksi_id);
                            formDD.jumlah_tersisa = selectedProduksi.kuantitas;
                        }">
                            <template #option="slotProps">
                                <div class="flex flex-col items-start gap-1" :class="[slotProps.option.id === formDD.produksi_id ? 'text-green-300' : '']">
                                    <span class="font-semibold">Nomor Batch: {{ slotProps.option.nomor_batch }}</span>
                                    <span class="font-semibold">Produk: {{ slotProps.option.produk.nama_produk }} - {{ slotProps.option.produk.kode_produk }}</span>
                                    <span class="font-semibold">Jumlah produksi: {{ slotProps.option.kuantitas }} </span>
                                    <span class="font-semibold">Tanggal produksi: {{ parseDate(slotProps.option.mulai_produksi, "DD-MM-YYYY HH:mm") }} </span>
                                </div>
                            </template>
                        </Select>

                        <!-- EDIT -->
                        <Select v-if="actionDD !== 'create'" v-model="formDD.produksi_id" :options="allProduksi" optionLabel="nomor_batch" option-value="id" placeholder="Select Batch..." class="flex-auto w-full" disabled
                        @change="(event) => {
                            selectedProduksi = allProduksi?.find(item => item.id == formDD.produksi_id);
                        }" />
                        
                        <InputError :message="formDD.errors.produksi_id" class="mt-2" />
                    </div>
                    <div v-if="formDD.produksi_id" class="flex flex-col items-start gap-1 p-2">
                        <div class="flex gap-2">
                            <label class="w-40">Produk</label>
                            <span>:</span>
                            <span class="font-semibold">{{ selectedProduksi?.produk?.nama_produk }} [{{ selectedProduksi?.produk?.kode_produk }}]</span>
                        </div>
                        <div class="flex gap-2">
                            <label class="w-40">Jumlah Produksi</label>
                            <span>:</span>
                            <span class="font-semibold">{{ selectedProduksi?.kuantitas }}</span>
                        </div>
                        <div class="flex gap-2">
                            <label class="w-40">Tanggal Kadaluarsa</label>
                            <span>:</span>
                            <span class="font-semibold">{{ parseDate(selectedProduksi?.tanggal_kadaluarsa, 'DD MMM YYYY') }}</span>
                        </div>
                        <div class="flex gap-2">
                            <img :src="selectedProduksi?.produk?.gambar" alt="image" class="w-1/3 rounded object-cover" />
                        </div>
                    </div>
                </div>
                <Divider layout="vertical" />
                <div class="flex flex-col flex-1 gap-2">
                    <div class="flex flex-col items-start gap-1">
                        <label for="nama_retailer" class="font-semibold">Nama Retailer</label>
                        <InputText v-model="formDD.nama_retailer" id="nama_retailer" class="flex-auto w-full" autocomplete="off" />
                        <InputError :message="formDD.errors.nama_retailer" class="mt-2" />
                    </div>
                    <div class="flex flex-col items-start gap-1">
                        <label for="alamat" class="font-semibold">Alamat</label>
                        <Textarea v-model="formDD.alamat" id="alamat" rows="3" :auto-resize="true" class="flex-auto w-full" autocomplete="on" />
                        <InputError :message="formDD.errors.alamat" class="mt-2" />
                    </div>
                    <div class="flex flex-col items-start gap-1">
                        <label for="tanggal_pengiriman" class="font-semibold">Tanggal Pengiriman</label>
                        <DatePicker v-model="formDD.tanggal_pengiriman" showIcon fluid iconDisplay="input" inputId="icondisplay" dateFormat="dd/mm/yy" placeholder="dd/mm/yy" :disabled="isDateDisabled" />
                        <InputError :message="formDD.errors.tanggal_pengiriman" class="mt-2" />
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-4 gap-2">
                <SecondaryButton type="button" label="Cancel" @click="visibleDD = false" />
                <Button :loading="formDD.processing" type="submit" label="Save" />
            </div>
        </form>
     </Dialog>

    <!-- Recall Detail -->
    <Dialog v-model:visible="visibleDRP" header="Detail Riwayat Recall" class="w-3/4" :modal="true" dismissable-mask>
        <div class="flex flex-col">
            <span class="flex items-center gap-2 text-surface-400">
                <span class="font-bold">History Recall</span>
                <i class="pi pi-history" size="1rem"></i>
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
                        {{ parseDate('2025-03-03', 'YYYY MMM DD') }}
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
                <template v-if="selectedDistribusi.recall_produk.length <= 0" #empty>
                    <div class="text-center py-5 text-surface-500">
                        Data Tidak Ditemukan
                    </div>                        
                </template>
            </DataTable>
        </div>
    </Dialog>
</template>
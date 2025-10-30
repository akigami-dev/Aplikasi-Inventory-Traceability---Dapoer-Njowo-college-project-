<script setup lang="ts">
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head, router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'volt-components/Dialog.vue';
import { ref, watch } from 'vue';
import { addConfirm, formatDecimalforInputNumber, formatIDR, parseDate, parseDateForDatePicker } from '@/composables/Helper';
import SecondaryButton from 'volt-components/SecondaryButton.vue';
import InputText from 'volt-components/InputText.vue';
import InputNumber from 'volt-components/InputNumber.vue';
import Select from 'volt-components/Select.vue';
import Divider from 'volt-components/Divider.vue';
import { useToast } from 'primevue/usetoast';
import addToast from '@/composables/addToast';
import InputError from '@/components/InputError.vue';
import { useConfirm } from 'primevue/useconfirm';
import FilterMenu from '@/components-project/filterMenu.vue';
import Paginator from 'primevue/paginator';

// DEFAULT SECTIONS
const props = defineProps({
    ...useDefineProps("Master Produk"),
    masterProduk: Object,
    sertifikasi: Object,
    kategori: Object,
    search: String,
    paginate: Number,
    filter: Object,
})

defineOptions({
    layout: useAppLayout()
})

const confirm = useConfirm();
const toast = useToast();

// VARIABLE SECTIONS
const kategori = ref([...props.kategori as any]);

// DP Dialog
const visibleDP = ref(false);
const actionDP = ref();

// Data for DP Dialog
const imageBLOB = ref();
const labelBLOB = ref();
const selectedProdukID = ref();
const formDP = useForm({
    nama_produk: '',
    gambar: '',
    template_label: '',
    kategori: '',
    harga: 0,
    berat_bersih: 0,
    satuan_berat: 'ml',
    sertifikasi: [],
    _method: 'post',
})

// Lihat Sertifikasi Dialog
const visibleLS = ref(false);
const selectedSertifikasi = ref()
const selectedProdukSertifikasi = ref();

// FUNCTION SECTIONS

// function to open LS dialog
function openLSDialog(data: object, kodeProduk: string, namaProduk: string){
    selectedSertifikasi.value = data;
    selectedProdukSertifikasi.value = {
        kodeProduk: kodeProduk,
        namaProduk: namaProduk,
    }
    visibleLS.value = true;
}

// function to open create DP dialog
function openTDPDialog(){
    visibleDP.value = true;
    actionDP.value = 'create';
}

// function to open edit DP dialog
function openEDPDialog(){
    visibleDP.value = true;
    actionDP.value = 'edit';
}

// function to handle file image
function handleFileChange(event) {
    const file = event.target.files[0];
    if(file){
        imageBLOB.value = {
            image: URL.createObjectURL(file),
            name: file.name,
        };
        formDP.gambar = file
    }
}

function handleFileLabel(event){
    const file = event.target.files[0];
    if(file){
        labelBLOB.value = {
            image: URL.createObjectURL(file),
            name: file.name,
        };
        formDP.template_label = file
    }
}

// function to close DP dialog
function closeDP(){
    visibleDP.value = false;
}

// function to submit formDP
function submitDP(){
    console.log("FORM: ", formDP);
    beforeSubmitDP();
    const submitRoute = actionDP.value === 'create' ? 'stafAdmin.master_produk.create.data_produk' : 'stafAdmin.master_produk.update.data_produk';
    formDP.post(route(submitRoute, {MasterProduk: selectedProdukID.value ?? undefined}), {
        forceFormData: true,
        onSuccess: (response) => {
            console.log("SUCCESS: ", response);
            if(response.props && response.props?.toast?.severity){
                addToast(toast, response.props.toast);
            }else{
                addToast(toast, {
                    severity: 'success',
                    summary: 'Create Data Produk Success!',
                    detail: 'Data berhasil ditambahkan',
                    life: 5000,
                });
            }
            visibleDP.value = false;
        },
        onError: (response)=>{
            console.log("ERROR: ", response);
            if(response.props && response.props?.toast?.severity){
                addToast(toast, response.props.toast);
            }else{
                addToast(toast, {
                    severity: 'error',
                    summary: 'Create Data Produk Failed!',
                    detail: 'Data gagal ditambahkan',
                    life: 5000,
                });
            }
        },
        preserveUrl: true,
        preserveScroll: true,
    })
}

// function for Edit DP dialog
function edit(data: object){
    console.log(data);
    const katID = props.kategori.find(k => k.id === data.kategori.id);
    
    if(katID){
        console.log("LMAO");
        formDP.kategori = parseInt(katID.id);
    }else{
        kategori.value.push(data.kategori);
        formDP.kategori = data.kategori.id;
    }

    imageBLOB.value = {
        image: data.gambar,
        name: "",
    }
    labelBLOB.value = {
        image: data.template_label,
        name: "",
    }

    formDP.nama_produk = data.nama_produk;
    formDP.harga = data.harga;
    formDP.berat_bersih = data.berat_bersih;
    formDP.satuan_berat = data.satuan;
    formDP.sertifikasi = data.sertifikasi.map((item: object) => {
        return {
            sertifikasi: props.sertifikasi.find(s => s.id === item.id),
            kode_sertifikasi: item.kode_sertifikasi,
        }
    });
    selectedProdukID.value = data.id;
    formDP._method = 'put';
    openEDPDialog();

    console.log("NGETSE: ", formDP);
}

// function for Popup then Delete data
function popupDelete(data: object){
    addConfirm(confirm, {
        icon: 'pi pi-trash',
        accept: () => {
            router.delete(route('stafAdmin.master_produk.delete.data_produk', {MasterProduk: data.id}), {
                onSuccess: (response) => {
                    if(response.props.toast){
                        addToast(toast, response.props.toast);
                    }
                    reset();
                },
                onError: (response) => {
                    if(response.props.toast){
                        addToast(toast, response.props.toast);
                    }
                },
                preserveUrl: true,
                preserveScroll: true,
            });
        }
    });
}

// function for before formDP submitted
function beforeSubmitDP(){
    formDP.nama_produk = formDP.nama_produk.trim();
}

// function to add empty sertifikasi to formDP
function addEmptySertifikasi_formDP(){
    if(props.sertifikasi?.length !== 0){
        const dumpData = {
            sertifikasi: null,
        };
        formDP.sertifikasi.push(dumpData);
        return;
    }

    addToast(toast, {
        severity: 'error',
        summary: 'Sertifikasi Tidak Tersedia!',
        detail: 'Sertifikasi tidak tersedia, tidak dapat menambahkan sertifikasi',
        life: 5000,
    });
}

// function to remove sertifikasi from formDP by clicking the delete icon
function deleteSertifikasi_formDP(index: number){
    formDP.sertifikasi.splice(index, 1);
}

// function to format the error message for sertifikasi id
function errorSertifikasiId(error: string){
    if(!error) return null;
    return error.replace(/The .+ field /, '').replace('The ', '');
}

// function to format the header DP dialog
function headerDP(){
    return actionDP.value === 'create' ? 'Tambah Data Produk' : 'Edit Data Produk';
}

// EVENT SECTIONS

// event handler for when DP dialog is closed
watch(visibleDP, (newValue, oldValue) => {
    if(newValue === false){
        formDP.reset();
        formDP.clearErrors();
        imageBLOB.value = null;
        labelBLOB.value = null;
        selectedProdukID.value = null;
        reset();
    }
})

// event handler for when LS dialog is closed
watch(visibleLS, (newValue, oldValue) => {
    if(newValue === false){
        selectedSertifikasi.value = null;
        selectedProdukSertifikasi.value = null;
    }
})
const submitSearch = () => {
    formatFilters();
    router.get(route('stafAdmin.master_produk'), {s: searchValue.value.trim(), filter: submittedFilters});
}

const searchValue = ref(props.search ?? '');
const masterProduk = ref([...props.masterProduk?.data as any]);
const DataMeta = ref({...props.masterProduk?.meta as any});
const changePaginate = (amount: number) => {
    router.get(window.location.pathname + window.location.search, {paginate: amount});
}
const sortOption = ref([
    'created at',
    'kode produk',
    'nama produk',
    'kategori',
    'harga',
    'berat bersih',
    'satuan berat',
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
    router.get(window.location.pathname + window.location.search, {page: page});
}

function reset(){
    masterProduk.value = [...props.masterProduk?.data as any];
    DataMeta.value = {...props.masterProduk?.meta as any};
    kategori.value = [...props.kategori as any];
}
</script>

<template>
    {{ console.log("PROPS: ", props) }}
    <Head :title="props.title" />
    <main>
        <div class="main-container flex flex-col border dark:border-0 gap-2">
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
                </div>
                <Button label="Filter" size="small" @click="submitSearch" />
            </FilterMenu>
            <div class="flex-1">
                <DataTable class="rounded-lg shadow !overflow-auto text-sm" show-gridlines striped-rows :value="masterProduk" pt:table="min-w-200">
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold border-b-2 dark:border-surface-400">Master Produk</span>
                            <Button label="Tambah Data Produk" @click="openTDPDialog()" />
                        </div>
                    </template>
                    <Column style="min-width: 0rem; max-width: 7rem;" field="kode_produk" header="Kode Produk"></Column>
                    <Column style="min-width: 0rem; max-width: 7rem;" field="nama_produk" header="Nama Produk"></Column>
                    <Column style="min-width: 0rem; max-width: 7rem;" field="gambar" header="Gambar">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <img :src="data.gambar" alt="image" class="max-w-20 rounded object-cover" />
                            </div>
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 7rem;" field="template_label" header="Template Label">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <img :src="data.template_label" alt="image" class="max-w-20 rounded object-cover" />
                            </div>
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 7rem;" field="sertifikasi" header="Sertifikasi">
                        <template #body="{ data }">
                            <Button label="Sertifikasi" size="small" icon="pi pi-file" outlined rounded severity="info" @click="openLSDialog(data.sertifikasi, data.kode_produk, data.nama_produk)" />
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 7rem;" field="kategori.name" header="Kategori"></Column>
                    <Column style="min-width: 0rem; max-width: 7rem;" field="berat_bersih" header="Berat Bersih">
                        <template #body="{ data }">
                            {{ formatDecimalforInputNumber(data.berat_bersih) }} {{ data.satuan }}
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 7rem;" field="harga" header="Harga">
                        <template #body="{ data }">
                            <div class="flex items-center">
                                {{ formatIDR(data.harga) }}
                            </div>
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 7rem;" header="Action">
                        <template #body="{ data }">
                            <div class="flex justify-start items-center gap-2">
                                <Button icon="pi pi-pencil" size="small" severity="info" outlined rounded @click="edit(data)" />
                                <Button icon="pi pi-trash" size="small" severity="danger" outlined rounded @click="popupDelete(data)" />
                            </div>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>                        
                    </template>
                    <template v-if="masterProduk.length > 0" #footer>
                        
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

    <!-- Lihat Sertifikasi DIALOG -->
    <Dialog v-model:visible="visibleLS" modal header="Lihat Sertifikasi" class="w-auto" :draggable="false">
        <DataTable v-if="selectedSertifikasi" :value="selectedSertifikasi" pt:table="min-w-full" >
            <template #header>
                <div class="flex flex-col justify-start mb-2 text-md">
                    <span class="font-semibold">
                        Kode: <span class="font-normal">{{ selectedProdukSertifikasi.kodeProduk }}</span>
                    </span>
                    <span class="font-semibold capitalize">
                        Produk: <span class="font-normal">{{ selectedProdukSertifikasi.namaProduk }}</span>
                    </span>
                </div>
            </template>
            <Column field="nama_sertifikasi" header="Nama Sertifikasi" class="w-1"></Column>
            <Column field="badan_penerbit" header="Badan Penerbit" class="w-1"></Column>
            <Column field="logo_path" header="Logo" class="w-1">
                <template #body="{ data }">
                    <div class="flex items-center gap-2">
                        <img :src="data.logo_path" alt="image" class="max-w-8" />
                    </div>
                </template>
            </Column>
            <Column field="kode_sertifikasi" header="Kode Sertifikasi" class="w-1"></Column>
            <template #empty>
                <div class="text-center py-5 text-surface-500">
                    Data Tidak Ditemukan
                </div>
            </template>
            
        </DataTable>
    </Dialog>

    <!-- Data Produk DIALOG -->
    <Dialog v-model:visible="visibleDP" modal :header="headerDP()" class="w-2/3" :draggable="false" :closable="false">
        <form class="flex flex-col gap-2" @submit.prevent="submitDP">
            <div class="flex gap-5">
                <div class="flex flex-col flex-1">
                    <div class="flex justify-between py-2">
                        <span class="text-surface-500 dark:text-surface-400 block">Data Produk</span>
                    </div>
                    <div class="flex flex-col items-start gap-2">
                        <label for="nama_produk" class="font-semibold">Nama Produk</label>
                        <InputText v-model="formDP.nama_produk" id="nama_produk" class="flex-auto w-full" autocomplete="off" />
                        <InputError :message="formDP.errors.nama_produk" class="mt-2" />
                    </div>
                    <div class="flex flex-col items-start gap-2">
                        <label for="kategori" class="font-semibold">Kategori</label>
                        <Select v-model="formDP.kategori" :options="kategori" option-label="name" option-value="id" id="kategori" class="flex-auto w-auto" autocomplete="off" />
                        <InputError :message="formDP.errors.kategori" class="mt-2" />
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1 flex flex-col items-start gap-2">
                            <label for="harga" class="font-semibold">Harga</label>
                            <InputNumber v-model="formDP.harga" inputId="currency-id" :min-fraction-digits="0" :max-fraction-digits="2" mode="currency" currency="IDR" locale="id-ID" fluid />
                            <InputError :message="formDP.errors.harga" class="mt-2" />
                        </div>
                        <div class="flex-1 flex flex-col items-start gap-2">
                            <label for="harga" class="font-semibold">Berat Bersih</label>
                            <div class="flex gap-2">
                                <InputNumber v-model="formDP.berat_bersih" :min-fraction-digits="0" :max-fraction-digits="2" locale="id-ID" fluid />
                                <Select v-model="formDP.satuan_berat" :options="['ml', 'liter', 'gram', 'kg']" ></Select>
                            </div>
                            <InputError :message="formDP.errors.berat_bersih" class="mt-2" />
                            <InputError :message="formDP.errors.satuan_berat" class="mt-2" />
                        </div>
                    </div>
                    <!-- GAMBAR -->
                    <div class="flex gap-2 mt-5">
                        <!-- GAMBAR -->
                        <div class="flex flex-col justify-between items-start gap-2 flex-1 py-2">
                            <div class="flex flex-col gap-2">
                                <p>Gambar</p>
                                <div id="gambar" v-if="imageBLOB">
                                    <img :src="imageBLOB.image" alt="image" class="w-1/3 rounded object-cover" />
                                </div>
                            </div>
                            <div>
                                <div class="flex flex-col justify-start items-start gap-2">
                                    <label for="input_file" class="border-1 border-primary-300 dark:border-surface-500 p-2 rounded cursor-pointer hover:bg-primary-100 dark:hover:bg-surface-700">Upload gambar</label>
                                    <p v-if="imageBLOB">{{ imageBLOB.name }}</p>
                                </div>
                                <input type="file" id="input_file" accept="image/*" @change="handleFileChange" hidden>
                                <InputError :message="formDP.errors.gambar" class="mt-2" />
                            </div>
                        </div>
                        <Divider layout="vertical" />
                        <!-- TEMPLATE LABEL -->
                        <div class="flex flex-col justify-between items-start gap-2 flex-1 py-2">
                            <div class="flex flex-col gap-2">
                                <p>Template Label</p>
                                <div id="gambar" v-if="labelBLOB">
                                    <img :src="labelBLOB.image" alt="image" class="w-1/3 rounded object-cover" />
                                </div>
                            </div>
                            <div>
                                <div class="flex flex-col justify-start items-start gap-2">
                                    <label for="input_file_label" class="border-1 border-primary-300 dark:border-surface-500 p-2 rounded cursor-pointer hover:bg-primary-100 dark:hover:bg-surface-700">Upload template</label>
                                    <p v-if="labelBLOB">{{ labelBLOB.name }}</p>
                                </div>
                                <input type="file" id="input_file_label" accept="image/*" @change="handleFileLabel" hidden>
                                <InputError :message="formDP.errors.template_label" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>
                <Divider layout="vertical" />
                <div class="flex flex-col flex-1">
                    <div class="flex justify-between py-2">
                        <p class="text-surface-500 dark:text-surface-400 block">Data Sertifikasi</p>
                        <i class="pi pi-plus-circle mr-5 cursor-pointer" style="font-size: 1.2rem" @click="addEmptySertifikasi_formDP()"></i>
                    </div>
                    <DataTable :value="formDP.sertifikasi" >
                        <Column field="nama_sertifikasi" header="Sertifikasi">
                            <template #body="{ data, index }">
                                <div class="flex items-center gap-2">
                                    <Select v-model="data.sertifikasi" :options="sertifikasi" option-label="nama_sertifikasi" placeholder="Select Sertifikasi..." required>
                                        <template #option="{ option }">
                                            <div class="flex flex-col gap-1">
                                                <span>sertifikasi: {{option.nama_sertifikasi}}</span>
                                                <span>kode sertifikasi: {{option.kode_sertifikasi}}</span>
                                            </div>
                                        </template>
                                    </Select>
                                    <InputError :message="errorSertifikasiId(formDP.errors[`sertifikasi.${index}.sertifikasi.id`])" class="mt-2" />
                                </div>
                            </template>
                        </Column>
                        <Column field="kode_sertifikasi" header="Kode Sertifikasi">
                            <template #body="{ data }">
                                <div class="flex flex-col gap-2">
                                    {{ data.sertifikasi?.kode_sertifikasi ?? '' }}
                                    <InputError :message="formDP.errors[`sertifikasi.${index}.kode_sertifikasi`]" />
                                </div>
                            </template>
                        </Column>
                        <Column>
                            <template #body="{index}">
                                <i class="pi pi-times-circle text-red-400 cursor-pointer" @click="deleteSertifikasi_formDP(index)"></i>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <SecondaryButton type="button" label="Cancel" @click="closeDP()" />
                <Button type="submit" label="Save" :loading="formDP.processing" />
            </div>
        </form>
    </Dialog>
</template>
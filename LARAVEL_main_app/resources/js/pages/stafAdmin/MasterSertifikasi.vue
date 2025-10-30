<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import addToast from '@/composables/addToast';
import { addConfirm, parseDate, parseDateForDatePicker } from '@/composables/Helper';
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head, router, useForm } from '@inertiajs/vue3';
import Column from 'primevue/column';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Dialog from 'volt-components/Dialog.vue';
import InputText from 'volt-components/InputText.vue';
import SecondaryButton from 'volt-components/SecondaryButton.vue';
import { ref, watch } from 'vue';
import FilterMenu from '@/components-project/filterMenu.vue';
import Select from 'primevue/select';
import Paginator from 'primevue/paginator';

const props = defineProps({
    ...useDefineProps("Master Sertifikasi"),
    sertifikasi: Object,
    search: String,
    paginate: Number,
    filter: Object,
})

defineOptions({
    layout: useAppLayout()
})

const toast = useToast();
const confirm = useConfirm();

// VARIABLE SECTIONS
const visibleDS = ref(false);
const imageBLOB = ref();
const actionDS = ref();
const selectedSertifikasiID = ref();

// Form DS
const formDS = useForm({
    nama_sertifikasi: "",
    badan_penerbit: "",
    logo: null,
    kode_sertifikasi: "",
    _method: 'post',
});


// FUNCTION SECTIONS
function openTDS(){
    visibleDS.value = true;
    actionDS.value = 'create';
    formDS._method = 'post';
}

function openEDS(data: object){
    selectedSertifikasiID.value = data.id;
    formDS.nama_sertifikasi = data.nama_sertifikasi;
    formDS.badan_penerbit = data.badan_penerbit;
    formDS.kode_sertifikasi = data.kode_sertifikasi;
    imageBLOB.value = {
        image: data.logo_path,
        name: '',
    }

    visibleDS.value = true;
    actionDS.value = 'edit';
    formDS._method = 'put';
}

function handleFileChange(event){
    const file = event.target.files[0];
    if(file){
        imageBLOB.value = {
            image: URL.createObjectURL(file),
            name: file.name,
        };
        formDS.logo = file;
    }

}

function submitDS(){
    const submitRoute = actionDS.value === 'create' ? 'stafAdmin.master_sertifikasi.create.data_sertifikasi' : 'stafAdmin.master_sertifikasi.update.data_sertifikasi';
    formDS.post(route(submitRoute, {sertifikasi: selectedSertifikasiID.value ?? undefined}), {
        onSuccess: (response) => {
            console.log("SUCCESS: ", response);
            if(response.props && response.props?.toast?.severity){
                addToast(toast, response.props.toast);
            }
            visibleDS.value = false;
        },
        onError: (response) => {
            console.log("ERROR: ", response);
            if(response.props && response.props?.toast?.severity){
                addToast(toast, response.props.toast);
            }else{
                addToast(toast, {
                    severity: 'error',
                    summary: 'Create Data Sertifikasi Failed!',
                    detail: 'Data gagal ditambahkan',
                    life: 5000,
                });
            }
        },
        preserveUrl: true,
        preserveScroll: true,
        forceFormData: true,
    })
}

function popupDelete(data){
    addConfirm(confirm, {
        icon: 'pi pi-trash',
        acceptProps: {
            label: "Delete",
        },
        accept: () => {
            router.delete(route('stafAdmin.master_sertifikasi.delete.data_sertifikasi', {sertifikasi: data.id}), {
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
        },
    })
}

watch(visibleDS, (newValue, oldValue) => {
    if(newValue === false){
        formDS.reset();
        formDS.clearErrors();
        imageBLOB.value = null;
        selectedSertifikasiID.value = null;
        reset();
    }
})

const submitSearch = () => {
    formatFilters();
    router.get(route('stafAdmin.master_sertifikasi'), {s: searchValue.value.trim(), filter: submittedFilters});
}

const searchValue = ref(props.search ?? '');
const sertifikasi = ref([...props.sertifikasi?.data as any]);
const DataMeta = ref({...props.sertifikasi?.meta as any});
const changePaginate = (amount: number) => {
    router.get(window.location.pathname + window.location.search, {paginate: amount});
}
const sortOption = ref([
    'created at',
    'nama sertifikasi',
    'badan penerbit',
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
    sertifikasi.value = [...props.sertifikasi?.data as any];
    DataMeta.value = {...props.sertifikasi?.meta as any};
}
</script>
<template>
    {{ console.log("PROPS: ", props) }}
    <Head :title="props.title" />
    <main>
        {{ console.log("SERTIFIKASI: ", props.sertifikasi) }}
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
                <DataTable class="rounded-lg shadow !overflow-auto text-sm" show-gridlines striped-rows :value="sertifikasi" pt:table="min-w-200">
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold border-b-2 dark:border-surface-400">Master Sertifikasi</span>
                            <Button label="Tambah Data Sertifikasi" @click="openTDS()" />
                        </div>
                    </template>
                    <Column style="min-width: 0rem; max-width: 7rem;" field="nama_sertifikasi" header="Nama Sertifikasi"></Column>
                    <Column style="min-width: 0rem; max-width: 7rem;" field="badan_penerbit" header="Badan Penerbit"></Column>
                    <Column style="min-width: 0rem; max-width: 7rem;" field="kode_sertifikasi" header="Kode Sertifikasi"></Column>
                    <Column style="min-width: 0rem; max-width: 7rem;" field="logo_path" header="Logo">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <img :src="data.logo_path" alt="image" class="max-w-10 rounded object-cover" />
                            </div>
                        </template>
                    </Column>
                    <Column style="min-width: 0rem; max-width: 7rem;" header="Action">
                        <template #body="{ data }">
                            <Button icon="pi pi-pencil" size="small" severity="info" outlined rounded @click="openEDS(data)" />
                            <Button icon="pi pi-trash" size="small" severity="danger" outlined rounded @click="popupDelete(data)" />
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>                        
                    </template>
                    <template v-if="sertifikasi?.length > 0" #footer>
                        
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

    <!-- DS Dialog -->
     <Dialog v-model:visible="visibleDS" modal :header="actionDS === 'create' ? 'Tambah Data Sertifikasi' : 'Edit Data Sertifikasi'" class="w-1/4" :draggable="false">
        <form class="flex flex-col" @submit.prevent="submitDS">
            <div class="flex flex-col items-start">
                <label for="nama_sertifikasi" class="font-semibold">Nama Sertifikasi</label>
                <InputText v-model="formDS.nama_sertifikasi" id="nama_sertifikasi" class="flex-auto w-full" autocomplete="off" />
                <InputError :message="formDS.errors.nama_sertifikasi" class="mt-2" />
            </div>
            <div class="flex flex-col items-start">
                <label for="badan_penerbit" class="font-semibold">Badan Penerbit</label>
                <InputText v-model="formDS.badan_penerbit" id="badan_penerbit" class="flex-auto w-full" autocomplete="off" />
                <InputError :message="formDS.errors.badan_penerbit" class="mt-2" />
            </div>
            <div class="flex flex-col items-start">
                <label for="kode_sertifikasi" class="font-semibold">Kode Sertifikasi</label>
                <InputText v-model="formDS.kode_sertifikasi" id="kode_sertifikasi" class="flex-auto w-full" autocomplete="off" maxlength="25" minlength="10" />
                <InputError :message="formDS.errors.kode_sertifikasi" class="mt-2" />
            </div>
            <div class="flex flex-col items-start">
                <p>Gambar</p>
                <div class="flex flex-col gap-2">
                    <div id="gambar" v-if="imageBLOB">
                        <img :src="imageBLOB.image" alt="image" type="image/*" class="w-1/3 rounded object-cover" />
                    </div>
                    <div class="flex flex-col justify-start items-start gap-2">
                        <label for="input_file" class="border-1 border-primary-300 dark:border-surface-500 p-2 rounded cursor-pointer hover:bg-primary-100 dark:hover:bg-surface-700">Upload gambar</label>
                        <p v-if="imageBLOB">{{ imageBLOB.name }}</p>
                    </div>
                    <input type="file" id="input_file" accept="image/*" @change="handleFileChange" hidden>
                </div>
                <InputError :message="formDS.errors.logo" class="mt-2" />
            </div>
            <div class="flex justify-end gap-2">
                <SecondaryButton type="button" label="Cancel" @click="visibleDS = false" />
                <Button type="submit" label="Save" />
            </div>
        </form>
     </Dialog>
</template>
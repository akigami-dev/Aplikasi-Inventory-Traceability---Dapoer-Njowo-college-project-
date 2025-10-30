<script setup lang="ts">
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head, router, useForm } from '@inertiajs/vue3';
import Column from 'primevue/column';
import Textarea from 'volt-components/Textarea.vue';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Dialog from 'volt-components/Dialog.vue';
import InputMask from 'volt-components/InputMask.vue';
import InputText from 'volt-components/InputText.vue';
import { ref, watch } from 'vue';
import addToast from '@/composables/addToast';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import SecondaryButton from 'volt-components/SecondaryButton.vue';
import InputError from '@/components/InputError.vue';
import { addConfirm, parseDate, parseDateForDatePicker } from '@/composables/Helper';
import FilterMenu from '@/components-project/filterMenu.vue';
import Select from 'primevue/select';
import Paginator from 'primevue/paginator';

const props = defineProps({
    ...useDefineProps("Master Supplier"),
    supplier: Object,
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
const selectedSupplierID = ref(null);
const actionDS = ref();

// FORM DS
const formDS = useForm({
    nama_supplier: '',
    email: '',
    no_telp: '',
    alamat: '',
    _method: 'post',
})

// FUNCTION SECTIONS
function openTDS (){
    visibleDS.value = true;
    actionDS.value = 'create';
    formDS._method = 'post';
}

function openEDS(data: object){
    selectedSupplierID.value = data.id;
    formDS.nama_supplier = data.nama_supplier;
    formDS.email = data.email;
    formDS.no_telp = data.no_telpon;
    formDS.alamat = data.alamat;

    visibleDS.value = true;
    actionDS.value = 'edit';
    formDS._method = 'put';
}

function popupDelete(data: object){
    addConfirm(confirm, {
        icon: 'pi pi-trash',
        acceptProps: {
            label: "Delete",
        },
        accept: () => {
            router.delete(route('stafAdmin.master_supplier.delete.supplier', {supplier: data.id}), {
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

function submitDS(){
    const submitRoute = actionDS.value === 'create' ? 'stafAdmin.master_supplier.create.supplier' : 'stafAdmin.master_supplier.update.supplier';
    formDS.post(route(submitRoute, {supplier: selectedSupplierID.value ?? undefined}), {
        onSuccess: (response) => {
            if(response.props && response.props?.toast?.severity){
                addToast(toast, response.props.toast);
            }
            visibleDS.value = false;
        },
        onError: (response) => {
            if(response.props && response.props?.toast?.severity){
                addToast(toast, response.props.toast);
            }else{
                addToast(toast, {
                    severity: 'error',
                    summary: 'Create Supplier Failed!',
                    detail: 'Supplier gagal ditambahkan',
                    life: 5000,
                });
            }
        },
        preserveUrl: true,
        preserveScroll: true,
    })
}

watch(visibleDS, (newValue, oldValue) => {
    if(newValue === false){
        formDS.reset();
        formDS.clearErrors();
        selectedSupplierID.value = null;
        reset();
    }
})

const submitSearch = () => {
    formatFilters();
    router.get(route('stafAdmin.master_supplier'), {s: searchValue.value.trim(), filter: submittedFilters});
}

const searchValue = ref(props.search ?? '');
const supplier = ref([...props.supplier?.data as any]);
const DataMeta = ref({...props.supplier?.meta as any});
const changePaginate = (amount: number) => {
    router.get(window.location.pathname + window.location.search, {paginate: amount});
}
const sortOption = ref([
    'created at',
    'kode supplier',
    'nama supplier',
    'email',
    'no telpon',
    'alamat',
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
    supplier.value = [...props.supplier?.data as any];
    DataMeta.value = {...props.supplier?.meta as any};
}
</script>
<template>
    {{ console.log("PROPS: ", props) }}
    <Head :title="props.title" />
    <main>
        {{ console.log("SUPPLIER: ", props.supplier) }}
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
                <DataTable class="rounded-lg shadow !overflow-auto text-sm" show-gridlines striped-rows :value="supplier" pt:table="min-w-200">
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold border-b-2 dark:border-surface-400">Master Supplier</span>
                            <Button label="Tambah Data Supplier" @click="openTDS()" />
                        </div>
                    </template>
                    <Column field="kode_supplier" header="Kode Supplier" class="w-1"></Column>
                    <Column field="nama_supplier" header="Nama Supplier" class="w-1"></Column>
                    <Column field="email" header="Email" class="w-1"></Column>
                    <Column field="no_telpon" header="No. Telpon" class="w-1"></Column>
                    <Column field="alamat" header="Alamat" class="w-1"></Column>
                    <Column header="Action" class="w-1">
                        <template #body="{ data }">
                            <div class="flex justify-start items-center gap-2">
                            <Button icon="pi pi-pencil" size="small" severity="info" outlined rounded @click="openEDS(data)" />
                            <Button icon="pi pi-trash" size="small" severity="danger" outlined rounded @click="popupDelete(data)" />
                            </div>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>                        
                    </template>
                    <template v-if="supplier?.length > 0" #footer>
                        
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

    <Dialog v-model:visible="visibleDS" modal :header="actionDS === 'create' ? 'Tambah Data Supplier' : 'Edit Data Supplier'" class="w-1/4" :draggable="false">
        <form class="flex flex-col" @submit.prevent="submitDS">
            <div class="flex flex-col items-start">
                <label for="nama_supplier" class="font-semibold">Nama Supplier</label>
                <InputText v-model="formDS.nama_supplier" id="nama_supplier" class="flex-auto w-full" autocomplete="off" />
                <InputError :message="formDS.errors.nama_supplier" class="mt-2" />
            </div>
            <div class="flex flex-col items-start">
                <label for="email" class="font-semibold">Email</label>
                <InputText v-model="formDS.email" id="email" type="email" class="flex-auto w-full" autocomplete="off" />
                <InputError :message="formDS.errors.email" class="mt-2" />
            </div>
            <div class="flex flex-col items-start">
                <label for="no_telp" class="font-semibold">No. Telpon</label>
                <InputMask v-model="formDS.no_telp"  mask="9999 9999 9999 99" placeholder="0812 3456 7890 99" slot-char="" :auto-clear="false" class="flex-auto w-full" id="no_telpon" required />
                <InputError :message="formDS.errors.no_telp" class="mt-2" />
            </div>
            <div class="flex flex-col items-start">
                <label for="alamat" class="font-semibold">Alamat</label>
                <Textarea v-model="formDS.alamat" id="alamat" rows="3" :auto-resize="true" class="flex-auto w-full" autocomplete="on" />
                <InputError :message="formDS.errors.alamat" class="mt-2" />
            </div>
            <div class="flex justify-end mt-4 gap-2">
                <SecondaryButton type="button" label="Cancel" @click="visibleDS = false" />
                <Button type="submit" label="Save" />
            </div>
        </form>
    </Dialog>
</template>
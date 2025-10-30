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
import Select from 'volt-components/Select.vue';
import { ref, watch } from 'vue';
import Paginator from 'primevue/paginator';
import FilterMenu from '@/components-project/filterMenu.vue';

const props = defineProps({
    ...useDefineProps("Template"),
    bahanBaku: Object,
    supplier: Object,
    search: String,
    paginate: Number,
})

defineOptions({
    layout: useAppLayout()
})

const toast = useToast();
const confirm = useConfirm();

// VARIABLE SECTIONS
const selectedBahanBakuID = ref(null);
const visibleDB = ref(false);
const actionDB = ref('create');
const formDB = useForm({
    _method: 'post',
    nama_bahan: '',
    supplier: '',
});

// FUNCTION SECTIONS
function openTDB() {
    visibleDB.value = true;
    actionDB.value = 'create';
    formDB._method = 'post';
}

function openEDB(data: object){
    selectedBahanBakuID.value = data.id;
    formDB.nama_bahan = data.nama_bahan;
    formDB.supplier = data.supplier.id;

    visibleDB.value = true;
    actionDB.value = 'edit';
    formDB._method = 'put';
}

function submitDB(){
    console.log("FORM: ", formDB);
    const submitRoute = actionDB.value === 'create' ? 'stafAdmin.master_bahan_baku.create.bahan_baku' : 'stafAdmin.master_bahan_baku.update.bahan_baku';
    formDB.post(route(submitRoute, {bahan_baku: selectedBahanBakuID.value ?? undefined}), {
        preserveUrl: true,
        preserveScroll: true,

        onSuccess: (response) => {
            console.log("SUCCESS: ", response);
            visibleDB.value = false;
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }
        },
        onError: (response) => {
            console.log("ERROR: ", response);
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }else{
                addToast(toast, {
                    severity: 'error',
                    summary: 'Create Data Bahan Baku Failed!',
                    detail: 'Data gagal ditambahkan',
                    life: 5000,
                });
            }
        }
    });
}

function popupDelete(data: object){
    addConfirm(confirm, {
        icon: 'pi pi-trash',
        accept: () => {
            router.delete(route('stafAdmin.master_bahan_baku.delete.bahan_baku', {bahan_baku: data.id}), {
                onSuccess: (response) => {
                    if(response.props?.toast){
                        addToast(toast, response.props.toast);
                    }
                    reset();
                },
                onError: (response) => {
                    if(response.props?.toast){
                        addToast(toast, response.props.toast);
                    }
                },
                preserveUrl: true,
                preserveScroll: true,
            });
        }
    });
}

watch(visibleDB, (newValue, oldValue) => {
    if(newValue === false){
        formDB.reset();
        formDB.clearErrors();
        reset();
    }
})

const submitSearch = () => {
    formatFilters();
    router.get(route('stafAdmin.master_bahan_baku'), {s: searchValue.value.trim(), filter: submittedFilters});
}

const searchValue = ref(props.search ?? '');
const bahanBaku = ref([...props.bahanBaku?.data as any]);
const DataMeta = ref({...props.bahanBaku?.meta as any});
const changePaginate = (amount: number) => {
    router.get(window.location.pathname + window.location.search, {paginate: amount});
}
const sortOption = ref([
    'created at',
    'kode bahan',
    'nama bahan',
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
    bahanBaku.value = [...props.bahanBaku?.data as any];
    DataMeta.value = {...props.bahanBaku?.meta as any};
}
</script>
<template>
    {{ console.log("PROPS TOD: ", props) }}
    <Head :title="props.title" />
    <main>
        <div class="main-container flex flex-col border dark:border-0">
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
                <DataTable class="rounded-lg shadow !overflow-auto text-sm" show-gridlines striped-rows :value="bahanBaku" pt:table="min-w-200">
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold border-b-2 dark:border-surface-400">Master Bahan Baku</span>
                            <Button label="Tambah Data Bahan Baku" @click="openTDB()" />
                        </div>
                    </template>
                    <Column field="kode_bahan" header="Kode Bahan" class="w-1"></Column>
                    <Column field="nama_bahan" header="Nama Bahan" class="w-1"></Column>
                    <Column field="supplier.nama_supplier" header="Nama Supplier" class="w-1"></Column>
                    <Column header="action" class="w-1">
                        <template #body="{ data }">
                            <div class="flex justify-start items-center gap-2">
                                <Button icon="pi pi-pencil" size="small" severity="info" outlined rounded @click="openEDB(data)" />
                            <Button icon="pi pi-trash" size="small" severity="danger" outlined rounded @click="popupDelete(data)" />
                            </div>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>                        
                    </template>
                    <template v-if="bahanBaku.length > 0" #footer>
                        
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

    <Dialog v-model:visible="visibleDB" modal :header=" actionDB === 'create' ? 'Tambah Data Bahan Baku' : 'Edit Data Bahan Baku'" class="w-1/4" :draggable="false">
        <form class="flex flex-col" @submit.prevent="submitDB">
            <div class="flex flex-col items-start">
                <label for="nama_supplier" class="font-semibold">Nama Bahan</label>
                <InputText v-model="formDB.nama_bahan" id="nama_supplier" class="flex-auto w-full" autocomplete="off" />
                <InputError :message="formDB.errors.nama_bahan" class="mt-2" />
            </div>
            <div class="flex flex-col items-start">
                <label for="nama_supplier" class="font-semibold">Nama Supplier</label>
                <Select v-model="formDB.supplier" :options="supplier" option-label="nama_supplier" option-value="id" id="kategori" placeholder="Select Supplier" class="flex-auto w-auto" autocomplete="off">
                    <template #option="slotProps">
                        {{ slotProps.option.nama_supplier }} -- [{{ slotProps.option.kode_supplier }}]
                    </template>
                </Select>
                <InputError :message="formDB.errors.supplier" class="mt-2" />
            </div>
            <div class="flex justify-end mt-4 gap-2">
                <SecondaryButton type="button" label="Cancel" @click="visibleDB = false" />
                <Button type="submit" label="Save" />
            </div>
        </form>
    </Dialog>
</template>
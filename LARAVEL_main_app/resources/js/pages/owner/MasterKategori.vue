<script setup lang="ts">
import FilterMenu from '@/components-project/filterMenu.vue';
import InputError from '@/components/InputError.vue';
import addToast from '@/composables/addToast';
import { addConfirm, parseDate, parseDateForDatePicker } from '@/composables/Helper';
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head, router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import DatePicker from 'primevue/datepicker';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Paginator from 'primevue/paginator';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import SecondaryButton from 'volt-components/SecondaryButton.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    ...useDefineProps("Template"),
    data: Object,
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
const data = ref([...props.data?.data as any]);
const dataMeta = ref({...props.data?.meta as any});

const visibleDK = ref(false);
const actionTDK = ref('create');
const formDK = useForm({
    _method: 'post',
    id: null,
    nama_kategori: '',
    deskripsi: '',
})

const searchValue = ref(props.search ?? '');

const sortOption = ref([
    'created at',
    'nama kategori',
    'deskripsi',
])

const rangeOption = ref([
    'created at',
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

// FUNCTION SECTIONS
const openTDK = () => {
    visibleDK.value = true
    actionTDK.value = 'create'
    formDK._method = 'post'
}

const openEDK = (data: object) => {
    visibleDK.value = true
    actionTDK.value = 'edit'
    formDK._method = 'put';
    formDK.id = data.id
    formDK.nama_kategori = data.name
    formDK.deskripsi = data.deskripsi
}

const popupDelete = (data: object) => {
    addConfirm(confirm, {
        icon: 'pi pi-trash',
        message: 'Apakah anda yakin ingin menghapus data ini?',
        accept: () => {
            router.delete(route('owner.master_kategori.delete.kategori', data.id), {
                onSuccess: () => {
                    addToast(toast);
                    reset();
                },
                onError: () => {
                    addToast(toast)
                },
            })
        },
    })
}

const headerTDK = () => {
    switch (actionTDK.value) {
        case 'create':
            return 'Tambah Kategori'
        case 'edit':
            return 'Edit Kategori'
        default:
            return 'Tambah Kategori'
    }
}

const submitDK = () => {
    const submitRoute = actionTDK.value === 'create' ? 'owner.master_kategori.create.kategori' : 'owner.master_kategori.update.kategori';
    formDK.post(route(submitRoute, {id: formDK.id ?? undefined}), {
        onSuccess: (response) => {
            addToast(toast);
            visibleDK.value = false
            reset();
        },
        onError: () => {
            addToast(toast)
        },
    })
}

const changePaginate = (amount: number) => {
    router.get(window.location.pathname + window.location.search, {paginate: amount});
}

const reset = () => {
    data.value = [...props.data?.data as any];
    dataMeta.value = {...props.data?.meta as any};
}

const submitSearch = () => {
    formatFilters();
    router.get(route('owner.master_kategori'), {s: searchValue.value.trim(), filter: submittedFilters});
}

const formatFilters = () => {
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


// WATCH
watch(visibleDK, (newValue, oldValue) => {
    if(newValue === false){
        formDK.reset();
        formDK.clearErrors();
        reset();
    }
})
</script>
<template>
    <Head :title="props.title" />
    <main>
        <div class="main-container flex flex-col border dark:border-0 gap-2">
            <div class="flex-1">
                <form @submit.prevent="submitSearch" class="flex gap-2">
                    <InputText v-model="searchValue" placeholder="Cari..." />
                    <Button label="Search" type="submit" />
                </form>
            </div>
            <FilterMenu class="py-1">
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
                <DataTable class="rounded-lg shadow !overflow-auto text-sm" show-gridlines striped-rows :value="data" pt:table="min-w-200">
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold border-b-2 dark:border-surface-400">Master Kategori</span>
                            <Button label="Tambah Data Sertifikasi" @click="openTDK()" />
                        </div>
                    </template>
                    <Column style="min-width: 0rem; max-width: 7rem;" field="name" header="Nama Kategori"></Column>
                    <Column style="min-width: 0rem; max-width: 7rem;" field="deskripsi" header="Deskripsi"></Column>
                    <Column style="min-width: 0rem; max-width: 3rem;" header="Action">
                        <template #body="{ data }">
                            <span class="flex gap-2">
                                <Button icon="pi pi-pencil" size="small" severity="info" outlined rounded @click="openEDK(data)" />
                                <Button icon="pi pi-trash" size="small" severity="danger" outlined rounded @click="popupDelete(data)" />
                            </span>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>                        
                    </template>
                    <template v-if="data?.length > 0" #footer>
                        <Paginator :pt="{paginatorContainer: 'w-full'}" :first="(dataMeta.current_page - 1) * dataMeta.per_page" :rows="dataMeta.per_page" :totalRecords="dataMeta.total" @page="(e) => toPage(e.page + 1)">
                            <template #start="slotProps">
                                <div class="w-full flex items-center gap-4">
                                    <span>Showing {{ slotProps.state.page + 1 }} to {{ dataMeta.last_page }} of {{ dataMeta.total }} entries</span>
                                    <Select class="w-fit" :options="[10, 25, 50]" :default-value="paginate" placeholder="Select..." @value-change="(value) => changePaginate(value)" />
                                </div>
                            </template>
                        </Paginator>
                    </template>
                </DataTable>
            </div>
        </div>
    </main>

    <Dialog v-model:visible="visibleDK" modal :header="headerTDK()" class="w-md" :maximizable="true" :draggable="false" :dismissable-mask="true">
        <form class="flex flex-col" @submit.prevent="submitDK">
            <div class="flex flex-col items-start">
                <label for="nama_kategori" class="font-semibold">Nama Kategori</label>
                <InputText v-model="formDK.nama_kategori" id="nama_kategori" class="flex-auto w-full" autocomplete="off" required minlength="1" maxlength="25" />
                <InputError :message="formDK.errors.nama_kategori" class="mt-2" />
            </div>
            <div class="flex flex-col items-start">
                <label for="deskripsi" class="font-semibold">Deskripsi</label>
                <Textarea rows="2" v-model="formDK.deskripsi" id="deskripsi" class="flex-auto w-full" autocomplete="off" required minlength="1" />
                <InputError :message="formDK.errors.deskripsi" class="mt-2" />
            </div>
            <div class="flex justify-end mt-4 gap-2">
                <SecondaryButton type="button" label="Cancel" @click="visibleDK = false" />
                <Button type="submit" label="Save" />
            </div>
        </form>
    </Dialog>
</template>
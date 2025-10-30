<script setup lang="ts">
import FilterMenu from '@/components-project/filterMenu.vue';
import InputError from '@/components/InputError.vue';
import addToast from '@/composables/addToast';
import { addConfirm, formatDatefromString, formatDateTimeForLaravel, formatDecimal, parseDate, parseDateForDatePicker, parseNumber } from '@/composables/Helper';
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head, router, useForm } from '@inertiajs/vue3';
import Column from 'primevue/column';
import Paginator from 'primevue/paginator';
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

const props = defineProps({
    ...useDefineProps("Lokasi Penyimpanan"),
    search: String,
    DataLokasiPenyimpanan: Object,
    paginate: Number,
    filter: Object,
})

defineOptions({
    layout: useAppLayout()
})

const toast = useToast();
const confirm = useConfirm();

// VARIABLE SECTIONS
const searchValue = ref(props.search ?? '');
const DataLokasiPenyimpanan = ref([...props.DataLokasiPenyimpanan?.data as any]);
const DataMeta = ref({...props?.DataLokasiPenyimpanan?.meta});
const actionLP = ref('create');
const visibleTLP = ref(false);
const formDLP = useForm({
    _method: 'post',
    id: null,
    nama_lokasi_penyimpanan: '',
    deskripsi: '',
    suhu_default: 0,
})

// FUNCTION SECTIONS
const submitSearch = () => {
    formatFilters();
    router.get(route('owner.lokasi_penyimpanan'), {s: searchValue.value.trim(), filter: submittedFilters});
}

const openTLP = () => {
    visibleTLP.value = true;
}

const openELP = (data) => {
    actionLP.value = 'edit';
    formDLP._method = 'put';
    formDLP.id = data.id;
    formDLP.nama_lokasi_penyimpanan = data.name;
    formDLP.deskripsi = data.deskripsi;
    formDLP.suhu_default = data.suhu_default;
    visibleTLP.value = true;
}

const toPage = (page) => {
    router.get(route('owner.lokasi_penyimpanan'), {s: searchValue.value, page: page});
}

const submitTLP = () => {
    const submitRoute = actionLP.value === 'create' ? 'owner.lokasi_penyimpanan.create.penyimpanan' : 'owner.lokasi_penyimpanan.update.penyimpanan';
    formDLP.post(route(submitRoute, {penyimpanan: formDLP.id, s: searchValue.value}), {
        onSuccess: (response) => {
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }else{
                addToast(toast, {
                    severity: 'error',
                    summary: 'Failed!',
                    detail: 'Lokasi Penyimpanan gagal ditambahkan / diubah ',
                    life: 5000,
                });
            }

            visibleTLP.value = false;
        },
        onError: (response) => {
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }else{
                addToast(toast, {
                    severity: 'error',
                    summary: 'Failed!',
                    detail: 'Lokasi Penyimpanan gagal ditambahkan / diubah ',
                    life: 5000,
                });
            }
        },
        preserveScroll: true,
        preserveState: true
    })
}

const popupDelete = (data: object) => {
    addConfirm(confirm, {
        icon: 'pi pi-trash',
        accept: () => {
            router.delete(route('owner.lokasi_penyimpanan.delete.penyimpanan', {penyimpanan: data.id}), {
                onSuccess: (response) => {
                    if(response.props?.toast){
                        addToast(toast, response.props?.toast);
                    }else{
                        addToast(toast, {
                            severity: 'error',
                            summary: 'Delete Data Lokasi Penyimpanan Failed!',
                            detail: 'Data gagal dihapus',
                            life: 5000,
                        });
                    }
                    reset();
                },
                onError: (response) => {
                    if(response.props?.toast){
                        addToast(toast, response.props?.toast);
                    }else{
                        addToast(toast, {
                            severity: 'error',
                            summary: 'Delete Data Lokasi Penyimpanan Failed!',
                            detail: 'Data gagal dihapus',
                            life: 5000,
                        });
                    }
                },
                preserveUrl: true,
                preserveScroll: true,
            });
        }
    })};

const reset = () => {
    actionLP.value = 'create';
    formDLP.reset();
    formDLP.clearErrors();
    DataLokasiPenyimpanan.value = [...props.DataLokasiPenyimpanan?.data || []];
    DataMeta.value = {...props.DataLokasiPenyimpanan?.meta};
}

watch(props.DataLokasiPenyimpanan as any, (newValue) => {
    DataLokasiPenyimpanan.value = [...newValue?.data || []];
    DataMeta.value = [...newValue?.meta || []];
})

watch(visibleTLP, (newValue) => {
    if(newValue === false){
        reset();
    }
})


const sortOption = ref([
    'created at',
    'lokasi penyimpanan',
    'deskripsi',
    'suhu default',
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
                <DataTable class="rounded-lg shadow !overflow-auto text-sm" show-gridlines striped-rows :value="DataLokasiPenyimpanan" pt:table="min-w-200">
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold border-b-1 border-surface-300 dark:border-surface-600">Data Lokasi Penyimpanan</span>
                            <Button label="Tambah Data Lokasi Penyimpanan" @click="openTLP()" />
                        </div>
                    </template>
                    <Column field="name" header="Lokasi Penyimpanan"></Column>
                    <Column field="suhu_default" header="Suhu Default">
                        <template #body="{ data }">
                            <div class="flex gap-1">
                                <span 
                                class="font-bold text-center"
                                :class="{'text-red-300': data.suhu_default > 30, 'text-green-300': data.suhu_default >= 0, 'text-blue-300': data.suhu_default < 0}"
                                >{{ parseNumber(data.suhu_default) }}</span>
                                <span 
                                :class="{'text-red-500': data.suhu_default > 30, 'text-green-500': data.suhu_default >= 0, 'text-blue-500': data.suhu_default < 0}">°C</span>
                            </div>
                        </template>
                    </Column>
                    <Column field="deskripsi" header="Deskripsi"></Column>
                    <Column header="Action" >
                        <template #body="{ data }">
                            <div class="flex justify-start items-center gap-2">
                                <Button icon="pi pi-pencil" size="small" severity="info" outlined rounded @click="openELP(data)" />
                            <Button icon="pi pi-trash" size="small" severity="danger" outlined rounded @click="popupDelete(data)" />
                            </div>
                        </template>
                    </Column>
                    <template #footer>
                        <div v-if="DataLokasiPenyimpanan.length <= 0" class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>
                        <Paginator v-if="DataLokasiPenyimpanan.length > 0" :pt="{paginatorContainer: 'w-full'}" :first="(DataMeta.current_page - 1) * DataMeta.per_page" :rows="DataMeta.per_page" :totalRecords="DataMeta.total" @page="(e) => toPage(e.page + 1)">
                            <template #start="slotProps">
                                <div class="w-full">
                                    Showing {{ slotProps.state.page + 1 }} to {{ DataMeta.last_page }} of {{ DataMeta.total }} entries
                                </div>
                            </template>
                        </Paginator>
                    </template>
                </DataTable>
            </div>
        </div>
    </main>
    
    <Dialog v-model:visible="visibleTLP" modal :header="actionLP == 'edit' ? 'Edit Data Lokasi Penyimpanan' : 'Tambah Data Lokasi Penyimpanan'" class="w-1/4" :draggable="false">
        <form class="flex flex-col" @submit.prevent="submitTLP">
            <div class="flex flex-col items-start">
                <label for="nama_lokasi_penyimpanan" class="font-semibold">Nama Lokasi Penyimpanan</label>
                <InputText v-model="formDLP.nama_lokasi_penyimpanan" id="nama_lokasi_penyimpanan" class="flex-auto w-full" autocomplete="off" required />
                <InputError :message="formDLP.errors.nama_lokasi_penyimpanan" class="mt-2" />
            </div>
            <div class="flex flex-col items-start">
                <label for="suhu_default" class="font-semibold">Suhu Default</label>
                <InputNumber v-model="formDLP.suhu_default" id="suhu_default" class="flex-auto w-full" :max="1000" :max-fraction-digits="2" suffix=" °C" required />
                <InputError :message="formDLP.errors.suhu_default" class="mt-2" />
            </div>
            <div class="flex flex-col items-start">
                <label for="deskripsi" class="font-semibold">Deskripsi</label>
                <Textarea v-model="formDLP.deskripsi" id="deskripsi" rows="3" :auto-resize="true" class="flex-auto w-full" required />
                <InputError :message="formDLP.errors.deskripsi" class="mt-2" />
            </div>
            <div class="flex justify-end mt-4 gap-2">
                <SecondaryButton type="button" label="Cancel" @click="visibleTLP = false" />
                <Button type="submit" label="Save" />
            </div>
        </form>
    </Dialog>
</template>
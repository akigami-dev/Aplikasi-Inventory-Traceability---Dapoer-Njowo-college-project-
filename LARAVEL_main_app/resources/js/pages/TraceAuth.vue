<script setup lang="ts">
import addToast from '@/composables/addToast';
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Button from 'volt-components/Button.vue';
import InputText from 'volt-components/InputText.vue';
import DataView from 'volt-components/DataView.vue'
import { formatDatefromString, formatDateTimefromString, formatDecimalforInputNumber, parseDate, parseDateForDatePicker } from '@/composables/Helper';
import { ref, toRaw, watch } from 'vue';
import Dialog from 'volt-components/Dialog.vue';
import SelectButton from 'primevue/selectbutton';
import Badge from 'volt-components/Badge.vue';
import FilterMenu from '@/components-project/filterMenu.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Paginator from 'primevue/paginator';
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';

const props = defineProps({
    ...useDefineProps("Template"),
    trace: Object,
    search: String,
    filter: Object,
    paginate: Number,
    styleView: String,
})

defineOptions({
    layout: useAppLayout()
})

const toast = useToast();
const confirm = useConfirm();

// VARIABLE SECTIONS
const searchValue = ref(props.search ?? '');

const selectedTrace = ref();
const openDialog = ref(false);
const filter = ref(props.filter ?? {});

// FUNCTION SECTIONS
async function submitSearch(){
    formatFilters();
    router.get(route('auth.trace'), {s: searchValue.value.trim(), filter: submittedFilters, styleView: styleView.value});

}

function open(item: object){
    selectedTrace.value = item;
    openDialog.value = true;
    console.log("ITEM: ", selectedTrace.value);
}

function submitPDF(){
    console.log("selected trace: ", selectedTrace.value);
    router.post(route('auth.trace.pdf_product'), {trace: selectedTrace.value});
}

watch(openDialog, (newValue, oldValue) => {
    if(newValue === false){
        selectedTrace.value = null;
        reset();
    }
})


const styleView = ref(props.styleView ?? 'List');
const selectButtonOptions = ref([
    { icon: 'pi pi-list', value: 'List' },
    { icon: 'pi pi-th-large', value: 'Tiles' },
]);

const trace = ref([...props.trace?.data as any]);
const DataMeta = ref({...props.trace?.meta as any});

const changePaginate = (amount: number) => {
    router.get(window.location.pathname + window.location.search, {paginate: amount});
}

const sortOption = ref([
    'created at',
    'nomor batch',
    'kode distribusi',
    'nama produk',
    'tanggal kadaluarsa',
])

const rangeOption = ref([
    'tanggal kadaluarsa',
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
    trace.value = [...props.trace?.data as any];
    DataMeta.value = {...props.trace?.meta as any};
}

</script>
<template>
    {{ console.log('PROPS: ', props)}}
    <Head :title="props.title" />
    <main>
        <div class="main-container flex flex-col border dark:border-0">
            <div class="flex-1 flex flex-col gap-4">
                <form @submit.prevent="submitSearch" class="flex gap-2">
                    <InputText v-model="searchValue" placeholder="Cari..." />
                    <Button label="Search" type="submit" />
                </form>
                
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
            </div>
            <SelectButton v-model="styleView" :allow-empty="false" :options="selectButtonOptions" option-value="value" class="mb-1">
                <template #option="slotProps">
                    <i :class="slotProps.option.icon"></i>
                </template>
            </SelectButton>
            <div v-if="styleView == 'List'" class="flex-1">
                <DataTable class="rounded-lg shadow !overflow-auto text-sm" show-gridlines striped-rows :value="trace" pt:table="min-w-200">
                    <Column class="w-1" field="produk.gambar" header="Gambar">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <img :src="data.produk.gambar" alt="image" class="max-w-20 rounded object-cover" />
                            </div>
                        </template>
                    </Column>
                    <Column class="max-w-[3rem]" field="produksi.nomor_batch" header="Nomor Batch"></Column>
                    <Column class="max-w-[3rem]" field="distribusi.kode_distribusi" header="Kode Distribusi"></Column>
                    <Column class="max-w-[3rem]" field="produk.nama_produk" header="Nama Produk"></Column>
                    
                    <Column class="max-w-[3rem]" field="produk.kategori.name" header="Kategori"></Column>
                    <Column class="max-w-[3rem]" header="Kadaluarsa">
                        <template #body="{ data }">
                            <span class="text-sm">{{ formatDatefromString(data.produksi.tanggal_kadaluarsa) }}</span>
                        </template>
                    </Column>
                    <Column class="max-w-[3rem]" header="">
                        <template #body="{ data }">
                            <Button label="Detail" size="small" @click="open(data)"></Button>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>                        
                    </template>
                </DataTable>
            </div>
            <div v-if="styleView == 'Tiles'" class="flex-1">
                <DataView v-if="trace.length > 0" :value="trace">
                    <template #list="slotProps">
                        <div class="flex flex-wrap justify-center gap-4">
                            <div v-for="(item, index) in slotProps.items" :key="index" class="flex-1/2 sm:flex-1/4 2xl:flex-1/4 md:flex-1/3 main-container border shadow p-4 mt-2">
                                <div class="flex flex-col md:flex-row gap-4">
                                    <div class="flex-1/2 md:flex-none md:w-30 relative">
                                        <img class="block xl:block mx-auto rounded-md w-full" :src="item.produk.gambar" :alt="item.name" />
                                    </div>
                                    <div class="flex flex-row justify-start flex-1 gap-6">
                                        <div class="flex flex-col items-start gap-2">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-medium text-surface-500 dark:text-surface-400 capitalize">{{ item.produk.kategori.name }}</span>
                                                <span class="text-xl font-bold">{{ item.produk.nama_produk }}</span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-medium text-surface-500 dark:text-surface-400 flex gap-1">
                                                    <span class="min-w-25">Nomor Batch</span> 
                                                    <span>:</span>
                                                    <span class="text-primary-400 dark:text-primary-300">{{ item.produksi.nomor_batch }}</span>
                                                </span>
                                                <span class="text-sm font-medium text-surface-500 dark:text-surface-400 flex gap-1">
                                                    <span class="min-w-25">Kode Distribusi</span> 
                                                    <span>:</span>
                                                    <span class="text-primary-400 dark:text-primary-300">{{ item.distribusi.kode_distribusi }}</span>
                                                </span>
                                                <span class="text-sm font-medium text-surface-500 dark:text-surface-400 flex gap-1">
                                                    <span class="min-w-25">Kadaluarsa</span> 
                                                    <span>:</span>
                                                    <span class="text-primary-400 dark:text-primary-300">{{ formatDatefromString(item.produksi.tanggal_kadaluarsa) }}</span>
                                                </span>
                                            </div>
                                            <Button label="Detail" size="small" @click="open(item)"></Button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>                        
                    </template>
                </DataView>
                <div v-if="trace?.length <= 0" class="text-center py-5 text-surface-500">
                    Data Tidak Ditemukan
                </div> 
            </div>
        </div>
        <Paginator :pt="{paginatorContainer: 'w-full'}" :first="(DataMeta.current_page - 1) * DataMeta.per_page" :rows="DataMeta.per_page" :totalRecords="DataMeta.total" @page="(e) => toPage(e.page + 1)">
            <template #start="slotProps">
                <div class="w-full flex items-center gap-4">
                    <span>Showing {{ slotProps.state.page + 1 }} to {{ DataMeta.last_page }} of {{ DataMeta.total }} entries</span>
                    <Select class="w-fit" :options="[10, 25, 50]" :default-value="paginate" placeholder="Select..." @value-change="(value) => changePaginate(value)" />
                </div>
            </template>
        </Paginator>
    </main>

    <Dialog v-model:visible="openDialog" modal header="Detail" :draggable="false" :closable="true" :maximizable="true" :dismissable-mask="true" class="w-5/6 p-2">
        
        <div class="flex flex-col gap-3">
            <!-- <Link href=""  ></Link> -->
            <form action="POST" @submit.prevent="submitPDF" target="_blank">
                
                <button type="submit">
                    <Badge class="w-fit flex gap-1 dark:bg-surface-400 bg-surface-500 cursor-pointer">
                        <i class="pi pi-file"></i>
                        <span>PDF</span>
                    </Badge>
                </button>
            </form>
            <!-- <span class="w-fit p-1 border rounded"></span> -->
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 flex flex-col gap-4 relative">
                    <div class="flex flex-col gap-3 main-container border shadow p-4">
                        <span class="font-bold text-xl text-surface dark:text-surface-400 border-b-1 border-surface pb-2">Produk</span>
                        <div class="flex gap-3 overflow-hidden">
                            <img :src="selectedTrace.produk.gambar" alt="produk" class="max-w-40 max-h-50 rounded object-cover">
                            <div class="flex flex-col gap-3">
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Kode Produk: </span> 
                                    <span class="">{{ selectedTrace.produk.kode_produk }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Nama Produk: </span> 
                                    <span class="">{{ selectedTrace.produk.nama_produk }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Kategori: </span> 
                                    <span class="">{{ selectedTrace.produk.kategori.name }}</span>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <span class="font-bold text-primary">Sertifikasi: </span>
                                    <div class="flex flex-col gap-5 py-1" v-for="(sertifikasi, index) in selectedTrace.produk.sertifikasi" :key="index">
                                        <div class="flex">
                                            <div class="w-2 h-2 bg-surface-400 rounded-full mr-3 flex-shrink-0 self-center"></div>
                                            <span class="font-medium min-w-10">{{ sertifikasi.nama_sertifikasi }}</span>
                                            <span class="px-2 font-medium">:</span>
                                            <span class="font-light">{{ sertifikasi.kode_sertifikasi }}</span>
                                        </div>
                                    </div>
                                    <span v-if="selectedTrace.produk.sertifikasi.length <= 0" class="text-sm text-surface-400">(Tidak memiliki sertifikasi)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 main-container border shadow p-4">
                        <span class="font-bold text-xl text-surface dark:text-surface-400 border-b-1 border-surface pb-2">Produksi</span>
                        <div class="flex gap-3 overflow-hidden">
                            <img v-tooltip.top="'Label Produk'" :src="selectedTrace.produksi.label_path" alt="label" class="max-w-40 max-h-50 rounded object-cover cursor-pointer">
                            <div class="flex flex-col gap-3">
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Nomor Batch: </span> 
                                    <span class="">{{ selectedTrace.produksi.nomor_batch }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Mulai Produksi: </span> 
                                    <span class="">{{ parseDate(selectedTrace.produksi.mulai_produksi) }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Selesai Produksi: </span> 
                                    <span class="">{{ parseDate(selectedTrace.produksi.selesai_produksi) }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Tanggal Kadaluarsa: </span> 
                                    <span class="">{{ parseDate(selectedTrace.produksi.tanggal_kadaluarsa, 'YYYY-MM-DD') }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Lokasi Penyimpanan: </span> 
                                    <span class="">{{ selectedTrace.produksi.lokasi_penyimpanan }} ({{ selectedTrace.produksi.suhu_penyimpanan }} °C)</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Jumlah Produksi: </span> 
                                    <span class="">{{ selectedTrace.produksi.kuantitas }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Diproduksi Oleh: </span> 
                                    <span class="">{{ selectedTrace.produksi.user_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 main-container border shadow p-4">
                        <span class="font-bold text-xl text-surface dark:text-surface-400 border-b-1 border-surface pb-2">Recall Produk</span>
                        <div class="flex flex-col gap-3 main-container border shadow" v-for="(recall, index) in selectedTrace.distribusi.recall_produk" :key="index">
                            <div class="flex gap-2">
                                <span class="font-bold text-surface dark:text-surface-200">#{{ index + 1 }}</span>
                            </div>
                            <div class="flex gap-10">
                                <div class="flex flex-col">
                                    <div class="flex gap-2">
                                        <span class="font-bold text-primary">Kode Recall: </span> 
                                        <span class="">{{ recall.kode_recall }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <span class="font-bold text-primary">Tanggal Recall: </span> 
                                        <span class="">{{ parseDate(recall.tanggal_recall, 'DD MMM YYYY HH:ss') }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <span class="font-bold text-primary">Jumlah Recall: </span> 
                                        <span class="">{{ recall.jumlah_recall }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex gap-2">
                                        <span class="font-bold text-primary">Alasan Recall: </span> 
                                        <span class="">{{ recall.alasan_recall }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <span class="font-bold text-primary">Keterangan: </span> 
                                        <span class="">{{ recall.keterangan }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <span class="font-bold text-primary">Di recall oleh: </span> 
                                        <span class="">{{ recall.user.name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span v-if="selectedTrace.distribusi.recall_produk.length <= 0" class="text-surface-400 text-sm">Data recall tidak ditemukan atau kosong</span>
                    </div>
                </div>
                <div class="flex-1 flex flex-col gap-4">
                    <div class="flex flex-col gap-3 main-container border shadow p-4">
                        <span class="font-bold text-xl text-surface dark:text-surface-400 border-b-1 border-surface pb-2">Distribusi</span>
                        <div class="flex gap-3 overflow-hidden">
                            <div class="flex flex-col gap-3">
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Kode Distribusi: </span> 
                                    <span class="">{{ selectedTrace.distribusi.kode_distribusi }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Nama Retailer: </span> 
                                    <span class="">{{ selectedTrace.distribusi.nama_retailer }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Tanggal Pengiriman: </span> 
                                    <span class="">{{ parseDate(selectedTrace.distribusi.tanggal_pengiriman, 'YYYY-MM-DD') }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Alamat: </span> 
                                    <span class="">{{ selectedTrace.distribusi.alamat }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Jumlah Distribusi Tersisa: </span> 
                                    <span class="">{{ selectedTrace.distribusi.jumlah_tersisa }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="font-bold text-primary">Jumlah Ter-recall: </span> 
                                    <span class="">{{ selectedTrace.distribusi.recall_produk.reduce((total, item) => total + item.jumlah_recall, 0) }}</span>
                                </div>
                                <!-- <div class="flex gap-2">
                                    <span class="font-bold text-primary">Status: </span> 
                                    <span class="font-semibold uppercase" :class="selectedTrace.distribusi.status.toLowerCase() == 'retur' ? 'text-red-500' : 'text-green-500'">{{ selectedTrace.distribusi.status }}</span>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 main-container border shadow p-4">
                        <span class="font-bold text-xl text-surface dark:text-surface-400 border-b-1 border-surface pb-2">Bahan Baku</span>
                        <div class="flex flex-col gap-3 main-container border shadow" v-for="(bahan, index) in selectedTrace.bahan_baku" :key="index">
                            <div class="flex gap-2">
                                <span class="font-bold text-surface dark:text-surface-200">{{ index + 1 }}. {{ bahan.bahan_baku.nama_bahan }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="font-bold text-primary">Kode LOT: </span> 
                                <span class="">{{ bahan.lot_bahan_baku.kode_lot }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="font-bold text-primary">Nama Supplier: </span> 
                                <span class="">{{ bahan.bahan_baku.supplier.nama_supplier }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="font-bold text-primary">Jumlah Pakai: </span> 
                                <span class="">{{ formatDecimalforInputNumber(bahan.jumlah_penggunaan) }} {{ bahan.satuan }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="font-bold text-primary">Tanggal Kadaluarsa: </span> 
                                <span class="">{{ parseDate(bahan.lot_bahan_baku.tanggal_kadaluarsa, 'YYYY-MM-DD') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Dialog>
</template>
<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import addToast from '@/composables/addToast';
import { addConfirm, downloadImage, formatDatefromString, formatDateTimeForLaravel, formatDateTimefromString, formatDecimal, formatDecimalforInputNumber, openImage, parseDate, parseDateForDatePicker} from '@/composables/Helper';
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head, router, useForm } from '@inertiajs/vue3';
import Column from 'primevue/column';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import DatePicker from 'primevue/datepicker';
import Dialog from 'volt-components/Dialog.vue';
import Divider from 'volt-components/Divider.vue';
import InputNumber from 'volt-components/InputNumber.vue';
import Menu from 'volt-components/Menu.vue';
import SecondaryButton from 'volt-components/SecondaryButton.vue';
import Select from 'volt-components/Select.vue';
import { ref, watch } from 'vue';
import LotBahanBaku from '../stafAdmin/LotBahanBaku.vue';
import InputText from 'volt-components/InputText.vue';
import Paginator from 'primevue/paginator';
import FilterMenu from '@/components-project/filterMenu.vue';

const props = defineProps({
    ...useDefineProps("Template"),
    dataProduksi: Object,
    dataProduk: Object,
    dataLotBahanBaku: Object,
    dataLokasiPenyimpanan: Object,
    search: String,
    paginate: Number,
    filter: Object
})

defineOptions({
    layout: useAppLayout()
})

const toast = useToast();
const confirm = useConfirm();

// PENDING = MASTER PRODUK ID, LOT PENGGUNAAN LOGS [KODE LOT, JUMLAH PAKAI], KUANTITAS
// PROSES =
// SELESAI = NOMOR BATCH, TANGGAL KADALUARSA, SELESAI PRODUKSI, QRCODE, LABEL

// VARIABLE SECTIONS
const selectedBahanBaku = ref();
const selectedSatuan = ref([]);
const selectedJumlah = ref([]);
const selectedStatus = ref([]);
const selectedProduksiId = ref();
const selectedProduksi = ref();
const suhuDefault = ref();
const visibleLBB = ref(false);
const visibleSelesai = ref(false);
const visibleInfo = ref(false);
const searchValue = ref(props.search ?? '');
// const dataProduksi = ref(props?.dataProduksi?.data ?? []);
const DataMeta = ref(props?.dataProduksi?.meta ?? {});

const visibleDP = ref(false);
const visibleKadaluarsa = ref(false);
const actionDP = ref();
const produkBLOB = ref();
const dataLotBahanBaku = ref([...props.dataLotBahanBaku as any]);
const dataLokasiPenyimpanan = ref([...props.dataLokasiPenyimpanan as any]);

const formDP = useForm({
    produk_id: null,
    bahan_baku: [],
    jumlah_produksi: null,
    tanggal_kadaluarsa: null,
    status: null,
    mulai_produksi: null,
    selesai_produksi: null,
    qrcode_path: null,
    lokasi_penyimpanan_id: null,
    suhu_penyimpanan: null,
    _method : 'post',
})

const formSelesai = useForm({
    tanggal_kadaluarsa: null,
    produksi_id: null,
    selesai_produksi: null,
})

const formEditKadaluarsa = useForm({
    tanggal_kadaluarsa: null,
    _method : 'put',
})
const menu = ref();
const toggle = (event, id, status, index) => {
    const choice = statusChoice(status, id);
    selectedStatus.value = choice;
    menu.value.toggle(event);
};

// FUNCTION SECTIONS
const openInfo = (data) => {
    selectedProduksi.value = data;
    visibleInfo.value = true;
}
function submitDP(){
    console.log("FORM: ", formDP);
    // return;
    formDP.tanggal_kadaluarsa = formatDateTimeForLaravel(formDP.tanggal_kadaluarsa);
    formDP.mulai_produksi = formatDateTimeForLaravel(formDP.mulai_produksi);
    formDP.selesai_produksi = formatDateTimeForLaravel(formDP.selesai_produksi);
    const submitRoute = actionDP.value === 'create' ? 'stafProduksi.produksi.create.produksi' : 'stafProduksi.produksi.update.produksi';
    formDP.post(route(submitRoute, {produksi: selectedProduksiId.value}), {
        forceFormData: true,
        onSuccess: (response) => {
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }
            visibleDP.value = false;
        },
        onError: (response)=>{
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }else{
                addToast(toast, {
                    severity: 'error',
                    summary: 'Create Data Produksi Failed!',
                    detail: 'Data gagal ditambahkan',
                    life: 5000,
                });
            }
        }
    });
}

function submitSelesai(){
    formSelesai.selesai_produksi = formatDateTimeForLaravel(formSelesai.selesai_produksi);
    formSelesai.tanggal_kadaluarsa = formatDateTimeForLaravel(formSelesai.tanggal_kadaluarsa);
    console.log("FORM: ", formSelesai);
    formSelesai.put(route('stafProduksi.produksi.selesai.produksi', {produksi: formSelesai.produksi_id}), {
        onSuccess: (response) => {
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }
            visibleSelesai.value = false;
        },
        onError: (response)=>{
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }else{
                addToast(toast, {
                    severity: 'error',
                    summary: 'Ubah Status Produksi Failed!',
                    detail: 'Status gagal diubah',
                    life: 5000,
                });
            }
        }
    })
}

function submitEditKadaluarsa(){
    formEditKadaluarsa.tanggal_kadaluarsa = formatDateTimeForLaravel(formEditKadaluarsa.tanggal_kadaluarsa);
    formEditKadaluarsa.put(route('stafProduksi.produksi.edit_kadaluarsa.produksi', {produksi: selectedProduksiId.value}), {
        onSuccess: (response) => {
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }
            visibleKadaluarsa.value = false;
        },
        onError: (response)=>{
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }else{
                addToast(toast, {
                    severity: 'error',
                    summary: 'Ubah Status Produksi Failed!',
                    detail: 'Status gagal diubah',
                    life: 5000,
                });
            }
        }
    })
}

function statusChoice(data, produksiID) {
    let status = null;
    switch (data) {
        case 'Pending':
            status = ['Pending', 'Proses'];
            // status = ['Pending', 'Proses', 'Batal'];
            break;
        case 'Proses':
            status = ['Proses', 'Selesai'];
            // status = ['Proses', 'Selesai', 'Batal'];
            break;
        case 'Selesai':
            status = ['Selesai'];
            // status = ['Selesai', 'Batal'];
            break;
        case 'Batal':
            status = ['Batal']; // Can't swap from Batal
            break;
        default:
            status = [];
    }

    return status.map(item => ({
        selected: data,
        label: item,
        produksi_id: produksiID
    }));
    return status;
}

function ConfirmPopup(status: string, produksiID: number) {
    addConfirm(confirm, {
        message: `Apakah anda yakin ingin mengubah status menjadi "${status}?""`,  
        icon: 'pi pi-question',
        accept: () => {
            switch (status.toLowerCase()) {
                case "pending":
                    break;
                case "proses":
                    actionProses(produksiID)
                    break;
                case "selesai":
                    actionSelesai(produksiID);
                    break;
                case "batal":
                    break;
                default:
                    break;
            }
        }
    })
}

function headerDP (){
    return actionDP.value === 'create' ? 'Tambah Data Produksi' : 'Edit Data Produksi';
}

function openTDP(){
    visibleDP.value = true;
    actionDP.value = 'create';
    formDP._method = 'post';
}

function test(data: object){
    const dataIDs = new Set(dataLotBahanBaku.value.map(item => item.id));
    data.bahan_baku.map(item => {
        if(!dataIDs.has(item.lot_bahan_baku.id)){
            dataLotBahanBaku.value.push(item.lot_bahan_baku);
        }
        dataLotBahanBaku.value.find(i => {
            if(i.id == item.lot_bahan_baku.id){
                // alert('INSIDE');
                i.jumlah_pakai_original = Number(item.jumlah_penggunaan);
                // i.id_original = item.lot_bahan_baku.id;
                item.lot_bahan_baku.jumlah_pakai_original = Number(item.jumlah_penggunaan);
                // item.lot_bahan_baku.id_original = item.lot_bahan_baku.id;
            }
        });
        // @ts-expect-error I EXPECT IT TO ERROR YEAH, IT WORKS SMOOTH 
        formDP.bahan_baku.push({
            lot_bahan_baku: item.lot_bahan_baku,
            jumlah_pakai: Number(item.jumlah_penggunaan),
            // id_original: item.lot_bahan_baku.id,
        });
        console.log("ASLKJDLKAJSKLDJSALKDJLASKJDAKSJDKLASJDLKASJDLKASJLDKJSA: ", formDP.bahan_baku);
        console.log("ASLKJDLKAJSKLDJSALKDJLASKJDAKSJDKLASJDLKASJDLKASJLDKJSA: ", dataLotBahanBaku.value);
    });
}

function openEDP(data:object){
    test(data);



    // return;
    selectedProduksiId.value = data.id; 
    formDP.produk_id = data.produk;
    formDP.jumlah_produksi = data.kuantitas;
    formDP.lokasi_penyimpanan_id = data.lokasi_penyimpanan.id;
    formDP.suhu_penyimpanan = data.suhu_penyimpanan;
    suhuDefault.value = data.suhu_penyimpanan;
    handleImage(data.produk.gambar);
    // data.bahan_baku.forEach((item, index) => {
    //     selectedSatuan.value[index] = item.satuan;
    //     selectedJumlah.value[index] = formatDecimalforInputNumber(item.lot_bahan_baku.jumlah);
    //     formDP.bahan_baku[index] = {
    //         lot_bahan_baku_id: item.lot_bahan_baku,
    //         jumlah_pakai: item.jumlah_penggunaan,
    //         action: 'produksi',
    //     }
    // })
    visibleDP.value = true;
    actionDP.value = 'edit';
    formDP._method = 'put';
}

function openKadaluarsa(data:object){
    selectedProduksiId.value = data.id;
    visibleKadaluarsa.value = true;
    formEditKadaluarsa.tanggal_kadaluarsa = new Date(data.tanggal_kadaluarsa);
    formEditKadaluarsa._method = 'put';
}

function handleImage(data){
    if(data){
        produkBLOB.value = {
            image: data,
            name: '',
        }
    }
}

function addEmptyBahanBaku_formDP(){
    const dumpData = {
        lot_bahan_baku: null,
        jumlah_pakai: null,
        // action: 'produksi',
    }
    formDP.bahan_baku.push(dumpData);
}

function errorBahanBakuId(error: string){
    if(!error) return null;
    return error.replace(/The .+ field /, '').replace('The ', '');
}

function deleteBahanBaku_formDP(index){
    formDP.clearErrors();
    formDP.bahan_baku.splice(index, 1);
    selectedSatuan.value[index] = null;
    selectedJumlah.value[index] = null;
}

function openLBB(data:object){
    selectedBahanBaku.value = data.bahan_baku.map(item => ({
            kode_bahan: item.bahan_baku.kode_bahan,
            nama_bahan: item.bahan_baku.nama_bahan,
            jumlah_penggunaan: formatDecimal(Number(item.jumlah_penggunaan)),
            satuan: item.satuan,
            kode_lot: item.kode_lot,
            nama_supplier: item.bahan_baku.supplier.nama_supplier,
            kode_supplier: item.bahan_baku.supplier.kode_supplier,
        }));
    console.log(data);
    console.log(selectedBahanBaku.value);
    visibleLBB.value = true;
}

function actionSelesai(ProduksiID: number){
    formSelesai.produksi_id = ProduksiID;
    formSelesai.selesai_produksi = new Date();
    visibleSelesai.value = true
}

function actionProses(produksiID: number){
    router.put(route('stafProduksi.produksi.proses.produksi', {produksi: produksiID}), {}, {
        onSuccess: (response) => {
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }
        },
        onError: (response)=>{
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }else{
                addToast(toast, {
                    severity: 'error',
                    summary: 'Update Status Failed!',
                    detail: 'Status gagal diubah',
                    life: 5000,
                });
            }
        }
    });
}

function popupDelete(data: object) {
    addConfirm(confirm, {
        message: 'Apakah anda yakin ingin menghapus data produksi ini?',
        accept: () => {
            router.delete(route('stafProduksi.produksi.delete.produksi', {produksi: data.id}), {
                onSuccess: (response) => {
                    if(response.props?.toast){
                        addToast(toast, response.props?.toast);
                    }
                    afterCloseDP();
                },
                onError: (response) => {
                    if(response.props?.toast){
                        addToast(toast, response.props?.toast);
                    }else{
                        addToast(toast, {
                            severity: 'error',
                            summary: 'Delete Data Produksi Failed!',
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

function maxJumlahPakai(data: object){
    console.log(data);
    console.log("dataLotBahanBaku: ", props.dataLotBahanBaku);
    if(actionDP.value === 'create') return formatDecimalforInputNumber(data.lot_bahan_baku.jumlah);

    return formatDecimalforInputNumber(
        data.lot_bahan_baku.jumlah_pakai_original 
        ? formatDecimalforInputNumber(data.lot_bahan_baku.jumlah_pakai_original) + formatDecimalforInputNumber(data.lot_bahan_baku.jumlah)
        : formatDecimalforInputNumber(data.lot_bahan_baku.jumlah)
    );

    // return formatDecimalforInputNumber(
    //     data.id_original === data.lot_bahan_baku.id 
    //     ? formatDecimalforInputNumber(data.jumlah_pakai_original) + formatDecimalforInputNumber(data.lot_bahan_baku.jumlah) 
    //     : formatDecimalforInputNumber(data.lot_bahan_baku.jumlah)
    // )
    
}

function afterCloseDP(){
    formDP.reset();
    formDP.clearErrors();
    actionDP.value = null;
    produkBLOB.value = null;
    selectedBahanBaku.value = null;
    selectedSatuan.value = [];
    selectedJumlah.value = [];
    selectedProduksiId.value = null;
    selectedProduksi.value = null;
    suhuDefault.value = null;
    dataLotBahanBaku.value = [...props.dataLotBahanBaku];
    dataLokasiPenyimpanan.value = [...props.dataLokasiPenyimpanan];
}

// SEARCH & FILTERS
const sortOption = ref([
    'created at',
    'mulai produksi',
    'selesai produksi',
    'tanggal kadaluarsa',
    'status produksi',
    'jumlah produksi',
])

const rangeOption = ref([
    'mulai produksi',
    'selesai produksi',
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

function submitSearch(){
    formatFilters();
    router.get(route('stafProduksi.produksi'), {s: searchValue.value.trim(), filter: submittedFilters});
}

const changePaginate = (amount: number) => {
    router.get(window.location.pathname + window.location.search, {paginate: amount});
}

const toPage = (page) => {
    router.get(window.location.pathname + window.location.search, {page: page});
}

watch (visibleDP, (newValue, oldValue) => {
    if(newValue === false){
        afterCloseDP();
    }
})

watch(visibleLBB, (newValue, oldValue) => {
    if(newValue === false){
        selectedBahanBaku.value = null;
    }
})

watch(visibleSelesai, (newValue, oldValue) => {
    if(newValue === false){
        formSelesai.reset();
        formSelesai.clearErrors();
    }
})

watch(visibleKadaluarsa, (newValue, oldValue) => {
    if(newValue === false){
        formEditKadaluarsa.reset();
        formEditKadaluarsa.clearErrors();
        selectedProduksiId.value = null;
    }
})

watch(visibleInfo, (newValue) => {
    if(newValue === false){
        selectedProduksi.value = null;
    }
})
</script>
<template>
    {{ console.log("props.dataProduksi: ", props.dataProduksi?.data) }}
    {{ console.log("props.dataProduk: ", props.dataProduk) }}
    {{ console.log("props.dataLotBahanBaku: ", props.dataLotBahanBaku) }}
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
            <div class="flex-1 overflow-hidden">
                <DataTable class="rounded-lg shadow !overflow-auto text-sm" show-gridlines striped-rows :value="dataProduksi?.data">
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold border-b-2 dark:border-surface-400">Data Produksi</span>
                            <Button label="Tambah Data Produksi" @click="openTDP()" />
                        </div>
                    </template>
                    <Column field="nomor_batch" header="Nomor Batch" class="w-fit" style="min-width: 0rem; max-width: 9rem;">
                        <template #body="{ data }">
                            <p v-if="data.nomor_batch" class="text-md">
                                {{ data.nomor_batch }}
                            </p>
                            <span v-else>-</span>
                        </template>
                    </Column>
                    <Column field="produk.gambar" header="Produk" class="w-fit" style="min-width: 0rem; max-width: 9rem;">
                        <template #body="{ data }">
                            <img v-tooltip.top="`${data.produk.nama_produk}`" :src="data.produk.gambar" alt="image" class="w-15 rounded object-cover cursor-pointer" @click="openImage(data.produk.gambar)" />
                            <!-- <span class="text-xs">{{ data.produk.nama_produk }}</span> -->
                        </template>
                    </Column>
                    <Column header="Bahan Baku" class="w-fit" style="min-width: 0rem; max-width: 9rem;">
                        <template #body="{ data }">
                            <!-- <i v-tooltip.top="'Lihat Bahan Baku'" class="pi pi-eye cursor-pointer" @click="openLBB(data)"></i> -->
                            <!-- <div v-tooltip.top="'Lihat Penggunaan Bahan Baku'" class="flex flex-col items-center cursor-pointer hover:border-b-1" @click="openLBB(data)">
                                <i class="pi pi-eye text-blue-400"></i>
                                <p class="text-xs text-blue-400 font-medium capitalize">lihat bahan baku</p>
                            </div> -->
                            <Button v-tooltip.top="'Lihat Bahan Baku'" size="small" icon="pi pi-eye" outlined rounded severity="success" class="mr-2" label="Detail" @click="openLBB(data)"/>
                        </template>
                    </Column>
                    <Column header="Lokasi Penyimpanan" >
                        <template #body="{ data }">
                            <div class="text-primary font-medium">
                                {{ data.lokasi_penyimpanan.name }}
                                <span :class="{'text-red-300': data.suhu_penyimpanan > 30, 'text-green-300': data.suhu_penyimpanan >= 0, 'text-blue-300': data.suhu_penyimpanan < 0}">({{ data.suhu_penyimpanan + ' °C' }})</span>
                            </div>
                        </template>
                    </Column>
                    <Column header="Jumlah" class="w-fit" style="min-width: 0rem; max-width: 9rem;">
                        <template #body="{ data }">
                            <p class="text-center">{{ data.kuantitas }}</p>
                        </template>
                    </Column>
                    <Column field="status" header="Status" class="w-fit" style="min-width: 0rem; max-width: 9rem;">
                        <template #body="{ data, index }">
                            <div class="flex justify-between items-center gap-2 border-1 border-surface p-2 rounded cursor-pointer font-semibold" @click="(event) => toggle(event, data.id, data.status, index)">
                                <p>{{ data.status }}</p>
                                <i class="pi pi-chevron-down"></i>
                            </div>
                            <!-- {{ selectedStatus }} -->
                            <Menu ref="menu" :model="selectedStatus" class="!min-w-[10rem]" :popup="true">
                                <template #item="{ item }">
                                    <div class="p-2" :class="item.selected === item.label ? 'bg-surface-200 dark:bg-surface-800' : 'cursor-pointer'" @click="item.selected === item.label ? '' : ConfirmPopup(item.label, item.produksi_id)">
                                        <span >{{ item.label }}</span>
                                    </div>
                                </template>
                            </Menu>
                        </template>
                    </Column>
                    <Column field="mulai_produksi" header="Mulai Produksi" class="w-fit" style="min-width: 0rem; max-width: 9rem;">
                        <template #body="{ data }">
                            <p v-if="data.mulai_produksi" class="text-md">
                                {{ formatDateTimefromString(data.mulai_produksi) }}
                            </p>
                            <span v-else>-</span>
                        </template>
                    </Column>
                    <Column field="selesai_produksi" header="Selesai Produksi" class="w-fit" style="min-width: 0rem; max-width: 9rem;">
                        <template #body="{ data }">
                            <p v-if="data.selesai_produksi" class="text-md">
                                {{ formatDateTimefromString(data.selesai_produksi) }}
                            </p>
                            <span v-else>-</span>
                        </template>
                    </Column>
                    <Column field="tanggal_kadaluarsa" header="Kadaluarsa" class="w-fit" style="min-width: 0rem; max-width: 9rem;">
                        <template #body="{ data }">
                            <p v-if="data.tanggal_kadaluarsa" class="text-md">
                                {{ formatDatefromString(data.tanggal_kadaluarsa) }}
                            </p>
                            <span v-else>-</span>
                        </template>
                    </Column>
                    <Column field="qrcode_path" header="QR Code" class="w-fit" style="min-width: 0rem; max-width: 9rem;">
                        <template #body="{ data }">
                            <!-- <i class="pi pi-download text-blue-400"></i> -->
                            <img v-if="data.qrcode_path" v-tooltip.top="'Download QR Code'" :src="data.qrcode_path" alt="QR Code" class="w-15 cursor-pointer rounded object-cover" @click="downloadImage(data.qrcode_path)" />
                            <span v-else>-</span>
                        </template>
                    </Column>
                    <Column field="label_path" header="Label" class="w-fit"  style="min-width: 0rem; max-width: 9rem;">
                        <template #body="{ data }">
                            <img v-if="data.label_path" v-tooltip.top="'Download Label'" :src="data.label_path" alt="Label" class="w-15 cursor-pointer rounded object-cover" @click="downloadImage(data.label_path)" />
                            <span v-else>-</span>
                        </template>
                    </Column>
                    <Column header="Action" class="w-fit" header-style="{text-align: center}" style="min-width: 0rem; max-width: 9rem;">
                        <template #body="{ data }">
                            <div class="flex flex-col gap-4 items-center ">
                                <div v-if="data.distribusi_check" class="flex justify-start items-center" v-tooltip.left="'Produksi Sudah Terdistribusi, data tidak dapat diubah lagi!'">
                                    <Button size="small" icon="pi pi-pencil" outlined rounded class="mr-2" disabled/>
                                    <Button size="small" icon="pi pi-trash" outlined rounded class="mr-2" disabled/>
                                </div>
                                <div v-else class="flex justify-start items-center">
                                    <Button size="small" 
                                        v-tooltip.top="`${data.status === 'Selesai' ? 'Edit Kadaluarsa' : 'Edit Produksi'}`"  
                                        icon="pi pi-pencil"
                                        outlined 
                                        rounded 
                                        severity="info"
                                        class="mr-2"
                                        @click="(data.status === 'Selesai' ? openKadaluarsa(data) : openEDP(data))"
                                    />
                                    <Button size="small" icon="pi pi-trash" outlined rounded severity="danger" class="mr-2" @click="popupDelete(data)"/>
                                </div>
                                <!-- <Button size="small" icon="pi pi-eye" outlined rounded severity="success" class="mr-2" label="Detail" @click="openInfo(data)"/> -->
                            </div>
                        </template>
                    </Column>
                    <template v-if="dataProduksi?.data.length <= 0" #empty>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>
                    </template>
                    <template v-if="dataProduksi?.data.length > 0" #footer>
                        <Paginator :pt="{paginatorContainer: 'w-full'}" :first="(dataProduksi?.meta.current_page - 1) * dataProduksi?.meta.per_page" :rows="dataProduksi?.meta.per_page" :totalRecords="dataProduksi?.meta.total" @page="(e) => toPage(e.page + 1)">
                            <template #start="slotProps">
                                <div class="w-full">
                                    Showing {{ slotProps.state.page + 1 }} to {{ dataProduksi?.meta.last_page }} of {{ dataProduksi?.meta.total }} entries
                                    <Select class="w-fit" :options="[10, 25, 50]" :default-value="paginate" placeholder="Select..." @value-change="(value) => changePaginate(value)" />
                                </div>
                            </template>
                        </Paginator>
                    </template>
                </DataTable>
            </div>
        </div>
    </main>

    <!-- LBB Dialog -->
    <Dialog v-model:visible="visibleLBB" modal header="Lihat Bahan Baku" class="w-2/3" :draggable="false" :closable="true">
        <DataTable :value="selectedBahanBaku" pt:table="min-w-200">
            <Column field="kode_lot" header="Kode LOT Bahan Baku"></Column style="min-width: 0rem; max-width: 9rem;">
            <Column field="kode_bahan" header="Kode Bahan"></Column>
            <Column field="nama_bahan" header="Nama Bahan"></Column>
            <Column header="Jumlah Penggunaan">
                <template #body="{ data }">
                    {{ data.jumlah_penggunaan }} {{ data.satuan }}
                </template>
            </Column>
            <Column field="nama_supplier" header="Nama Supplier"></Column>
        </DataTable>
    </Dialog>

    <!-- TAMBAH DIALOG -->
     <Dialog v-model:visible="visibleDP" modal :header="headerDP()" class="w-6xl" :draggable="false" :closable="false">
        <form class="flex flex-col gap-2" @submit.prevent="submitDP">
            <div class="flex gap-5">
                <!-- LEFT -->
                <div class="flex flex-col flex-1 gap-2">
                    <div class="flex justify-between py-2">
                        <span class="text-surface-500 dark:text-surface-400 block">Data Produksi</span>
                    </div>
                    <div class="flex flex-col items-start gap-2">
                        <label for="produk_id" class="font-semibold">Nama Produk</label>
                        <div class="flex gap-2">
                            <Select id="produk_id" v-model="formDP.produk_id" :options="dataProduk" option-label="nama_produk" placeholder="Select Produk" class="flex-auto w-auto h-fit" autocomplete="off" @change="(value) => {
                                handleImage(value.value.gambar);
                            }">
                            </Select>
                        </div>
                        <div v-if="produkBLOB">
                            <img :src="produkBLOB.image" alt="image" class="w-1/5 rounded object-cover" />
                        </div>
                        <InputError :message="formDP.errors.produk_id" class="mt-2" />
                    </div>
                    <div class="flex flex-col items-start gap-2">
                        <label for="kuantitas" class="font-semibold">Jumlah Produksi</label>
                        <div class="flex gap-2">
                            <InputNumber id="kuantitas" v-model="formDP.jumlah_produksi" inputId="currency-id" :min-fraction-digits="0" :max-fraction-digits="3" mode="decimal" locale="id-ID" fluid class="w-full" placeholder="0" />
                            <InputError :message="formDP.errors.jumlah_produksi" class="mt-2" />
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="lokasi_penyimpanan_id" class="font-semibold">Lokasi Penyimpanan</label>
                        <div class="flex-auto w-md">
                            <Select 
                            id="lokasi_penyimpanan_id"
                            v-model="formDP.lokasi_penyimpanan_id" 
                            :options="dataLokasiPenyimpanan" 
                            option-value="id" 
                            option-label="name" 
                            placeholder="Select Lokasi Penyimpanan"
                            filter
                            filter-placeholder="Cari nama lokasi penyimpanan"
                            fluid
                            @value-change="(item) => {
                                const suhu = dataLokasiPenyimpanan.find(i => i.id == item)?.suhu_default;
                                formDP.suhu_penyimpanan = suhu;
                                suhuDefault = suhu;
                            }"
                            >
                                <template #option="{ option }">
                                    <div class="flex">
                                            <div class="flex flex-col items-start gap-1">
                                                <span class="font-medium text-primary-300 dark:text-primary-200">
                                                    Nama: <span class="text-primary"><span class="text-green-400">{{ option.name }}</span></span>
                                                </span>
                                                <span class="font-medium text-primary-300 dark:text-primary-200">
                                                    Deskripsi: <span class="text-primary"><span class="text-green-400">{{ option.deskripsi }}</span></span>
                                                </span>
                                            </div>
                                        </div>
                                </template>
                            </Select>
                            <InputError :message="formDP.errors.lokasi_penyimpanan_id" class="mt-2" />
                        </div>
                    </div>
                    <div class="flex flex-col items-start gap-2">
                        <label for="suhu_penyimpanan" class="font-semibold">Suhu Penyimpanan</label>
                        <div class="flex gap-2">
                            <InputNumber 
                            v-tooltip.left="suhuDefault ? `Default: ${suhuDefault} °C` : ''"
                            id="suhu_penyimpanan" 
                            class="w-full" 
                            v-model="formDP.suhu_penyimpanan" 
                            :min-fraction-digits="0" 
                            :max-fraction-digits="2" 
                            :max="1000"
                            :min="-1000"
                            mode="decimal" 
                            locale="id-ID" 
                            fluid 
                            suffix=" °C"
                            placeholder="0.00 °C" />
                            <InputError :message="formDP.errors.suhu_penyimpanan" class="mt-2" />
                        </div>
                    </div>
                </div>
                <Divider layout="vertical" />
                <!-- RIGHT -->
                <div class="flex flex-col flex-1">
                    <div class="flex justify-between py-2">
                        <p class="text-surface-500 dark:text-surface-400 block">Data Bahan Baku</p>
                        <!-- <i v-if="actionDP !== 'create' ? false : true" class="pi pi-plus-circle mr-5 cursor-pointer" style="font-size: 1.2rem" @click="addEmptyBahanBaku_formDP()"></i> -->
                        <i class="pi pi-plus-circle mr-5 cursor-pointer" style="font-size: 1.2rem" @click="addEmptyBahanBaku_formDP()"></i>
                    </div>
                    <DataTable :value="formDP.bahan_baku" >
                        <Column header="Nama Bahan">
                            <template #body="{ data, index }">
                                <Select 
                                v-model:model-value="data.lot_bahan_baku" 
                                :options="dataLotBahanBaku"
                                filter
                                filter-placeholder="Cari nama bahan"
                                option-label="bahan_baku.nama_bahan" 
                                placeholder="Select Bahan"
                                @value-change="(value) => {
                                    console.log(dataLotBahanBaku?.find(item => item.id == value.id));
                                    data.jumlah_pakai = null;
                                }"
                                fluid
                                :class="{'mb-7': formDP.errors[`bahan_baku.${index}.jumlah_pakai`]}"
                                >
                                    <template #option="{option}">
                                        <!-- <div class="flex flex-col items-start gap-1" :class="[slotProps.option.id === formDD.produksi_id ? 'text-green-300' : '']"> -->
                                        <div class="flex">
                                            <div class="flex flex-col items-start gap-1">
                                                <span class="font-medium text-primary-300 dark:text-primary-200">
                                                    Nama Bahan: <span class="text-primary"><span class="text-green-400">{{ option.bahan_baku.nama_bahan }}</span> - {{ option.kode_lot }}</span>
                                                </span>
                                                <span class="font-medium text-primary-300 dark:text-primary-200">
                                                    Nama Retailer: <span class="text-primary">{{ option.bahan_baku.supplier.nama_supplier }} - {{ option.bahan_baku.supplier.kode_supplier }}</span>
                                                </span>
                                                
                                            </div>
                                            <Divider  layout="vertical" />
                                            <div class="flex flex-col items-start gap-1">
                                                <span class="font-medium text-primary-300 dark:text-primary-200">
                                                    Jumlah Tersisa: <span class="text-primary">{{ formatDecimalforInputNumber(option.jumlah) }} {{ option.satuan }}</span>
                                                </span>
                                                <span class="font-medium text-primary-300 dark:text-primary-200">
                                                    Tanggal Kadaluarsa: <span class="text-primary">{{ parseDate(option.tanggal_kadaluarsa, 'DD/MM/YYYY') }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </template>
                                </Select>
                                <InputError v-if="data.lot_bahan_baku" :message="errorBahanBakuId(formDP.errors[`bahan_baku.${index}.lot_bahan_baku.id`] ?? formDP.errors[`bahan_baku.${index}.lot_bahan_baku.bahan_baku.nama_bahan`])" class="mt-2" />
                                <!-- <InputError v-if="data.lot_bahan_baku" :message="errorBahanBakuId()" class="mt-2" /> -->
                                
                            </template>
                        </Column>
                        <Column header="Jumlah Penggunaan">
                            <template #body="{ data, index }">
                                <!-- <InputText v-model="data.jumlah_pakai" /> -->
                                <InputNumber 
                                v-if="data.lot_bahan_baku" 
                                v-tooltip.top="`max: ${maxJumlahPakai(data)} ${data.lot_bahan_baku.satuan}`"
                                v-model="data.jumlah_pakai" 
                                inputId="currency-id" 
                                :min-fraction-digits="0" 
                                :max-fraction-digits="0" 
                                :max="maxJumlahPakai(data)" 
                                :suffix="` ${data.lot_bahan_baku.satuan}`"  
                                locale="id-ID" 
                                fluid 
                                class="w-full" 
                                :class="{'mb-7' : formDP.errors[`bahan_baku.${index}.lot_bahan_baku.id`] || formDP.errors[`bahan_baku.${index}.lot_bahan_baku.bahan_baku.nama_bahan`]}"
                                :placeholder="`0 ${data.lot_bahan_baku.satuan}`" 
                                />
                                <InputError v-if="data.lot_bahan_baku" :message="errorBahanBakuId(formDP.errors[`bahan_baku.${index}.jumlah_pakai`])" class="mt-2" />
                                <!-- <InputNumber :disabled="actionDP !== 'create' ? true : false" v-tooltip.top="`${actionDP === 'create' ? 'max: ' + formatDecimal(selectedJumlah[index]) + ' ' + selectedSatuan[index] : ''}`" id="kuantitas" v-model="data.jumlah_pakai" inputId="currency-id" :min-fraction-digits="0" :max-fraction-digits="3" mode="decimal" locale="id-ID" fluid class="w-full" :placeholder="`0 ${selectedSatuan[index]}`" :suffix="` ${selectedSatuan[index]}`" input-id="jumlah_pakai" :max="actionDP === 'create' ? formatDecimalforInputNumber(selectedJumlah[index]) : null"  /> -->
                            </template>
                        </Column>
                        <Column>
                            <template #body="{ index }">
                                <i class="pi pi-times-circle text-red-400 cursor-pointer" @click="deleteBahanBaku_formDP(index)"></i>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <SecondaryButton type="button" label="Cancel" @click="visibleDP = false" />
                <Button :loading="formDP.processing" type="submit" label="Save" />
            </div>
        </form>
    </Dialog>

    <!-- SELESAI DIALOG -->
    <Dialog v-model:visible="visibleSelesai" modal header="Selesai Produksi" class="w-1/4" :draggable="false">
        <form class="flex flex-col gap-3" @submit.prevent="submitSelesai">
            <div class="flex gap-2">
                <div class="flex flex-col flex-1 gap-2">
                    <label for="tanggal_kadaluarsa" class="font-semibold">Tanggal Kadaluarsa</label>
                    <DatePicker v-model="formSelesai.tanggal_kadaluarsa" showIcon fluid iconDisplay="input" inputId="icondisplay" dateFormat="dd/mm/yy" placeholder="dd/mm/yy" />
                    <InputError :message="formSelesai.errors.tanggal_kadaluarsa" class="mt-2" />
                </div>
                <div class="flex flex-col flex-1 gap-2">
                    <label for="tanggal_selesai" class="font-semibold">Tanggal Selesai</label>
                    <DatePicker v-model="formSelesai.selesai_produksi" showIcon show-time fluid iconDisplay="input" inputId="icondisplay" dateFormat="dd/mm/yy " placeholder="dd/mm/yy" />
                    <InputError :message="formSelesai.errors.selesai_produksi" class="mt-2" />
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <SecondaryButton type="button" label="Cancel" @click="visibleSelesai = false" />
                <Button :loading="formSelesai.processing" type="submit" label="Save" />
            </div>
        </form>
    </Dialog>

    <!-- EDIT KADALUARSA DIALOG -->
    <Dialog v-model:visible="visibleKadaluarsa" modal header="Edit Kadaluarsa" class="w-1/4" :draggable="false">
        <form class="flex flex-col gap-3" @submit.prevent="submitEditKadaluarsa">
            <div class="flex gap-2">
                <div class="flex flex-col flex-1 gap-2">
                    <label for="tanggal_kadaluarsa" class="font-semibold">Tanggal Kadaluarsa</label>
                    <DatePicker v-model="formEditKadaluarsa.tanggal_kadaluarsa" showIcon fluid iconDisplay="input" inputId="icondisplay" dateFormat="dd/mm/yy" placeholder="dd/mm/yy" />
                    <InputError :message="formEditKadaluarsa.errors.tanggal_kadaluarsa" class="mt-2" />
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <SecondaryButton type="button" label="Cancel" @click="visibleKadaluarsa = false" />
                <Button :loading="formEditKadaluarsa.processing" type="submit" label="Save" />
            </div>
        </form>
    </Dialog>

    <Dialog v-model:visible="visibleInfo" modal header="Detail Produksi" class="w-1/4" :draggable="false">

    </Dialog>
</template>
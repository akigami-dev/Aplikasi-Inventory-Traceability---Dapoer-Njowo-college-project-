<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import addToast from '@/composables/addToast';
import { addConfirm, downloadImage, formatDatefromString, formatDateTimeForLaravel, formatDateTimefromString, formatDecimal, formatDecimalforInputNumber, openImage} from '@/composables/Helper';
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head, router, useForm } from '@inertiajs/vue3';
import Column from 'primevue/column';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Button from 'volt-components/Button.vue';
import DataTable from 'volt-components/DataTable.vue';
import DatePicker from 'volt-components/DatePicker.vue';
import Dialog from 'volt-components/Dialog.vue';
import Divider from 'volt-components/Divider.vue';
import InputNumber from 'volt-components/InputNumber.vue';
import Menu from 'volt-components/Menu.vue';
import SecondaryButton from 'volt-components/SecondaryButton.vue';
import Select from 'volt-components/Select.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    ...useDefineProps("Template"),
    dataProduksi: Object,
    dataProduk: Object,
    dataLotBahanBaku: Object,
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
const visibleLBB = ref(false);
const visibleSelesai = ref(false);

const visibleDP = ref(false);
const visibleKadaluarsa = ref(false);
const actionDP = ref();
const produkBLOB = ref();

const formDP = useForm({
    produk_id: null,
    bahan_baku: [],
    kuantitas: null,
    tanggal_kadaluarsa: null,
    status: null,
    mulai_produksi: null,
    selesai_produksi: null,
    qrcode_path: null,
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
function submitDP(){
    formDP.tanggal_kadaluarsa = formatDateTimeForLaravel(formDP.tanggal_kadaluarsa);
    formDP.mulai_produksi = formatDateTimeForLaravel(formDP.mulai_produksi);
    formDP.selesai_produksi = formatDateTimeForLaravel(formDP.selesai_produksi);
    console.log("FORM: ", formDP);
    const submitRoute = actionDP.value === 'create' ? 'stafProduksi.produksi.create.produksi' : 'stafProduksi.produksi.update.produksi';
    formDP.post(route(submitRoute, {produksi: selectedProduksiId.value}), {
        forceFormData: true,
        onSuccess: (response) => {
            console.log("SUCCESS: ", response);
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }
            visibleDP.value = false;
        },
        onError: (response)=>{
            console.log("ERROR: ", response);
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
    console.log("FORM: ", formSelesai);
    formSelesai.put(route('stafProduksi.produksi.selesai.produksi', {produksi: formSelesai.produksi_id}), {
        onSuccess: (response) => {
            console.log("SUCCESS: ", response);
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }
            visibleSelesai.value = false;
        },
        onError: (response)=>{
            console.log("ERROR: ", response);
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
    console.log("Tanggal Kadaluarsa: ", formEditKadaluarsa.tanggal_kadaluarsa);
    formEditKadaluarsa.put(route('stafProduksi.produksi.edit_kadaluarsa.produksi', {produksi: selectedProduksiId.value}), {
        onSuccess: (response) => {
            console.log("SUCCESS: ", response);
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }
            visibleKadaluarsa.value = false;
        },
        onError: (response)=>{
            console.log("ERROR: ", response);
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
                    console.log("PENDING");
                    break;
                case "proses":
                    actionProses(produksiID)
                    break;
                case "selesai":
                    actionSelesai(produksiID);
                    break;
                case "batal":
                    console.log("BATAL");
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

function openEDP(data:object){
    console.log("DATA: ", data);
    selectedProduksiId.value = data.id;
    formDP.produk_id = data.produk;
    formDP.kuantitas = data.kuantitas;
    data.bahan_baku.forEach((item, index) => {
        selectedSatuan.value[index] = item.satuan;
        selectedJumlah.value[index] = formatDecimalforInputNumber(item.lot_bahan_baku.jumlah);
        formDP.bahan_baku[index] = {
            lot_bahan_baku_id: item.lot_bahan_baku,
            jumlah_pakai: item.jumlah_penggunaan,
            action: 'produksi',
        }
    })

    console.log("FORM: ", formDP);
    console.log('selectedSatuan: ', selectedSatuan.value);
    console.log('selectedJumlah: ', selectedJumlah.value);
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
        lot_bahan_baku_id: null,
        jumlah_pakai: null,
        action: 'produksi',
    }
    formDP.bahan_baku.push(dumpData);
}

function errorBahanBakuId(error: string){
    if(!error) return null;
    return error.replace(/The .+ field /, '').replace('The ', '');
}

function deleteBahanBaku_formDP(index){
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
            console.log("SUCCESS: ", response);
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }
        },
        onError: (response)=>{
            console.log("ERROR: ", response);
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

watch (visibleDP, (newValue, oldValue) => {
    if(newValue === false){
        formDP.reset();
        formDP.clearErrors();
        actionDP.value = null;
        produkBLOB.value = null;
        selectedBahanBaku.value = null;
        selectedSatuan.value = [];
        selectedJumlah.value = [];
        selectedProduksiId.value = null;
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
</script>
<template>
    {{ console.log("DATAPRODUKSI: ", props.dataProduksi) }}
    {{ console.log("DATAPRODUK: ", props.dataProduk) }}
    {{ console.log("DATALOTBAHANBAKU: ", props.dataLotBahanBaku) }}
    <Head :title="props.title" />
    <main>
        <div class="main-container flex flex-col border dark:border-0">
            <div class="flex-1">
                <DataTable v-if="dataProduksi" class="border border-surface rounded-lg shadow !overflow-auto !border-b-0" :value="dataProduksi" pt:table="">
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold border-b-2 dark:border-surface-400">Data Produksi</span>
                            <Button label="Tambah Data Produksi" @click="openTDP()" />
                        </div>
                    </template>
                    <Column field="nomor_batch" header="Nomor Batch" class="w-fit">
                        <template #body="{ data }">
                            <p v-if="data.nomor_batch" class="text-md">
                                {{ data.nomor_batch }}
                            </p>
                            <span v-else>-</span>
                        </template>
                    </Column>
                    <Column field="produk.gambar" header="Produk" class="w-fit">
                        <template #body="{ data }">
                            <img v-tooltip.top="`${data.produk.nama_produk}`" :src="data.produk.gambar" alt="image" class="w-20 rounded object-cover cursor-pointer" @click="openImage(data.produk.gambar)" />
                            <!-- <span class="text-xs">{{ data.produk.nama_produk }}</span> -->
                        </template>
                    </Column>
                    <Column header="Bahan Baku" class="w-fit">
                        <template #body="{ data }">
                            <i v-tooltip.top="'Lihat Bahan Baku'" class="pi pi-eye cursor-pointer" @click="openLBB(data)"></i>
                        </template>
                    </Column>
                    <Column field="status" header="Status" class="w-fit">
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
                    <Column field="mulai_produksi" header="Mulai Produksi" class="w-fit">
                        <template #body="{ data }">
                            <p v-if="data.mulai_produksi" class="text-md">
                                {{ formatDateTimefromString(data.mulai_produksi) }}
                            </p>
                            <span v-else>-</span>
                        </template>
                    </Column>
                    <Column field="selesai_produksi" header="Selesai Produksi" class="w-fit">
                        <template #body="{ data }">
                            <p v-if="data.selesai_produksi" class="text-md">
                                {{ formatDateTimefromString(data.selesai_produksi) }}
                            </p>
                            <span v-else>-</span>
                        </template>
                    </Column>
                    <Column field="tanggal_kadaluarsa" header="Tanggal Kadaluarsa" class="w-fit">
                        <template #body="{ data }">
                            <p v-if="data.tanggal_kadaluarsa" class="text-md">
                                {{ formatDatefromString(data.tanggal_kadaluarsa) }}
                            </p>
                            <span v-else>-</span>
                        </template>
                    </Column>
                    <Column header="Jumlah Produksi" class="w-fit">
                        <template #body="{ data }">
                            <p class="text-center">{{ data.kuantitas }}</p>
                        </template>
                    </Column>
                    <Column field="qrcode_path" header="QR Code" class="w-fit">
                        <template #body="{ data }">
                            <!-- <i class="pi pi-download text-blue-400"></i> -->
                            <img v-if="data.qrcode_path" v-tooltip.top="'Download QR Code'" :src="data.qrcode_path" alt="QR Code" class="w-20 cursor-pointer rounded object-cover" @click="downloadImage(data.qrcode_path)" />
                            <span v-else>-</span>
                        </template>
                    </Column>
                    <Column field="label_path" header="Label" class="w-fit" >
                        <template #body="{ data }">
                            <img v-if="data.label_path" v-tooltip.top="'Download Label'" :src="data.label_path" alt="Label" class="w-20 cursor-pointer rounded object-cover" @click="downloadImage(data.label_path)" />
                            <span v-else>-</span>
                        </template>
                    </Column>
                    <Column header="Action" class="w-fit">
                        <template #body="{ data }">
                            <div v-if="data.distribusi_check" class="flex justify-start items-center gap-2" v-tooltip.left="'Produksi Sudah Terdistribusi, data tidak dapat diubah lagi!'">
                                <i 
                                    class="pi pi-pencil text-blue-400 opacity-40"
                                    style="font-size: 1.2rem" >
                                </i>
                                <i 
                                    class="pi pi-trash text-red-400 opacity-40"
                                    style="font-size: 1.2rem">
                                </i>
                            </div>
                            <div v-else class="flex justify-start items-center gap-2">
                                <i 
                                    v-tooltip.top="`${data.status === 'Selesai' ? 'Edit Kadaluarsa' : 'Edit Produksi'}`" 
                                    class="pi pi-pencil text-blue-400 cursor-pointer"
                                    style="font-size: 1.2rem" 
                                    @click="(data.status === 'Selesai' ? openKadaluarsa(data) : openEDP(data))">
                                </i>
                                <i 
                                    class="pi pi-trash text-red-400 cursor-pointer"
                                    style="font-size: 1.2rem" 
                                    @click="popupDelete(data)">
                                </i>
                            </div>
                        </template>
                    </Column>
                    <template v-if="dataProduksi?.length <= 0" #footer>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>                        
                    </template>
                </DataTable>
            </div>
        </div>
    </main>

    <!-- LBB Dialog -->
    <Dialog v-model:visible="visibleLBB" modal header="Lihat Bahan Baku" class="w-2/3" :draggable="false" :closable="true">
        <DataTable :value="selectedBahanBaku" pt:table="min-w-200">
            <Column field="kode_lot" header="Kode LOT Bahan Baku"></Column>
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

    <!-- DIALOG -->
     <Dialog v-model:visible="visibleDP" modal :header="headerDP()" class="w-2/3" :draggable="false" :closable="false">
        <form class="flex flex-col gap-2" @submit.prevent="submitDP">
            <div class="flex gap-5">
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
                            <InputNumber id="kuantitas" v-model="formDP.kuantitas" inputId="currency-id" :min-fraction-digits="0" :max-fraction-digits="3" mode="decimal" locale="id-ID" fluid class="w-full" placeholder="0" />
                            <InputError :message="formDP.errors.kuantitas" class="mt-2" />
                        </div>
                    </div>
                </div>
                <Divider layout="vertical" />
                <div class="flex flex-col flex-1">
                    <div class="flex justify-between py-2">
                        <p class="text-surface-500 dark:text-surface-400 block">Data Bahan Baku</p>
                        <i v-if="actionDP !== 'create' ? false : true" class="pi pi-plus-circle mr-5 cursor-pointer" style="font-size: 1.2rem" @click="addEmptyBahanBaku_formDP()"></i>
                    </div>
                    <DataTable :value="formDP.bahan_baku" >
                        <Column field="lot_bahan_baku_id" header="Nama Bahan">
                            <template #body="{ data, index }">
                                <div class="flex flex-col items-start gap-2">
                                    <div class="flex justify-start gap-1">
                                        <Select :disabled="actionDP !== 'create' ? true : false" v-model="data.lot_bahan_baku_id" :options="dataLotBahanBaku" option-label="bahan_baku.nama_bahan" placeholder="Bahan" @change="(value) => {
                                            if(value.value.status !== 'tersedia'){
                                                data.jumlah_pakai = null;
                                                deleteBahanBaku_formDP(index);
                                                addToast(toast, {severity: 'warn', summary: 'Bahan Baku Tidak Tersedia', detail: `Bahan baku ${value.value.bahan_baku.nama_bahan} [${value.value.kode_lot}] ${value.value.status}`, life: 5000,});
                                                return;
                                            }
                                            selectedSatuan[index] = value.value.satuan;
                                            selectedJumlah[index] = value.value.jumlah;
                                            data.jumlah_pakai = null;
                                        }" required >
                                            <template #option="slotProps">
                                                <span class="text-xs text-green-400">
                                                    {{ slotProps.option.bahan_baku.nama_bahan }} - {{ slotProps.option.bahan_baku.supplier.nama_supplier }} ({{ slotProps.option.kode_lot }})
                                                </span>
                                                <span class="text-xs font-semibold text-surface-500 pl-[1ch]" >
                                                    {{ slotProps.option.status === 'tersedia' ? `expired pada ${ formatDatefromString(slotProps.option.tanggal_kadaluarsa) }` : '' }}
                                                </span>
                                            </template>
                                        </Select>
                                    </div>
                                    <InputError :message="errorBahanBakuId(formDP.errors[`bahan_baku.${index}.id`])" class="mt-2" />
                                </div>
                            </template>
                        </Column>
                        <Column field="jumlah_pakai" header="Jumlah Pakai">
                            <template #body="{ data, index }">
                                <div class="flex flex-col items-start gap-2" v-if="selectedSatuan[index]">
                                    <div class="flex flex-col justify-center gap-1">
                                        <!-- <span class="text-xs text-surface-400">max: {{ formatDecimal(selectedJumlah[index]) }} {{ selectedSatuan[index] }}</span> -->
                                        <InputNumber :disabled="actionDP !== 'create' ? true : false" v-tooltip.top="`${actionDP === 'create' ? 'max: ' + formatDecimal(selectedJumlah[index]) + ' ' + selectedSatuan[index] : ''}`" id="kuantitas" v-model="data.jumlah_pakai" inputId="currency-id" :min-fraction-digits="0" :max-fraction-digits="3" mode="decimal" locale="id-ID" fluid class="w-full" :placeholder="`0 ${selectedSatuan[index]}`" :suffix="` ${selectedSatuan[index]}`" input-id="jumlah_pakai" :max="actionDP === 'create' ? formatDecimalforInputNumber(selectedJumlah[index]) : null"  />
                                        <!-- <span v-if="selectedSatuan[index]">{{ selectedSatuan[index] }}</span> -->
                                    </div>
                                    <InputError :message="formDP.errors[`bahan_baku.${index}.jumlah_pakai`]" />

                                </div>
                            </template>
                        </Column>
                        <Column>
                            <template #body="{index}">
                                <i v-if="actionDP !== 'create' ? false : true" class="pi pi-times-circle text-red-400 cursor-pointer" @click="deleteBahanBaku_formDP(index)"></i>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <SecondaryButton type="button" label="Cancel" @click="visibleDP = false" />
                <Button type="submit" label="Save" />
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
                <Button type="submit" label="Save" />
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
                <Button type="submit" label="Save" />
            </div>
        </form>
    </Dialog>
</template>
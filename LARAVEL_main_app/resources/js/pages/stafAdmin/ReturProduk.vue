<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import addToast from '@/composables/addToast';
import { addConfirm, capitalize, parseDate, parseDateForDatePicker } from '@/composables/Helper';
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
import InputText from 'volt-components/InputText.vue';
import SecondaryButton from 'volt-components/SecondaryButton.vue';
import Select from 'volt-components/Select.vue';
import Textarea from 'volt-components/Textarea.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    ...useDefineProps("Template"),
    retur_produk: Object,
    distribusi: Object,
    search: String,
})

defineOptions({
    layout: useAppLayout()
})

const toast = useToast();
const confirm = useConfirm();

// VARIABLE SECTIONS
const searchValue = ref(props.search ?? '');
const visibleDRP = ref(false);
const selectedDistribusi = ref(null);
const selectedRetur = ref(null);
const visibleDetail = ref(false);
const distribusi = ref(props.distribusi);

const actionDRP = ref();

const formDRP = useForm({
    retur_id: '',
    distribusi_id: '',
    tanggal_retur: new Date(),
    jumlah_retur: '',
    tindakan: '',
    keterangan: '',
    catatan_tambahan: '',
    _method: 'post',
})
const tindakan = [
    'Disimpan',
    'Dibuang',
    'Diperbaiki',
    'Diganti',
] 

// FUNCTION SECTIONS
function submitDRP(){
    if(formDRP.distribusi_id) formDRP.distribusi_id = formDRP.distribusi_id.id;
    if(formDRP.tanggal_retur) formDRP.tanggal_retur = parseDate(formDRP.tanggal_retur);
    
    const submitRoute = actionDRP.value === 'create' ? 'stafAdmin.retur_produk.create.retur' : 'stafAdmin.retur_produk.update.retur';

    formDRP.post(route(submitRoute, {retur_produk: formDRP, retur: formDRP.retur_id ?? null}), {
        onSuccess: (response) => {
            actionDRP.value = 'create';
            visibleDRP.value = false;
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }else{
                addToast(toast, {
                    severity: 'success',
                    summary: 'Success!',
                    detail: 'Retur Produk berhasil ditambahkan',
                    life: 5000,
                });
            }
        },
        onError: (response) => {
            if(response.props?.toast){
                addToast(toast, response.props.toast);
            }else{
                addToast(toast, {
                    severity: 'error',
                    summary: 'Failed!',
                    detail: 'Retur Produk gagal ditambahkan',
                    life: 5000,
                });
            }
        },
        preserveScroll: true,
        preserveUrl: true
    });
}

function submitSearch(){
    router.get(route('stafAdmin.retur_produk'), {s: searchValue.value.trim()});
}

function openTDRP(){
    visibleDRP.value = true;
    actionDRP.value = 'create';
}

function openEDRP(data){
    visibleDRP.value = true;
    actionDRP.value = 'edit';
    props.distribusi.push(data.distribusi);
    formDRP.retur_id = data.id;
    formDRP.distribusi_id = data.distribusi;
    formDRP.tanggal_retur = parseDateForDatePicker(data.tanggal_retur);
    formDRP.jumlah_retur = data.jumlah_retur;
    formDRP.tindakan = capitalize(data.tindakan);
    formDRP.keterangan = data.keterangan;
    formDRP.catatan_tambahan = data.catatan_tambahan;
    formDRP._method = 'put';
}

function openDetail(data){
    visibleDetail.value = true;
    selectedRetur.value = data;
}

function popupDelete(retur_id){
    addConfirm(confirm, {
        message: 'Apakah anda yakin ingin menghapus data retur produk ini?',
        accept: () => {
            router.delete(route('stafAdmin.retur_produk.delete.retur', {retur: retur_id}), {
                onSuccess: (response) => {
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
                            summary: 'Delete Data Retur Produk Failed!',
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

watch (visibleDRP, (newValue, oldValue) => {
    if(newValue === false){
        formDRP.reset();
        formDRP.clearErrors();
        selectedDistribusi.value = null;
        if(actionDRP.value !== 'create') {
            props.distribusi.pop();
        }
        distribusi.value = props.distribusi;
        console.log("TEST: ", props.distribusi);
    }
})

watch(visibleDetail, (newValue, oldValue) => {
    if(newValue === false){
        selectedRetur.value = null;
    }
})

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
            <div class="flex-1 border border-surface rounded-lg shadow !overflow-auto !border-b-0">
                <DataTable :value="retur_produk" pt:table="min-w-200">
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold border-b-1 border-surface-300 dark:border-surface-600">Data Retur Produk</span>
                            <Button label="Tambah Data Return" @click="openTDRP()" />
                        </div>
                    </template>
                    
                    <Column field="distribusi.kode_distribusi" header="Kode Distribusi"></Column>
                    <Column field="distribusi.produksi.nomor_batch" header="Nomor Batch"></Column>
                    <Column header="Tanggal Return">
                        <template #body="{ data }">
                            {{ parseDate(data.tanggal_retur, 'DD MMM YYYY HH:mm') }}
                        </template>
                    </Column>
                    <Column field="jumlah_retur" header="Jumlah Retur"></Column>
                    <Column field="user.name" header="Dibuat Oleh"></Column>
                    <Column header="Action">
                        <template #body="{ data }">
                            <div class="flex justify-start items-center gap-2">
                                <i v-tooltip.top="'Lihat Detail'" class="pi pi-eye text-surface-600 dark:text-surface-400 cursor-pointer" style="font-size: 1.2rem" @click="openDetail(data)"></i>
                                <i class="pi pi-pencil text-blue-400 cursor-pointer" style="font-size: 1.2rem" @click="openEDRP(data)"></i>
                                <i class="pi pi-trash text-red-400 cursor-pointer" style="font-size: 1.2rem" @click="popupDelete(data.id)"></i>
                            </div>
                        </template>
                    </Column>
                    <template v-if="retur_produk?.length <= 0" #footer>
                        <div class="text-center py-5 text-surface-500">
                            Data Tidak Ditemukan
                        </div>                        
                    </template>
                </DataTable>
            </div>
        </div>
    </main>
    
    <Dialog v-model:visible="visibleDRP" modal header="Tambah Data Retur Produk" class="w-2/3" :draggable="false">
        <form @submit.prevent="submitDRP">
            <div class="flex gap-2">
                <div class="flex-1 flex flex-col gap-2">
                    <div class="grid grid-cols-2">
                        <div class="flex flex-col gap-2 border-r-1 border-surface pr-7">
                            <div class="flex flex-col items-start gap-1">
                                <label for="produksi_id" class="font-semibold">Nomor Batch</label>
                                <Select class="w-full" :options="distribusi" optionLabel="kode_distribusi" placeholder="Select Batch..." @value-change="(item) => {
                                    formDRP.distribusi_id = item;
                                    formDRP.jumlah_retur = null;
                                }">
                                    <template #option="slotProps">
                                        <div class="flex flex-col items-start gap-1" :class="[slotProps.option.id === formDRP.distribusi_id?.id ? 'text-green-300' : '']">
                                            <span class="font-semibold">Kode Distribusi: {{ slotProps.option.kode_distribusi }}</span>
                                            <span class="font-semibold">Nomor Batch: {{ slotProps.option.produksi.nomor_batch }}</span>
                                            <span class="font-semibold">Produk: {{ slotProps.option.produksi.produk.nama_produk }} - {{ slotProps.option.produksi.produk.kode_produk }}</span>
                                            <span class="font-semibold">Jumlah produksi: {{ slotProps.option.produksi.kuantitas }} </span>
                                        </div>
                                    </template>
                                </Select>
                                <InputError :message="formDRP.errors.distribusi_id" class="mt-2 w-full" />
                            </div>
                            <div class="flex flex-col items-start gap-1">
                                <label for="produksi_id" class="font-semibold">Tanggal Retur</label>
                                <DatePicker class="w-full" v-model="formDRP.tanggal_retur" showIcon show-time fluid iconDisplay="input" inputId="icondisplay" dateFormat="dd/mm/yy" placeholder="dd/mm/yy" />
                                <InputError :message="formDRP.errors.tanggal_retur" class="mt-2 w-full" />
                            </div>
                            <div class="flex flex-col items-start gap-1">
                                <label for="jumlah_retur" class="font-semibold">Jumlah Retur <span v-if="formDRP.distribusi_id">[ max. {{ formDRP.distribusi_id.produksi.kuantitas }} ]</span></label>
                                <InputNumber v-if="formDRP.distribusi_id" class="w-full" v-model="formDRP.jumlah_retur" placeholder="0" autocomplete="off" :max="formDRP.distribusi_id.produksi.kuantitas" />
                                <InputNumber v-else class="w-full" v-model="formDRP.jumlah_retur" autocomplete="off" :disabled="true" />
                                <InputError :message="formDRP.errors.jumlah_retur" class="mt-2 w-full" />
                            </div>
                            <div class="flex flex-col items-start gap-1">
                                <label for="dibuat_oleh" class="font-semibold">Tindakan</label>
                                <Select class="w-full" v-model="formDRP.tindakan" :options="tindakan" placeholder="Select Tindakan..."  />
                                <InputError :message="formDRP.errors.tindakan" class="mt-2 w-full" />
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 pl-7">
                            <span class="font-semibold">Data Produk </span>
                            <div v-if="formDRP.distribusi_id" class="flex flex-col gap-2">
                                <span class="flex">
                                    <div class="w-40">Kode Distribusi</div>
                                    <span class="w-fit px-3">:</span>
                                    <span> {{ formDRP.distribusi_id.kode_distribusi }} </span>
                                </span>
                                <span class="flex">
                                    <div class="w-40">Nomor Batch</div>
                                    <span class="w-fit px-3">:</span>
                                    <span> {{ formDRP.distribusi_id.produksi.nomor_batch }} </span>
                                </span>
                                <span class="flex">
                                    <div class="w-40">Tanggal Pengiriman</div>
                                    <span class="w-fit px-3">:</span>
                                    <span> {{ parseDate(formDRP.distribusi_id.tanggal_pengiriman, 'DD MMM YYYY') }} </span>
                                </span>
                                <span class="flex">
                                    <div class="w-40">Nama Produk</div>
                                    <span class="w-fit px-3">:</span>
                                    <span> {{ formDRP.distribusi_id.produksi.produk.nama_produk }} </span>
                                </span>
                                <span class="flex">
                                    <div class="w-40">Kode Produk</div>
                                    <span class="w-fit px-3">:</span>
                                    <span> {{ formDRP.distribusi_id.produksi.produk.kode_produk }} </span>
                                </span>
                                <img :src="formDRP.distribusi_id.produksi.produk.gambar" alt="Produk" class="max-w-30">
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col items-start gap-1">
                        <label for="keterangan" class="font-semibold">Keterangan</label>
                        <Textarea v-model="formDRP.keterangan" id="keterangan" rows="3" :auto-resize="true" class="flex-auto w-full" autocomplete="on" />
                        <InputError :message="formDRP.errors.keterangan" class="mt-2" />
                    </div>
                    <div class="flex flex-col items-start gap-1">
                        <label for="catatan_tambahan" class="font-semibold">Catatan Tambahan</label>
                        <Textarea v-model="formDRP.catatan_tambahan" id="catatan_tambahan" rows="3" :auto-resize="true" class="flex-auto w-full" autocomplete="on" />
                        <InputError :message="formDRP.errors.catatan_tambahan" class="mt-2" />
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-4 gap-2">
                <SecondaryButton type="button" label="Cancel" @click="visibleDRP = false" />
                <Button type="submit" label="Save" />
            </div>
        </form>
    </Dialog>

    <Dialog v-model:visible="visibleDetail" modal header="Detail Retur Produk" class="w-2/5" :draggable="false" dismissable-mask>
        <div class="flex gap-4 mb-4 border-b border-surface pb-5">
            <div class="flex-1/2 flex justify-between gap-4">
                <div class="flex flex-col gap-2">
                    <span class="flex-1/3 flex rounded">
                        <div class="w-30 font-semibold">Kode Distribusi</div>
                        <span class="w-fit px-3">:</span>
                        <span> {{ selectedRetur.distribusi.kode_distribusi }} </span>
                    </span>
                    <span class="flex-1/3 flex rounded">
                        <div class="w-30 font-semibold">Nomor Batch</div>
                        <span class="w-fit px-3">:</span>
                        <span> {{ selectedRetur.distribusi.produksi.nomor_batch }} </span>
                    </span>
                    <span class="flex-1/3 flex rounded">
                        <div class="w-30 font-semibold">Tanggal Retur</div>
                        <span class="w-fit px-3">:</span>
                        <span> {{ parseDate(selectedRetur.tanggal_retur, 'DD MMM YYYY HH:mm') }} </span>
                    </span>
                    <span class="flex-1/3 flex rounded">
                        <div class="w-30 font-semibold">Jumlah Retur</div>
                        <span class="w-fit px-3">:</span>
                        <span> {{ selectedRetur.jumlah_retur }} </span>
                    </span>
                    <span class=" flex rounded">
                        <div class="w-30 font-semibold">Tindakan</div>
                        <span class="w-fit px-3">:</span>
                        <span> {{ selectedRetur.tindakan }} </span>
                    </span>
                    <span class=" flex rounded">
                        <div class="w-30 font-semibold">Dibuat Oleh</div>
                        <span class="w-fit px-3">:</span>
                        <span> {{ selectedRetur.user.name }} </span>
                    </span>
                </div>
                <img :src="selectedRetur.distribusi.produksi.produk.gambar" alt="asd" class="max-w-40 rounded">
            </div>
        </div>
        <div class="flex-1 flex flex-col gap-6 px-10">
            <span class="flex flex-col items-center rounded">
                <div class="font-semibold pb-3 text-center">Keterangan</div>
                <span class="border-b border-surface px-5 py-2 text-center">{{ selectedRetur.keterangan }}</span>
            </span>
            <span class="flex flex-col items-center rounded">
                <div class="font-semibold pb-3 text-center">Catatan Tambahan</div>
                <span class="border-b border-surface px-5 py-2 text-center">{{ selectedRetur.catatan_tambahan ?? '-' }}</span>
            </span>
        </div>
        
    </Dialog>
</template>
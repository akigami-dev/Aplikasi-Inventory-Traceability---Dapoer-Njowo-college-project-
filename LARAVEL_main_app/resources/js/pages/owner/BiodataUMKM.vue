<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import addToast from '@/composables/addToast';
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head, router, useForm } from '@inertiajs/vue3';
import InputMask from 'primevue/inputmask';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Button from 'volt-components/Button.vue';
import InputText from 'volt-components/InputText.vue';
import Textarea from 'volt-components/Textarea.vue';

const props = defineProps({
    ...useDefineProps("Lokasi Penyimpanan"),
    biodata: Object,
})

defineOptions({
    layout: useAppLayout()
})

const toast = useToast();
const confirm = useConfirm();

// VARIABLE SECTIONS
const biodataForm = useForm({
    nama: props.biodata?.nama ?? "",
    alamat: props.biodata?.alamat ?? "",
    no_telpon: props.biodata?.no_telpon ?? "",
    email: props.biodata?.email ?? "",
})

const submit = () =>{
    biodataForm.post(route('owner.biodata_umkm.createorupdate.umkm'), {
        onError: () => {
            addToast(toast, {severity: 'error', summary: 'Failed!', detail: 'Biodata gagal diubah', life: 5000});
        },
        onSuccess: (response) => {
            try{
                addToast(toast, response.props.toast);
            }catch{
            }
        },
    });
}

</script>
<template>

    <Head :title="props.title" />
    <main>
        <div class="main-container flex flex-col border dark:border-0 gap-2">
            <h1 class="capitalize font-semibold text-xl border-b-1 border-surface py-1" >Biodata UMKM</h1>
            <form class="flex flex-col gap-1 py-5" @submit.prevent="submit">
                <div class="flex flex-col gap-2">
                    <div class="flex max-w-2xl items-center gap-2">
                        <label for="" class="capitalize min-w-[8rem]">Nama UMKM</label>
                        <InputText v-model="biodataForm.nama" required fluid />
                    </div>
                    <div class="flex gap-2">
                        <label for="" class="capitalize min-w-[8rem]"></label>
                        <InputError :message="biodataForm.errors.nama" />
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <div class="flex max-w-2xl items-center gap-2">
                        <label for="" class="capitalize min-w-[8rem]">Nomor Telpon</label>
                        <InputMask v-model="biodataForm.no_telpon" mask="9999 9999 9999 99" slot-char="" :auto-clear="false" class="w-full" id="no_telpon" required />
                    </div>
                    <div class="flex gap-2">
                        <label for="" class="capitalize min-w-[8rem]"></label>
                        <InputError :message="biodataForm.errors.no_telpon" />
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <div class="flex max-w-2xl items-center gap-2">
                        <label for="" class="capitalize min-w-[8rem]">Email</label>
                        <InputText v-model="biodataForm.email" required fluid type="email" />
                    </div>
                    <div class="flex gap-2">
                        <label for="" class="capitalize min-w-[8rem]"></label>
                        <InputError :message="biodataForm.errors.email" />
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <div class="flex max-w-2xl items-start gap-2">
                        <label for="new_password" class="capitalize min-w-[8rem]">alamat</label>
                        <Textarea v-model="biodataForm.alamat" required fluid rows="4" />
                    </div>
                    <div class="flex gap-2">
                        <label for="new_password" class="capitalize min-w-[8rem]"></label>
                        <InputError :message="biodataForm.errors.alamat" />
                    </div>
                </div>
                <div class="flex gap-2 items-center">
                    <label for="" class="capitalize min-w-[8rem]"></label>
                    <Button label="Save" type="submit" size="small" class="w-fit flex gap-2 items-center" />
                </div>

            </form>
        </div>
    </main>
</template>
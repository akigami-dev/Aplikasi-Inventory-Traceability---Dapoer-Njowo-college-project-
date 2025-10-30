<script setup lang="ts">
import useAppLayout, { getProps, useDefineProps } from '@/composables/useAppLayout';
import { Head, useForm } from '@inertiajs/vue3';
import Button from 'volt-components/Button.vue';
import SettingsLayout from '@/layouts/settings/settingsLayout.vue';
import InputText from 'volt-components/InputText.vue';
import Image from 'primevue/image';
import { ref } from 'vue';
import InputMask from 'volt-components/InputMask.vue';
import InputError from '@/components/InputError.vue';
import { useToast } from 'primevue/usetoast';
import FileUpload from 'primevue/fileupload';
import Dialog from 'volt-components/Dialog.vue';
import Toast from 'volt-components/Toast.vue';

const toast = useToast();

defineOptions({
    layout: useAppLayout()
})

const props = defineProps({
    ...useDefineProps("Profile"),
})


const user = getProps()?.auth?.user;
const isChange = ref(false);
const visible = ref(false);

const form = useForm({
    id: '',
    name: '',
    email: '',
    no_telpon: '',
    avatar: null,
    _method: 'put',
});

form.id = user.id;
form.name = user.name;
form.email = user.email;
form.no_telpon = user.no_telpon;
const imageURL = ref(user.avatar);

// FUNCTION
function submitForm(){
    console.log("NAME: ", form.name)
    console.log("EMAIL: ", form.email)
    console.log("NO_TELPON: ", form.no_telpon)
    console.log("AVATAR: ", form.avatar)
    
    form.post(route('users.update'), {
        forceFormData: true,
        onSuccess: () => {
            toggleVisible();
            toast.add({ severity: 'success', summary: 'Edit Success', detail: 'Edit profile data', life: 3000 });
        },
        onError: (e) => {
            console.log("ERROR: ", e)
            toast.add({ severity: 'error', summary: 'Edit Failed!', detail: 'Edit profile data', life: 3000 });
        }
    });
}

function toggleVisible(){
    visible.value = !visible.value
}

function handleFileChange(event) {
    const file = event.target.files[0];
    if(file){
        const image = URL.createObjectURL(file);
        imageURL.value = image;
        form.avatar = file;
    }
    console.log("File selected:", file);
}
</script>

<script lang="ts">
// TRIGGERING input tag
export default {
  methods: {
    triggerFileInput() {
      this.$refs.fileInput.click();
    },
  }
};
</script>
<template>
    <Head :title="props.title" />
    <main>
        <SettingsLayout tabLabel="profile">
            <div class="flex gap-5 px-5">
                <Toast />
                <div class="flex-initial flex flex-col justify-center gap-5">
                    <div class="relative">
                        <Image :src="imageURL" alt="Image" class="rounded-full overflow-hidden" preview >
                            <template #image>
                                <img :src="imageURL" alt="image" class="w-55 h-55 rounded-full object-cover border-4 border-primary" />
                            </template>
                            <template #preview="slotProps">
                                <img :src="imageURL" class="scale-50 transition-transform duration-300"  alt="preview" :style="slotProps.style" @click="slotProps.onClick" />
                            </template>
                        </Image>

                        <button class="absolute bottom-0 right-10 cursor-pointer" @click="toggleVisible">
                            <i class="pi pi-pen-to-square rounded-full bg-primary-500 hover:bg-primary p-3 border-2 border-surface"></i>
                        </button>
                    </div>
                </div>
                <div class="relative flex-auto px-10 py-5 flex flex-col gap-2">
                    <div class="flex flex-col gap-1 flex-1">
                        <label for="name" class="font-bold text-xl capitalize">name</label>
                        <InputText id="name" v-model="form.name" placeholder="Enter your name..." disabled />
                    </div>
                    <div class="flex flex-col gap-1 flex-1">
                        <label for="email" class="font-bold text-xl capitalize">email</label>
                        <InputText id="email" v-model="form.email" placeholder="Enter your email..." disabled />
                    </div>
                    <div class="flex flex-col gap-1 flex-1">
                        <label for="no_telpon" class="font-bold text-xl capitalize">nomor telpon</label>
                        <InputMask id="no_telpon" v-model="form.no_telpon" mask="9999 9999 9999 99" slot-char="" :auto-clear="false" placeholder="Enter your phone number..." disabled />
                    </div>
                </div>

                <!-- DIALOG -->
                <Dialog v-model:visible="visible" modal header="Edit Profile" class="w-[40vw]" :draggable="false">
                    <form class="flex gap-10" @submit.prevent="submitForm()">
                    <!-- <form class="flex gap-10"> -->
                        <div class="flex flex-col gap-3 max-w-50 justify-center items-center">
                            <div class="relative w-50 h-50">
                                <div @click="triggerFileInput" for="avatar" class="absolute inset-0 bg-black flex items-center justify-center rounded-full overflow-hidden opacity-0 hover:opacity-60 transition-opacity duration-300 cursor-pointer">
                                    <i class="pi pi-pen-to-square rounded-full p-3 bg-surface-600 text-neutral-50"></i>
                                </div>
                                <img id="avatar" :src="imageURL" alt="Profile Image" class="w-full h-full rounded-full object-cover border-2 border-primary">
                            </div>
                            <input type="file" ref="fileInput" @change="handleFileChange" hidden />
                            <InputError :message="form.errors.avatar" />
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-col gap-1 flex-1">
                                <label for="name" class="font-bold text-xl capitalize">name</label>
                                <InputText id="name" v-model="form.name" placeholder="Enter your name..." />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="flex flex-col gap-1 flex-1">
                                <label for="email" class="font-bold text-xl capitalize">email</label>
                                <InputText id="email" v-model="form.email" placeholder="Enter your email..." />
                                <InputError :message="form.errors.email" />
                            </div>
                            <div class="flex flex-col gap-1 flex-1">
                                <label for="no_telpon" class="font-bold text-xl capitalize">nomor telpon</label>
                                <InputMask id="no_telpon" v-model="form.no_telpon" mask="9999 9999 9999 99" slot-char="" :auto-clear="false" placeholder="Enter your phone number..." />
                                <InputError :message="form.errors.no_telpon" />
                            </div>
                            <div class="flex flex-col gap-1 mt-4 flex-1">
                                <!-- <Button label="Save" type="submit" @click="submitForm()" /> -->
                                <Button label="Save" type="submit" />
                            </div>
                        </div>
                    </form>
                </Dialog>
            </div>

            
        </SettingsLayout>
    </main>
</template>
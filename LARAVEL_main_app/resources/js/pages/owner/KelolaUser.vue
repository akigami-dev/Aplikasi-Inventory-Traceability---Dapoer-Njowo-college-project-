<script setup lang="ts">
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head, router, useForm } from '@inertiajs/vue3';
import DataTable from 'volt-components/DataTable.vue';
import Column from 'primevue/column';
import { ref, watch } from 'vue';
import Select from 'volt-components/Select.vue';
import { useToast } from 'primevue/usetoast';
import addToast from '@/composables/addToast';
import { bodyScrollTopStore } from '@/composables/usePinia';
import Button from 'primevue/button';
import Dialog from 'volt-components/Dialog.vue';
import InputText from 'primevue/inputtext';
import InputMask from 'primevue/inputmask';
import { useConfirm } from 'primevue/useconfirm';
import InputError from '@/components/InputError.vue';
import { addConfirm } from '@/composables/Helper';

// bodyScrollTopStore().toggleScrollTop(false);
const confirm = useConfirm();
const toast = useToast();

const props = defineProps({
    ...useDefineProps("Settings"),
    DataUser: Object,
})

defineOptions({
    layout: useAppLayout()
})


const visibleDU = ref(false);
const actionDU = ref('create');

const role = ref([
    'staf produksi',
    'staf admin',
    'owner',
    // 'guest',
])

const form = useForm({
    id: null,
    name: '',
    email: '',
    no_telpon: '',
    password: '',
    password_confirmation: '',
    role: '',
    _method: 'post',
});

const searchValue = '';
const formRole = useForm({
    role: '',
});

const submitUser = () => {
    form.no_telpon = form.no_telpon.trim();
    const submitRoute = actionDU.value === 'create' ? 'owner.kelola_user.user.create' : 'owner.kelola_user.user.update';
    form.post(route(submitRoute, { id: form.id }), {
        onSuccess: (response) => {
            addToast(toast);
            visibleDU.value = false
        },
    })
};

const openEDU = (data) => {
    console.log(data);    
    form._method = 'put';
    form.id = data.id;
    form.name = data.name;
    form.email = data.email;
    form.no_telpon = data.no_telpon;
    form.role = data.role;
    visibleDU.value = true;
    actionDU.value = 'edit';
}

const popupDelete = (data) => {
    addConfirm(confirm, {
        icon: 'pi pi-trash',
        message: 'Apakah anda yakin ingin menghapus data ini?',
        accept: () => {
            router.delete(route('owner.kelola_user.user.delete', data.id), {
                onSuccess: () => {
                    addToast(toast);
                },
                onError: () => {
                    addToast(toast)
                },
            })
        },
    })
}

function onRoleChanged(role: string, id: any){
    formRole.role = role;

    formRole.put(route('owner.kelola_user.role.update', id), {
        onSuccess: () => {
            addToast(toast);
            console.log("LMAO");
        },
        onError: () => {
            addToast(toast)
        },
    })
}

function goToPage(url: string) {
    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
    })
}

function searching(){
    if(searchValue.length > 0){
        router.get(route('owner.aKelolaRole.search', searchValue))
    }
}

watch(visibleDU, (newValue, oldValue) => {
    if(newValue === false){
        form.reset();
        form.clearErrors();
    }
})
</script>
<template>
    {{ console.log("PAGINATION: ", DataUser.meta) }}
    <Head :title="props.title" />
    <main class="w-full">
        <!-- <div class="absolute inset-0 z-10 bg-surface-800 opacity-50 flex justify-center items-center" :hidden="!formRole.processing">
            <div class="flex flex-col items-center gap-2">
                <span class="text-white text-lg font-semibold">Loading</span>
                <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
            </div>
        </div> -->
        <div class="main-container border">
            <div class="flex flex-col gap-5 w-full">
                <DataTable v-if="DataUser?.data" :value="DataUser?.data" pt:table="min-w-200">
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold border-b-2 dark:border-surface-400">Data User</span>
                            <InputText v-model="searchValue" :onchange="searching()" hidden />
                            <Button label="Tambah Data User" @click="visibleDU = true; actionDU = 'create'" />
                        </div>
                    </template>
                    <Column class="max-w-[3rem]" field="avatar" header="Avatar">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <img :src="data.avatar" alt="image" class="w-1/3 aspect-square rounded-full object-cover border-2 border-primary" />
                            </div>
                        </template>
                    </Column>
                    <Column class="max-w-[4rem]" field="name" header="Name"></Column>
                    <Column class="max-w-[4rem]" field="email" header="Email"></Column>
                    <Column class="max-w-[4rem]" field="role" header="Role"></Column>
                    <Column class="max-w-[4rem]" hidden field="role" header="Role">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <Select :disabled="formRole.processing" v-model="data.role" :options="role" v-on:value-change="onRoleChanged(data.role, data.id)" placeholder="Select Role..." class="w-full md:w-56" />
                            </div>                            
                        </template>
                    </Column>
                    <Column class="max-w-[4rem]" header="action" >
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <Button icon="pi pi-pencil" size="small" severity="info" outlined rounded @click="openEDU(data)" />
                                <Button icon="pi pi-trash" size="small" severity="danger" outlined rounded @click="popupDelete(data)" />
                                <!-- <i class="pi pi-trash text-red-400 cursor-pointer" style="font-size: 1.2rem" @click="popupDelete(data)"></i> -->
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
            <div class="flex mt-4">
                <span v-if="DataUser?.meta" class="flex-1/2 text-md opacity-70">Showing page {{ DataUser?.meta.current_page }} to {{ DataUser?.meta.last_page }} of {{ DataUser?.meta.total }} results</span>
                <div class="flex-auto" v-for="(link, index) in DataUser?.meta.links" :key="index">
                    <!-- <button @click="goToPage(link.url)" v-html="link.label" :class="['cursor-pointer p-2 hover:bg-surface-700', Number.isInteger(parseInt(link.label)) ? 'w-10 rounded-full' : 'rounded', link.active ? 'bg-surface-700' : '']"></button> -->
                    <button @click="goToPage(link.url)" 
                    v-html="link.label" 
                    :class="['p-2',link.url ? 'cursor-pointer dark:hover:bg-surface-700 hover:bg-surface-300' : 'dark:text-surface-700 text-surface-300', Number.isInteger(parseInt(link.label)) ? 'w-10 rounded-full' : 'rounded', link.active ? 'bg-surface-300 dark:bg-surface-700' : '']" 
                    :disabled="link.url ? false : true"></button>
                </div>
            </div>
        </div>
    </main>

    <Dialog v-model:visible="visibleDU" :header="actionDU == 'edit' ? 'Edit Data User' : 'Tambah Data User'" modal :draggable="false" class="w-md">
        <form  @submit.prevent="submitUser">
            <div class="flex flex-col gap-2">
                <div class="flex flex-col gap-1">
                    <label for="name">Name</label>
                    <InputText v-model="form.name" type="name" class="w-full" id="name" required/>
                    <InputError :message="form.errors.name" />
                </div>
                <div class="flex flex-col gap-1">
                    <label for="email">Email</label>
                    <InputText v-model="form.email" type="email" class="w-full" id="email" required/>
                    <InputError :message="form.errors.email" />
                </div>
                <div class="flex flex-col gap-1">
                    <label for="role">Role</label>
                    <Select v-model="form.role" :options="role" placeholder="Select Role..." class="w-full md:w-56" />
                    <InputError :message="form.errors.role" />
                </div>
                <div class="flex flex-col gap-1">
                    <label for="no_telpon">Phone Number</label>
                    <InputMask v-model="form.no_telpon"  mask="9999 9999 9999 99" slot-char="" :auto-clear="false" class="w-full" id="no_telpon" required />
                    <InputError :message="form.errors.no_telpon" />
                </div>
                <div v-if="actionDU == 'create'" class="flex flex-col gap-1">
                    <label for="password">Password</label>
                    <InputText v-model="form.password" type="password" class="w-full" id="password" required/>
                    <InputError :message="form.errors.password" />
                </div>
                <div v-if="actionDU == 'create'" class="flex flex-col gap-1">
                    <label for="password_confirmation">Password Confirmation</label>
                    <InputText v-model="form.password_confirmation" type="password" class="w-full" id="password_confirmation" required/>
                    <InputError :message="form.errors.password_confirmation" />
                </div>
                <Button type="submit" class="w-full bg-primary hover:bg-primary-0 my-2 font-semibold" :disabled="form.processing">Save</Button>
            </div>
        </form>

    </Dialog>
</template>
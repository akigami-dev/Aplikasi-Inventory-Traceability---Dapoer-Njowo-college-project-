<script setup lang="ts">
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head, useForm } from '@inertiajs/vue3';
import Card from 'volt-components/Card.vue';
import SettingsLayout from '@/layouts/settings/settingsLayout.vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Password from 'primevue/password';
import InputError from '@/components/InputError.vue';
import { useToast } from 'primevue/usetoast';
import addToast from '@/composables/addToast';

const props = defineProps(useDefineProps("Settings"))

defineOptions({
    layout: useAppLayout()
})

const toast = useToast();

const passwordForm = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation : '',
})

const submitPassword = () => {
    passwordForm.post(route('settings.password.change_password'), {
        onSuccess: (response) => {
            if(response.props.toast) addToast(toast, response?.props?.toast);
            
            passwordForm.reset('current_password', 'new_password', 'new_password_confirmation');
        },
    });
}
</script>
<template>
    <Head :title="props.title" />
    <main>
        <SettingsLayout tabLabel="password">
            
            <h1 class="capitalize font-semibold text-xl border-b-1 border-surface py-1" >Change passwords</h1>
            <form class="flex flex-col gap-1 py-5" @submit.prevent="submitPassword">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <label for="current_password" class="capitalize min-w-[12rem] text-right">current password</label>
                        <Password v-model="passwordForm.current_password" required toggleMask :feedback="false" id="current_password" />
                    </div>
                    <div class="flex gap-2">
                        <label for="current_password" class="capitalize min-w-[12rem] text-right"></label>
                        <InputError :message="passwordForm.errors.current_password" />
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <label for="new_password" class="capitalize min-w-[12rem] text-right">new password</label>
                        <Password v-model="passwordForm.new_password" required toggleMask :feedback="false" id="new_password" />
                    </div>
                    <div class="flex gap-2">
                        <label for="new_password" class="capitalize min-w-[12rem] text-right"></label>
                        <InputError :message="passwordForm.errors.new_password" />
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <label for="new_password_confirmation " class="capitalize min-w-[12rem] text-right">confirm new password</label>
                        <Password v-model="passwordForm.new_password_confirmation" required toggleMask :feedback="false" id="new_password_confirmation " />
                    </div>
                    <div class="flex gap-2">
                        <label for="new_password_confirmation " class="capitalize min-w-[12rem] text-right"> </label>
                        <InputError :message="passwordForm.errors.new_password_confirmation " />
                    </div>
                </div>
                <div class="flex gap-2 items-center">
                    <label for="" class="capitalize min-w-[12rem] text-right"></label>
                    <Button label="Save" type="submit" size="small" class="w-fit flex gap-2 items-center" />
                </div>

            </form>
            
        </SettingsLayout>
        
    </main>
</template>
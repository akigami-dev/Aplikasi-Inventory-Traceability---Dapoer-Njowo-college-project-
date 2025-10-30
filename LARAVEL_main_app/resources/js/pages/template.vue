<script setup lang="ts">
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head } from '@inertiajs/vue3';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    ...useDefineProps("Template")
})

defineOptions({
    layout: useAppLayout()
})

const toast = useToast();
const confirm = useConfirm();

// VARIABLE SECTIONS

// FUNCTION SECTIONS

</script>
<template>
    <Head :title="props.title" />
    <main>
        <div class="main-container flex flex-col border dark:border-0">
            <div class="flex-1">
                <!-- Your Content -->
            </div>
        </div>
    </main>
</template>
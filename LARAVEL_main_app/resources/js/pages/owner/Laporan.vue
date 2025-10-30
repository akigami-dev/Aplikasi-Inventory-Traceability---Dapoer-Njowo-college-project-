<script setup lang="ts">
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import LayoutLaporan from './LayoutLaporan.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    ...useDefineProps("Template"),
    laporan: String,
    search: String,
    dataLaporan: Object,
})

defineOptions({
    layout: useAppLayout()
})

</script>
<template>
    <Head :title="props.title" />
    <LayoutLaporan>
    </LayoutLaporan>
</template>
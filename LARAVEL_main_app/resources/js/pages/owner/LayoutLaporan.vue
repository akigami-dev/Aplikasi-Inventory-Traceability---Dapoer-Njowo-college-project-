<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import SelectButton from 'primevue/selectbutton';
import Divider from 'volt-components/Divider.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    selectedLaporan: String,
})

// VARIABLE SECTIONS
const selectedLaporan = ref(props.selectedLaporan ?? '');
const optionsLaporan = ref(['Produksi', 'Distribusi', 'Restok Bahan Baku', 'Lot Bahan Baku',  'Recall Produk']);

// FUNCTION SECTIONS
watch(selectedLaporan, (newValue, oldValue) => {
    if(newValue !== oldValue && newValue !== ''){
        router.get(route(`owner.laporan`), {laporan: newValue});
    }
})

</script>
<template>
    <main>
        <div class="main-container flex flex-col border dark:border-0 gap-4">
            <div class="flex-1 flex flex-col gap-2">
                <span class="text-2xl font-bold p-1">Pilih Laporan</span>
                <SelectButton v-model="selectedLaporan" :options="optionsLaporan" class="capitalize"></SelectButton>
            </div>
            <div class="border-b border-surface-200 dark:border-surface-700"></div>
            <div class="flex-1">
                <slot/>
            </div>
        </div>
    </main>
</template>
<script setup lang="ts">
import { useLayout } from '@/composables/useLayout';
import { initializedThemeStore } from '@/composables/usePinia';
import { router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import html2pdf from 'html2pdf.js';

const props = defineProps({
    filename: String,
    route: String,
    params: Object,
});

const { isDarkMode, toggleDarkMode } = useLayout();
initializedThemeStore().initializeTheme();
// VARIABLE SECTIONS
const showDarkButton = ref(false);
const filename = props.filename ?? 'Document';

// FUNCTION SECTIONS
function toggleButton(){
    toggleDarkMode();
    toggleDarkButton();
}
function toggleDarkButton(){
    showDarkButton.value = !showDarkButton.value
}

function printPDF(){
    const element = document.getElementById('containerPDF');
    setTimeout(() => {
        html2pdf(element, {
            margin: 0,
            filename: `${filename}.pdf`,
            image: { type: 'jpeg', quality: 1 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'pt', format: 'a4', orientation: 'portrait' }
        });
    }, 500);

}

function goBack(){
    router.get(route(props.route as string), props.params ?? {});
}

onMounted(() => {
    printPDF();
});

</script>
<template>
    
    <div class="fixed top-0 right-0 transition-all duration-300 flex flex-col items-end gap-4 bg-surface-200 dark:bg-surface-600 border border-surface-400 dark:border-surface-500 py-5 px-3 rounded-bl">
        <button
            type="button"
            class="w-15 h-15 border-1 border-surface-300 dark:border-surface-800 bg-surface-100 hover:bg-surface-300 dark:bg-surface-900 dark:hover:bg-surface-700 cursor-pointer flex items-center justify-center rounded-full transition-all text-surface-900 dark:text-surface-0 focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-surface-0 dark:focus-visible:ring-offset-surface-950"
            @click="toggleButton"
        >
            <i style="font-size: 1.3rem" :class="['pi text-base', { 'pi-moon': isDarkMode, 'pi-sun': !isDarkMode }]" />
        </button>
        <span class="border-1 border-surface-300 dark:border-surface-800 bg-surface-100 hover:bg-surface-300 dark:bg-surface-900 dark:hover:bg-surface-700 cursor-pointer text-surface-900 dark:text-surface-0 p-2 rounded flex items-center gap-1"  size="small" @click="goBack"><i class="pi pi-arrow-left"></i> <span>Go Back</span></span>
        <div class="flex items-center gap-1 border-1 border-surface-300 dark:border-surface-800 bg-surface-100 hover:bg-surface-300 dark:bg-surface-900 dark:hover:bg-surface-700 cursor-pointer text-surface-900 dark:text-surface-0 p-2 rounded" @click="printPDF" data-html2canvas-ignore>
            <i class="pi pi-file"></i>
            <span>Download PDF</span>
        </div>
    </div>

    <div class="flex bg-surface-300 dark:bg-surface-700">
        <main id="containerPDF" class="w-[596pt] min-h-[841.5pt] h-fit main-container rounded-none">
            <slot/>
        </main>
    </div>
</template>
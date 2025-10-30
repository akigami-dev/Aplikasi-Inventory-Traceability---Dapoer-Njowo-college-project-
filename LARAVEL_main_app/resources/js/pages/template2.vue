<script setup lang="ts">
import { useDefineProps } from '@/composables/useAppLayout';
import { useLayout } from '@/composables/useLayout';
import { initializedThemeStore } from '@/composables/usePinia';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    ...useDefineProps('Login'),
    trace: Object,
});
const { isDarkMode, toggleDarkMode } = useLayout();
initializedThemeStore().initializeTheme();
// VARIABLE SECTIONS
const showDarkButton = ref(false);

// FUNCTION SECTIONS
function toggleButton(){
    toggleDarkMode();
    toggleDarkButton();
}
function toggleDarkButton(){
    showDarkButton.value = !showDarkButton.value
}

</script>
<template>
    <Head :title="props.title" />
    <div class="fixed -left-1 top-20">
        <div class="bg-surface-100 hover:bg-surface-300 dark:hover:bg-surface-700 dark:bg-surface-900 border border-surface shadow rounded-r-full py-2 px-1 flex items-center cursor-pointer" @click="toggleDarkButton">
            <i class="pi pi-chevron-right"></i>
        </div>
    </div>
    <div class="fixed transition-all duration-300 ease-in-out top-5" :class="{ '': showDarkButton, '-translate-x-full': !showDarkButton }">
        <button
            type="button"
            class="w-15 h-15 border-1 border-surface-300 dark:border-surface-800 bg-surface-100 hover:bg-surface-300 dark:bg-surface-900 dark:hover:bg-surface-700 cursor-pointer flex items-center justify-center rounded-full transition-all text-surface-900 dark:text-surface-0 focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-surface-0 dark:focus-visible:ring-offset-surface-950"
            @click="toggleButton"
        >
            <i style="font-size: 1.3rem" :class="['pi text-base', { 'pi-moon': isDarkMode, 'pi-sun': !isDarkMode }]" />
        </button>
    </div>
    <main class="min-w-screen min-h-screen main-container rounded-none">
        <div class="flex flex-col">
            <div class="flex-1">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempora ipsa culpa tenetur mollitia accusantium alias rem dicta dolorem, dolorum fuga?
            </div>
        </div>
    </main>
</template>
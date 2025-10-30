<script setup lang="ts">
import Button from "volt-components/Button.vue";
import { useLayout } from "@/composables/useLayout";
import AppConfig from "@/layouts/AppConfig.vue";

const { isDarkMode, toggleDarkMode } = useLayout();
</script>

<template>
    <div
        class="px-6 py-4 main-wrapper border-b-1 border-primary-300 dark:border-surface w-full bg-primary-300 dark:bg-surface-900 shadow-xl drop-shadow-md"
    >
        <div class="flex justify-between items-center">
            <div class="flex gap-3 items-center">
                <div class="mr-2">
                    <slot />
                </div>
                <span class="hidden sm:flex flex-col gap-1"
                    ><span class="text-xl font-light text-surface-0 dark:text-surface-100 leading-none"
                        >Inventory Traceability</span
                    >
                    <span class="text-sm font-medium text-primary-100 leading-tight"
                        >Dapoer Njowo</span
                    ></span
                >
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    class="w-10 h-10 cursor-pointer flex items-center justify-center rounded-full hover:bg-surface-100 dark:hover:bg-surface-800 transition-all text-surface-900 dark:text-surface-0 focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-surface-0 dark:focus-visible:ring-offset-surface-950"
                    @click="toggleDarkMode"
                >
                    <i :class="['pi text-base', { 'pi-moon': isDarkMode, 'pi-sun': !isDarkMode }]" />
                </button>
                <div class="relative">
                    <Button
                        v-styleclass="{
                            selector: '@next',
                            enterFromClass: 'hidden',
                            enterActiveClass: 'animate-scalein',
                            leaveToClass: 'hidden',
                            leaveActiveClass: 'animate-fadeout',
                            hideOnOutsideClick: true,
                        }"
                        icon="pi pi-palette"
                        text
                        rounded
                        aria-label="Settings"
                    />
                    <AppConfig />
                </div>
            </div>
        </div>
    </div>
</template>

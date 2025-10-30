<script setup lang="ts">
import AppSidebar from '@/layouts/AppSidebar.vue';
import AppTopbar from '@/layouts/AppTopbar.vue';
import AppContent from '@/layouts/AppContent.vue';
import AppFooter from './AppFooter.vue';
import Button from 'volt-components/Button.vue';
import { onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { bodyScrollTopStore, initializedThemeStore, sidebarStore } from '@/composables/usePinia';
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';

interface Props {
    sidebarItems: Array<any>,
    user: object,
    currentPage: string,
}
const props = defineProps<Props>()

// initialize theme
const initializedTheme = initializedThemeStore();
initializedTheme.initializeTheme();

// sidebar configuration
const sidebar = sidebarStore()
const isSidebarOpen = ref(sidebar.sidebarOpen)
function toggleSidebar() {
    sidebar.toggleSidebar();
    isSidebarOpen.value = sidebar.sidebarOpen;
}

// body scroll top configuration
const bodyScrollTop = bodyScrollTopStore()
onMounted(() => {
  router.on('navigate', () => {
    if (bodyScrollTop.isScrollTop){
        requestAnimationFrame(() => {
          window.scrollTo({ top: 0, behavior: 'auto' })
        })
    }
  })
})
</script>

<template>
    <div class="bg-surface-50 dark:bg-surface-950 min-h-screen main-wrapper">
        <div class="sticky top-0 z-10">
            <AppTopbar>
                <Button @click="toggleSidebar" icon="pi pi-bars"></Button>
            </AppTopbar>
        </div>
        <div :class="['flex pt-2']">
            <div :class="['default-transition',isSidebarOpen ? 'tablet:w-80 phone:w-0 mx-1' : 'w-0 mx-0']">
                <div :class="['h-[calc(100vh-100px)] fixed tablet:w-80 phone:w-screen default-transition', isSidebarOpen ? 'translate-x-0' : '-translate-x-full']">
                    <AppSidebar :sidebarItems="sidebarItems" :currentPage="currentPage" :user="user" />
                </div>
            </div>
            <div class="flex-1 mx-2">
                <AppContent>
                    <Toast />
                    <ConfirmDialog group="main">
                        <template #container="{message, acceptCallback, rejectCallback}">
                            <div class="flex flex-col items-center p-8 bg-surface-0 dark:bg-surface-900 rounded-full">
                                <div class="rounded-full text-primary-contrast inline-flex justify-center items-center h-24 w-24 -mt-20" :class="message.class ?? 'bg-primary'">
                                    <i :class="[message.icon]" class="!text-4xl"></i>
                                </div>
                                <span class="font-bold text-2xl block mb-2 mt-6">{{ message.header }}</span>
                                <p class="mb-0">{{ message.message }}</p>
                                <div class="flex items-center gap-2 mt-6">
                                    <Button :label="message.acceptProps.label ?? 'Ok'" @click="acceptCallback"></Button>
                                    <Button :class="message.rejectProps.class ?? ''" :label="message.rejectProps.label ?? 'Cancel'" outlined @click="rejectCallback"></Button>
                                </div>
                            </div>
                        </template>
                    </ConfirmDialog>
                    <slot :user="user" />
                    <AppFooter />
                </AppContent>
            </div>
        </div>
    </div>
</template>
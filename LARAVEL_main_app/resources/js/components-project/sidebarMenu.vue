<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import sidebarMenu from '@/components-project/sidebarMenu.vue';

const props = defineProps({
    items: Object,
    defaultIcon: String,
    currentPage: String,
});

</script>

<template>
    <!-- {{ console.log("Items: ", props.items) }} -->
    <!-- <div v-for="(item, index) in props.items" :key="index"> -->
        <!-- {{ console.log(item) }} -->
        <!-- <ul v-for="(item, index) in props.items" :key="index" 
        
        :class="['list-none py-0 pl-3 pr-0 m-0 transition-all duration-[400ms] ease-in-out hidden overflow-y-hidden']" >
        {{ item.label }} -->
        <li v-for="(item, index) in props.items" :key="index" class="py-[1px]">
                <Link v-if="!item.items" :class="['flex items-center cursor-pointer py-2 rounded text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800 duration-150 transition-colors pl-3', route(item.url) == props.currentPage  ? 'bg-surface-100 dark:bg-surface-800' : '']" :href="route(item.url)" preserve-scroll  preserve-state>
                    <i :class="['pi mr-2', item.icon || props.defaultIcon]"></i>
                    <span v-if="item.label" class="font-medium text-sm">{{ item.label }}</span>
                </Link>
                <div v-else>
                    <a
                        v-ripple
                        v-styleclass="{
                            selector: '@next',
                            enterFromClass: 'hidden',
                            enterActiveClass: 'animate-slidedown',
                            leaveToClass: 'hidden',
                            leaveActiveClass: 'animate-slideup'
                        }"
                        :class="['flex items-center cursor-pointer py-2 pr-4 rounded text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800 duration-150 transition-colors p-ripple pl-3']"
                    >
                        <i :class="['pi mr-2', item.icon ? item.icon : defaultIcon]"></i>
                        <span v-if="item.label" class="font-medium text-sm flex-1">{{ item.label }}</span>
                        <i class="pi pi-chevron-down"></i>
                    </a>
                    <ul :class="['list-none py-0 pl-3 pr-0 m-0 transition-all duration-[400ms] ease-in-out hidden overflow-y-hidden']">
                        <sidebarMenu :items="item.items" :defaultIcon="props.defaultIcon" :currentPage="props.currentPage" />
                    </ul>
                </div>
            </li>
        <!-- </ul> -->
    <!-- </div> -->
</template>
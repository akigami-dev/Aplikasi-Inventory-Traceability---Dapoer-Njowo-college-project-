<script setup lang="ts">
import {Link} from '@inertiajs/vue3';

defineProps({
    tabLabel: String,
})

const tab = [
    {
        label: 'profile',
        route: 'profile' 
    },
    {
        label: 'password',
        route: 'password' 
    },
]
</script>
<template>
    <main class="flex">
        <div class="w-1/6 px-3">
            <ul class="list-none"  v-for="(i, index) in tab" :key="index">
                <Link :href="route('settings.'+i.route)" preserve-scroll >
                    <li :class="['py-3 px-2 my-4 hover:dark:bg-surface-800 hover:bg-surface-200 rounded', i.label.toLowerCase() == tabLabel?.toLowerCase() ? 'bg-surface-200 dark:bg-surface-800' : '']">
                        <span class="font-medium text-sm uppercase">{{ i.label }}</span>
                    </li>
                </Link>
            </ul>     
        </div>
        <div class="flex-1">
            <div class="main-wrapper border rounded-lg p-4 shadow-xl">
                <slot />
            </div>
        </div>
    </main>
</template>
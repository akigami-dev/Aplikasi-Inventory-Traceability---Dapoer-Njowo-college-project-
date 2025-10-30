<script setup lang="ts">
import Accordion from 'primevue/accordion';
import AccordionContent from 'primevue/accordioncontent';
import AccordionPanel from 'primevue/accordionpanel';
import Button from 'primevue/button';
import { ref } from 'vue';

const activeAccordion = ref('false');
</script>

<template>
    <div>
        <div class="text-surface-500 dark:text-surface-400 px-1 cursor-pointer flex items-center gap-2 max-w-fit border-b-2 mb-2" @click="activeAccordion = activeAccordion === 'true' ? 'false' : 'true'">
            <h3 class="font-medium">Filter</h3>
            <i class="transition-all duration-300 pi pi-filter" :class="activeAccordion === 'true' ? 'rotate-180' : ''"></i>
        </div>
        <Accordion v-model:value="activeAccordion">
            <AccordionPanel
                class="!border-none" :value="'true'" 
            >
                <AccordionContent
                    class=""
                    :pt="
                    {
                        root: '!p-0',
                        content: { class: '!p-0' }
                    }"
                >
                    <div class="flex flex-wrap gap-6 p-3 main-container border bg-surface dark:bg-surface-800">
                        <slot/>
                        <!-- <Button label="Filter" size="small" /> -->
                    </div>
                </AccordionContent>
            </AccordionPanel>
        </Accordion>
    </div>
</template>
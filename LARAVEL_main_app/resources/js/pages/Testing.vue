<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import Badge from 'volt-components/Badge.vue';
import html2pdf from 'html2pdf.js';

const props = defineProps(useDefineProps("Testing"))

defineOptions({
    layout: useAppLayout()
})

async function printPDF(){
    const element = await document.getElementById('containerPDF');
    html2pdf(element);
}
</script>

<template>
    {{ console.log(props.title) }}
    <Head :title="props.title" />
    
    <main class="main-container">
        <Badge class="flex items-center gap-1 cursor-pointer" @click="printPDF">
            <i class="pi pi-file"></i>
            <span>PDF</span>
        </Badge>
        <div class="flex flex-col gap-6 main-container" id="containerPDF">
            <div class="">
                <span class="font-medium text-2xl border-b-2 border-surface w-fit py-2">PRODUCT</span>
            </div>
            <div class="flex gap-4">
                <div class="flex-1 flex flex-col gap-2">
                    <div class="flex gap-2">
                        <span class="font-medium text-primary">Nama Produk:</span>
                        <span class="font-light">Kunyit Asam</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-medium text-primary">Nama Produk:</span>
                        <span class="font-light">Kunyit Asam</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-medium text-primary">Nama Produk:</span>
                        <span class="font-light">Kunyit Asam</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-medium text-primary">Nama Produk:</span>
                        <span class="font-light">Kunyit Asam</span>
                    </div>
                </div>
                <div class="flex-1 flex flex-col gap-2">
                    <div class="flex gap-2">
                        <span class="font-medium text-primary">Nama Produk:</span>
                        <span class="font-light">Kunyit Asam</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-medium text-primary">Nama Produk:</span>
                        <span class="font-light">Kunyit Asam</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-medium text-primary">Nama Produk:</span>
                        <span class="font-light">Kunyit Asam</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-medium text-primary">Nama Produk:</span>
                        <span class="font-light">Kunyit Asam</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
</template>
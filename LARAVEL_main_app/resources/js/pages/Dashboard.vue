<script setup lang="ts">
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head } from '@inertiajs/vue3';
import Chart from 'primevue/chart';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref } from 'vue';

const props = defineProps({
    ...useDefineProps("Template"),
    data: Object,
})

defineOptions({
    layout: useAppLayout()
})

const toast = useToast();
const confirm = useConfirm();

// VARIABLE SECTIONS
const chartData = ref();
const chartOptions = ref();

const setChartData = () => {
    const documentStyle = getComputedStyle(document.documentElement);

    return {
        labels: props.data?.labels,
        datasets: [
            {
                label: 'Jumlah produksi',
                backgroundColor: documentStyle.getPropertyValue('--p-cyan-500'),
                borderColor: documentStyle.getPropertyValue('--p-cyan-500'),
                data: props.data?.produksi
            },
            {
                label: 'Jumlah Distribusi - Recall',
                backgroundColor: documentStyle.getPropertyValue('--p-gray-500'),
                borderColor: documentStyle.getPropertyValue('--p-gray-500'),
                data: props.data?.distribusi
            },
            {
                label: 'Jumlah Recall Produk',
                backgroundColor: documentStyle.getPropertyValue('--p-primary-500'),
                borderColor: documentStyle.getPropertyValue('--p-primary-500'),
                data: props.data?.recall
            }
        ]
    };
};

const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--p-text-muted-color');
    const surfaceBorder = documentStyle.getPropertyValue('--p-content-border-color');

    return {
        maintainAspectRatio: false,
        aspectRatio: 0.8,
        plugins: {
            legend: {
                labels: {
                    color: textColor
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: textColorSecondary,
                    font: {
                        weight: 500
                    }
                },
                grid: {
                    display: false,
                    drawBorder: false
                }
            },
            y: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder,
                    drawBorder: false
                }
            }
        }
    };
}

onMounted(() => {
    chartData.value = setChartData();
    chartOptions.value = setChartOptions();
});
// FUNCTION SECTIONS


</script>
<template>
    <Head :title="props.title" />
    <main>
        <div class="main-container flex flex-col border dark:border-0">
            <div class="flex-1">
                <span class="font-medium text-2xl border-b-2 border-surface w-fit py-2">Dashboard</span>
            </div>
            <div class="flex-1 flex flex-col py-10 gap-5">
                <span class="font-medium text-sm w-fit">Data Statistik Produk</span>
                <Chart type="bar" :data="chartData" :options="chartOptions" class="h-[30rem]"  />
                <div class="flex justify-center items-center h-full py-10 text-2xl font-bold">
                </div>
            </div>
        </div>
    </main>
</template>
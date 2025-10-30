<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import Divider from 'volt-components/Divider.vue';
import Menu from 'volt-components/Menu.vue';
import Badge from 'volt-components/Badge.vue';
import sidebarMenu from '@/components-project/sidebarMenu.vue';

import { ref } from 'vue';

const defaultIcon = "pi-th-large";

interface Props {
    sidebarItems: Array<any>,
    user: { name?: string; role?: string },
    currentPage: string,
}
const prop = defineProps<Props>()


const op = ref();
const items = ref([
    {
        label: 'Profile',
        items: [
            {
                label: 'Logout',
                icon: 'pi-sign-out',
                command: ()=> {
                    router.post(route("logout"))
                }
            },
            {
                label: 'Settings',
                icon: 'pi-cog',
                command: ()=> {
                    router.get(route("settings"))
                }
            },
        ]
    },
]);
const toggle = (event: any) => {
    op.value.toggle(event);
};

</script>

<template>
    <div class="flex flex-col h-full text-sm border-1 rounded-lg border-surface-200 dark:border-surface-700 bg-surface-0 dark:bg-surface-950 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 shrink-0 border-b-[1px] border-surface-200 dark:border-surface-700 bg-primary-400 shadow">
            <span class="inline-flex items-center gap-2">
                <span class="h-10 flex items-center">
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
	 width="66" viewBox="0 0 500 500" enable-background="new 0 0 500 500" xml:space="preserve">
                        <path class="fill-primary-100" opacity="1.000000" stroke="none" 
                                d="
                            M134.002640,256.130981 
                                C126.720787,257.065430 119.305069,259.362396 112.183784,258.662689 
                                C95.134308,256.987549 80.094650,250.403076 67.508049,238.019608 
                                C57.792084,228.460464 51.302120,216.988785 45.407200,205.154434 
                                C42.454823,199.227371 41.478802,192.269394 39.938854,185.700333 
                                C38.769802,180.713440 38.122051,175.604324 37.242340,170.549576 
                                C37.697338,170.284180 38.152332,170.018768 38.607327,169.753357 
                                C39.675034,170.361511 40.971203,170.755417 41.774288,171.611740 
                                C48.183807,178.446182 56.226788,181.716232 65.383583,182.035889 
                                C81.170212,182.587006 96.967194,182.877792 112.762062,183.146637 
                                C128.253632,183.410294 142.128738,187.805893 152.647797,199.631973 
                                C159.538254,207.378540 166.358383,215.492264 164.980789,227.115173 
                                C164.885513,227.919037 164.974838,228.790741 165.198486,229.567505 
                                C165.314026,229.968842 165.913727,230.230774 166.292038,230.553467 
                                C172.487259,225.345627 178.605789,219.778336 185.169510,214.797745 
                                C192.476883,209.252884 199.929581,203.808716 207.784622,199.096954 
                                C220.624329,191.395187 234.916214,187.813202 249.510437,184.635880 
                                C264.052429,181.469925 278.556580,180.719910 292.857666,183.418167 
                                C309.626831,186.582108 325.254181,193.353760 339.497925,203.125580 
                                C355.405396,214.038788 369.000977,227.625031 383.658356,239.980759 
                                C390.382690,245.649170 399.362518,249.160995 407.862885,252.054626 
                                C416.056091,254.843719 424.896271,256.446136 433.549500,256.973328 
                                C442.584442,257.523682 451.766327,256.471954 460.825714,255.548859 
                                C467.770294,254.841248 474.624298,253.244827 481.518585,252.043701 
                                C481.868408,252.482239 482.218262,252.920761 482.568115,253.359299 
                                C481.630341,254.690002 480.983398,256.518616 479.704651,257.265533 
                                C474.083954,260.548492 468.427704,263.841888 462.512085,266.533051 
                                C451.418976,271.579590 440.219086,276.415924 428.905548,280.944794 
                                C421.393677,283.951813 413.646454,286.394775 405.935059,288.874023 
                                C392.234375,293.278992 378.557129,297.786194 364.725342,301.744751 
                                C353.706238,304.898315 342.489746,307.375641 331.327698,310.012604 
                                C318.926392,312.942200 306.525757,315.899078 294.045685,318.458191 
                                C289.202026,319.451416 284.145569,319.440552 279.179565,319.796600 
                                C269.625793,320.481689 259.991394,322.159271 250.530396,321.478912 
                                C238.994659,320.649384 227.171112,319.122498 216.258850,315.516388 
                                C202.714615,311.040436 190.324280,303.596832 181.143997,292.018768 
                                C176.310883,285.923279 171.587616,279.740723 166.436920,273.111176 
                                C159.308411,275.981018 153.811813,281.920349 148.615799,287.980255 
                                C138.794235,299.434753 132.632538,312.933746 127.726044,327.085541 
                                C127.316284,328.267426 125.561943,329.603607 124.302689,329.749512 
                                C121.052650,330.126068 117.729950,329.875641 113.387848,329.875641 
                                C115.263550,322.758026 116.495255,316.203674 118.732201,310.012543 
                                C122.613716,299.269836 126.842659,288.630280 131.465271,278.186218 
                                C134.124939,272.177124 137.844055,266.636963 141.493668,260.471710 
                                C143.816666,257.339905 145.732269,254.622086 147.647888,251.904266 
                                C147.308762,251.482880 146.969635,251.061508 146.630508,250.640121 
                                C142.421219,252.470398 138.211929,254.300690 134.002640,256.130981 
                            M142.595688,245.045166 
                                C144.881226,244.254852 147.166748,243.464539 149.452286,242.674225 
                                C149.388336,242.222824 149.324387,241.771423 149.260437,241.320023 
                                C145.753098,241.736786 142.245773,242.153534 137.866150,242.611053 
                                C134.653244,242.623367 131.440353,242.635681 127.910988,241.997360 
                                C125.275276,242.035385 122.639572,242.073410 119.177460,242.056458 
                                C117.503555,241.568237 115.828835,241.082794 114.155891,240.591248 
                                C109.240051,239.146881 104.324913,237.700104 99.409500,236.254257 
                                C99.176575,236.929871 98.943649,237.605499 98.710732,238.281113 
                                C103.872963,240.511627 108.885529,244.074768 114.230843,244.675079 
                                C123.323471,245.696243 132.625473,244.852997 142.595688,245.045166 
                            M218.002228,216.095016 
                                C224.421906,215.087860 230.833542,213.325790 237.263214,213.257355 
                                C246.056000,213.163742 254.919281,213.940582 263.645813,215.127487 
                                C271.119537,216.144012 278.637573,217.678772 285.780121,220.054321 
                                C293.244415,222.536850 300.283691,226.297211 307.508759,229.498978 
                                C304.841888,226.625504 301.948334,223.896118 298.642914,221.823700 
                                C293.981323,218.901016 289.276001,215.646515 284.121063,214.000931 
                                C275.064758,211.109924 265.762024,208.456528 256.365601,207.290909 
                                C242.244629,205.539230 228.457397,208.647110 214.225876,213.004196 
                                C209.042465,215.495316 202.390869,215.144913 198.453613,220.047806 
                                C198.700394,220.682678 198.947174,221.317551 199.193954,221.952423 
                                C205.178055,219.995224 211.162155,218.038025 218.002228,216.095016 
                            z"/>
                    </svg>
                </span>                
                <span class="font-bold text-xl text-primary-100">
                    Dapoer Njowo
                </span>
            </span>
        </div>
        <!-- <Divider  /> -->
        <div class="overflow-y-auto scrollbar-thin" scroll-region v-if="prop.sidebarItems">
            <!-- Categories -->
            <ul class="list-none px-4 pt-3 m-0" v-for="(items, index) in prop.sidebarItems" :key="index" >
                <li>
                    <div
                        class="py-2 flex items-center justify-between text-surface-500 dark:text-surface-400"
                    >
                        <span v-if="items.label" class="font-semibold text-xs uppercase">{{ items.label }}</span>
                    </div>
                    
                    <div v-if="items.items">
                        <ul class="list-none p-0 m-0">
                            <!-- {{ console.log(item) }} -->
                            <sidebarMenu :items="items.items" :currentPage="currentPage" :defaultIcon="defaultIcon" />
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        <div class="mt-auto">
            <hr class="mb-1 mx-3 border-t border-0 border-surface-200 dark:border-surface-700" />
            <div @click="toggle" class="flex items-center cursor-pointer px-5 py-3 gap-3 text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800 duration-150 transition-colors">
                <Menu ref="op" class="p-2 !min-w-[18%] !left-4 !-translate-y-4" :model="items" popup>
                    <!-- <template #start>
                        <span class="inline-flex items-center gap-1 px-2 py-2">
                            <svg width="35" height="40" viewBox="0 0 35 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-8">
                                <path
                                    d="M25.87 18.05L23.16 17.45L25.27 20.46V29.78L32.49 23.76V13.53L29.18 14.73L25.87 18.04V18.05ZM25.27 35.49L29.18 31.58V27.67L25.27 30.98V35.49ZM20.16 17.14H20.03H20.17H20.16ZM30.1 5.19L34.89 4.81L33.08 12.33L24.1 15.67L30.08 5.2L30.1 5.19ZM5.72 14.74L2.41 13.54V23.77L9.63 29.79V20.47L11.74 17.46L9.03 18.06L5.72 14.75V14.74ZM9.63 30.98L5.72 27.67V31.58L9.63 35.49V30.98ZM4.8 5.2L10.78 15.67L1.81 12.33L0 4.81L4.79 5.19L4.8 5.2ZM24.37 21.05V34.59L22.56 37.29L20.46 39.4H14.44L12.34 37.29L10.53 34.59V21.05L12.42 18.23L17.45 26.8L22.48 18.23L24.37 21.05ZM22.85 0L22.57 0.69L17.45 13.08L12.33 0.69L12.05 0H22.85Z"
                                    fill="var(--p-primary-color)"
                                />
                                <path
                                    d="M30.69 4.21L24.37 4.81L22.57 0.69L22.86 0H26.48L30.69 4.21ZM23.75 5.67L22.66 3.08L18.05 14.24V17.14H19.7H20.03H20.16H20.2L24.1 15.7L30.11 5.19L23.75 5.67ZM4.21002 4.21L10.53 4.81L12.33 0.69L12.05 0H8.43002L4.22002 4.21H4.21002ZM21.9 17.4L20.6 18.2H14.3L13 17.4L12.4 18.2L12.42 18.23L17.45 26.8L22.48 18.23L22.5 18.2L21.9 17.4ZM4.79002 5.19L10.8 15.7L14.7 17.14H14.74H15.2H16.85V14.24L12.24 3.09L11.15 5.68L4.79002 5.2V5.19Z"
                                    fill="var(--p-text-color)"
                                />
                            </svg>
                            <span class="text-xl font-semibold">PRIME<span class="text-primary">APP</span></span>
                        </span>
                    </template> -->
                    <template #submenulabel="{ item }">
                        <span class="text-primary font-bold">{{ item.label }}</span>
                    </template>
                    <template #item="{ item, props }">
                        <a class="flex items-center" v-bind="props.action">
                            <span :class="['pi',item.icon]" />
                            <span>{{ item.label }}</span>
                            <Badge v-if="item.badge" class="ml-auto" :value="item.badge" />
                            <span v-if="item.shortcut" class="ml-auto border border-surface rounded bg-emphasis text-muted-color text-xs p-1">{{ item.shortcut }}</span>
                        </a>
                    </template>
                </Menu>
                <img v-if="user.avatar" :src="user.avatar" alt="Avatar" class="w-1/5 aspect-square rounded-full object-cover border-2 border-primary">
                <div class="grid grid-cols-1">
                    <span v-if="user.name" class="font-bold text-lg">{{ user.name }}</span>
                    <Divider class="!my-1 !p-0 border-t-1 border-surface-300 dark:border-surface-800" />
                    <span v-if="user.role" class="text-md font-medium text-primary dark:text-primary-400 uppercase">{{ user.role }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
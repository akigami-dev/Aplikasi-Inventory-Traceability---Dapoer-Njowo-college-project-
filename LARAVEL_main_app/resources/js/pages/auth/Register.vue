<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { useLayout } from '@/composables/useLayout';
import { initializedThemeStore } from '@/composables/usePinia';
import { useForm, Head, Link } from '@inertiajs/vue3';
import InputText from 'volt-components/InputText.vue';
import InputMask from 'volt-components/InputMask.vue';
import Button from 'volt-components/Button.vue';
import { useDefineProps } from '@/composables/useAppLayout';

const props = defineProps(useDefineProps('Sign Up'));

const form = useForm({
    name: '',
    email: '',
    no_telpon: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.no_telpon = form.no_telpon.trim();
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const { isDarkMode, toggleDarkMode } = useLayout();
initializedThemeStore().initializeTheme();

</script>

<template>
    <Head :title="props.title" />
    <div class="fixed left-5 top-5">
        <button
            type="button"
            class="w-10 h-10 cursor-pointer flex items-center justify-center rounded-full hover:bg-surface-100 dark:hover:bg-surface-800 transition-all text-surface-900 dark:text-surface-0 focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-surface-0 dark:focus-visible:ring-offset-surface-950"
            @click="toggleDarkMode"
        >
            <i :class="['pi text-base', { 'pi-moon': isDarkMode, 'pi-sun': !isDarkMode }]" />
        </button>
    </div>
    <div class="min-h-screen flex flex-col items-center justify-center bg-primary-200 dark:bg-surface-950 text-surface-600 dark:text-surface-0 font-medium">
        <div class="tablet:w-md phone:w-xs main-wrapper rounded-lg drop-shadow-xl shadow-2xl">
            <div class="flex flex-col gap-3 items-center">
                <div class="p-2 rounded-b-xl bg-primary-100 dark:bg-primary-300 drop-shadow-md">
                    <div class="flex flex-col items-center">
                        <span class="h-10 flex items-center">
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                width="100" viewBox="0 0 500 500" enable-background="new 0 0 500 500" xml:space="preserve">
                                <path class="fill-primary-400 dark:fill-primary-100" opacity="1.000000" stroke="none" 
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
                        <span class="text-2xl font-bold text-primary-400 dark:text-primary-100">Dapoer Njowo</span>
                    </div>
                </div>
            </div>
            <!-- <div class="text-center text-2xl font-bold">
                Sign Up
                
            </div> -->
            <div class="py-5">
                <div class="card px-5">
                    <form  @submit.prevent="submit">
                        <div class="flex flex-col gap-2">
                            <div class="flex flex-col gap-1">
                                <label for="name">Name</label>
                                <InputText v-model="form.name" type="name" class="w-full" id="name" required/>
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label for="email">Email</label>
                                <InputText v-model="form.email" type="email" class="w-full" id="email" required/>
                                <InputError :message="form.errors.email" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label for="no_telpon">Phone Number</label>
                                <InputMask v-model="form.no_telpon"  mask="9999 9999 9999 99" slot-char="" :auto-clear="false" class="w-full" id="no_telpon" required />
                                <InputError :message="form.errors.no_telpon" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label for="password">Password</label>
                                <InputText v-model="form.password" type="password" class="w-full" id="password" required/>
                                <InputError :message="form.errors.password" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label for="password_confirmation">Password Confirmation</label>
                                <InputText v-model="form.password_confirmation" type="password" class="w-full" id="password_confirmation" required/>
                                <InputError :message="form.errors.password_confirmation" />
                            </div>
                            <Button type="submit" class="w-full bg-primary hover:bg-primary-0 my-2 font-semibold" :disabled="form.processing">Sign Up</Button>
                        </div>
                    </form>
                    <div class="w-full text-sm text-center mt-4">
                        Already have account?
                        <Link :href="route('login')" class="font-semibold text-primary hover:text-primary-300 hover:dark:text-primary-50"> 
                            Login
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
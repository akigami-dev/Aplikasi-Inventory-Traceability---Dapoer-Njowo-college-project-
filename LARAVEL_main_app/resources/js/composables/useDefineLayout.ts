import { usePage } from "@inertiajs/vue3";

export default function useDefineProps(){
    const page = usePage();
    const pageName = page.component;
    console.log("iMin : ", pageName)
    return {
        sidebarItems: Array,
        title: {
            type: String,
            default: pageName
        }
    }
}
import { usePage } from "@inertiajs/vue3";

export default function usePageLayout(sidebarItems: Array<any>){
    const page = usePage();
    const pageName = page.component;

    return {
        sidebarItems: sidebarItems,
        pageName: pageName
    }
}
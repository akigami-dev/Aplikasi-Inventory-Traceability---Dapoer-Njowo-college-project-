import AppLayout from "@/layouts/AppLayout.vue";
import { usePage } from "@inertiajs/vue3";
import { bodyScrollTopStore } from "./usePinia";

export function usePageLayout(){
    const page = usePage();
    const currentPage = page.props.ziggy.location;
    const user = page?.props?.auth?.user ?? null;
    const sidebarItems = page.props.sidebarItems;
    return {
        sidebarItems: sidebarItems,
        user: user,
        currentPage: currentPage,
    }
}

export default function useAppLayout(isScrollTop: boolean = true){
    // bodyScrollTopStore().toggleScrollTop(isScrollTop);
    console.log("calling PageLayout");
    return (h: any, page: any) => {
        return h(AppLayout, usePageLayout(), () => {
            bodyScrollTopStore().toggleScrollTop(isScrollTop);
            return page
        })
    }
}

export function useDefineProps(defaultTitle: string){
    return {
        title: {
            type: String,
            default: defaultTitle || "Halaman",
        },
    }
}

export function getProps(){
    return usePage().props;
}
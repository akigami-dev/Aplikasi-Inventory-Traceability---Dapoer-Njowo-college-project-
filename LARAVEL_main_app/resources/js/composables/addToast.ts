import { usePage } from '@inertiajs/vue3';

export interface toastInterface {
    severity: string,
    summary: string,
    detail: string,
    life: number,
}

export default function addToast(toast, toastData?: toastInterface | null){
    // const toast = useToast();
    const page = usePage();

    const toastProps = toastData || page.props.toast;

    if(!toastProps) return;

    toast.add({
        severity: toastProps.severity,
        summary: toastProps.summary,
        detail: toastProps.detail,
        life: toastProps.life,
    })
}
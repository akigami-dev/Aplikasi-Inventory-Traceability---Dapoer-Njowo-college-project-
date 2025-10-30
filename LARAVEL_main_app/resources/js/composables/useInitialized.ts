import { setAppState } from "./useLayout";
import NProgress from 'nprogress'   
import { router } from '@inertiajs/vue3'

export function useInitialized() { 
    const scrollbarConfig = ["scrollbar-thumb-primary-300", "dark:scrollbar-thumb-primary-600", "scrollbar-track-transparent", "scrollbar-thin"];

    // Add scrollbar classes to the html element
    htmlAddClass(scrollbarConfig);
    
    // Removing data-page
    // removingDataPage(); 

    // initializing local storage
    initializedLocalStorage();

    // Set app state
    setupColor();

    // setup progressbar
    setupProgressBar()
}

function htmlAddClass(classList: string[]) {
    classList.forEach((name) => {
        document.documentElement.classList.add(name);
    })
}

function bodyAddClass(classList: string[]) {
    classList.forEach((name) => {
        document.body.classList.add(name);
    })
}

function removingDataPage() {    
    document.querySelector('#app')?.removeAttribute('data-page');
}

function initializedLocalStorage(){
    initializedTheme();
    initializedPrimarySurfaceColor();
    initializedOpenSidebar();
}

function initializedTheme() {
    if(!localStorage.getItem("theme")){
        localStorage.setItem("theme", "light");
    }
}

function initializedPrimarySurfaceColor(){
    const primaryColor = localStorage.getItem("primaryColor") || "orange";
    const surfaceColor = localStorage.getItem("surfaceColor") || "neutral";
    localStorage.setItem("primaryColor", primaryColor);
    localStorage.setItem("surfaceColor", surfaceColor);
}

function initializedOpenSidebar(){
    const isOpen = localStorage.getItem("sidebarOpen") || true;
    localStorage.setItem("sidebarOpen", isOpen.toString());
}

function setupColor(){
    setAppState();
    
}

function setupProgressBar(){
    router.on('start', () => NProgress.start())
    router.on('finish', () => NProgress.done())
    NProgress.configure({
        showSpinner: true,
    })
}
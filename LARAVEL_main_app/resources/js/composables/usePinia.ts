import { defineStore } from "pinia";
import { ref } from "vue";

export const bodyScrollTopStore = defineStore("bodyScrollTop", () =>{
    const isScrollTop = ref(true);

    function toggleScrollTop(value: boolean = true) {
        isScrollTop.value = value;
    }

    return {isScrollTop, toggleScrollTop}
})

export const initializedThemeStore = defineStore("initializedTheme", () => {
    const defaultTheme = ref(localStorage.getItem("theme"));
    const primaryColor = ref(localStorage.getItem("primaryColor"));
    const surfaceColor = ref(localStorage.getItem("surfaceColor"));

    function setThemeLS(theme: string = "light") {
        localStorage.setItem("theme", theme);
        defaultTheme.value = localStorage.getItem("theme");
    }

    function initializeTheme() {
        if(defaultTheme.value === "dark"){
            document.documentElement.classList.add("dark");
        }else{
            document.documentElement.classList.remove("dark");
        }
    }
    
    function setPrimaryColor(color: string) {
        localStorage.setItem("primaryColor", color);
    }

    function setSurfaceColor(color: string) {
        localStorage.setItem("surfaceColor", color);
    }

    return { defaultTheme, setThemeLS, initializeTheme, setPrimaryColor, setSurfaceColor, primaryColor, surfaceColor };
})

export const sidebarStore = defineStore("sidebar", () => {
    const sidebarOpen = ref(localStorage.getItem("sidebarOpen") === "true" ? true : false);

    function toggleSidebar(value: boolean = !sidebarOpen.value) {
        sidebarOpen.value = value;
        localStorage.setItem("sidebarOpen", value.toString());
    }

    return { sidebarOpen, toggleSidebar };
})
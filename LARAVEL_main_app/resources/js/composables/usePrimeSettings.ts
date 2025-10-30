import Aura from '@primeuix/themes/aura';
import {definePreset} from '@primeuix/themes';


const primary ={
    50: "#fef9f3",
    100: "#fae1c4",
    200: "#f7c995",
    300: "#f3b266",
    400: "#f09a38",
    500: "#ec8209",
    600: "#c96f08",
    700: "#a55b06",
    800: "#824805",
    900: "#5e3404",
    950: "#3b2102",
};  

const surface = {
    0: "#ffffff",
    50: "#f7f7f7",
    100: "#dadada",
    200: "#bcbcbc",
    300: "#9e9e9e",
    400: "#818181",
    500: "#636363",
    600: "#545454",
    700: "#454545",
    800: "#363636",
    900: "#282828",
    950: "#191919",
}

const test = definePreset(Aura, {
    semantic: {
        primary: {
            50: `${primary[50]}`,
            100: `${primary[100]}`,
            200: `${primary[200]}`,
            300: `${primary[300]}`,
            400: `${primary[400]}`,
            500: `${primary[500]}`,
            600: `${primary[600]}`,
            700: `${primary[700]}`,
            800: `${primary[800]}`,
            900: `${primary[900]}`,
            950: `${primary[950]}`
        },
        colorScheme: {
            dark: {
                surface: {
                    50: `${surface[50]}`,
                    100: `${surface[100]}`,
                    200: `${surface[200]}`,
                    300: `${surface[300]}`,
                    400: `${surface[400]}`,
                    500: `${surface[500]}`,
                    600: `${surface[600]}`,
                    700: `${surface[700]}`,
                    800: `${surface[800]}`,
                    900: `${surface[900]}`,
                    950: `${surface[950]}`
                },
            },
            light: {
                surface: {
                    50: `${surface[50]}`,
                    100: `${surface[100]}`,
                    200: `${surface[200]}`,
                    300: `${surface[300]}`,
                    400: `${surface[400]}`,
                    500: `${surface[500]}`,
                    600: `${surface[600]}`,
                    700: `${surface[700]}`,
                    800: `${surface[800]}`,
                    900: `${surface[900]}`,
                    950: `${surface[950]}`
                },
            }
        }
    }
});

export const theme = {
    theme : {
        preset: Aura,
        options : {
            darkModeSelector: '.dark',
        },
        unstyled : true,
    }
}





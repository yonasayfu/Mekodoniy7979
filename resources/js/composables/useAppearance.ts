import { useLocalStorage } from '@vueuse/core';
import { onMounted, watch } from 'vue';

const isDarkMode = useLocalStorage('isDarkMode', false);

export function useAppearance() {
    onMounted(() => {
        updateHtmlClass();
    });

    watch(isDarkMode, () => {
        updateHtmlClass();
    });

    function updateHtmlClass() {
        if (isDarkMode.value) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }

    return {
        isDarkMode,
    };
}

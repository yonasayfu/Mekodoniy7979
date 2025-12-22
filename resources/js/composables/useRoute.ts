import { getCurrentInstance } from 'vue';

export function useRoute() {
    const instance = getCurrentInstance();
    if (!instance) {
        throw new Error('useRoute must be called within a component setup function.');
    }
    return instance.appContext.config.globalProperties.route;
}

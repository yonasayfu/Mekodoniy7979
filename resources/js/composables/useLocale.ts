import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useLocale() {
    const page = usePage();

    const locale = computed(() => page.props.value.locale ?? 'en');
    const direction = computed(() => page.props.value.direction ?? 'ltr');
    const translations = computed(() => page.props.value.translations ?? {});

    return { locale, direction, translations };
}

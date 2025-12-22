import { ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

export function useTableFilters(
    routeName: string,
    filters: Record<string, any> = {},
) {
    const page = usePage();
    const defaultFilters = {
        search: page.props.filters?.search || '',
    };
    const filter = ref({ ...defaultFilters, ...filters });

    watch(
        filter,
        debounce(() => {
            router.get(
                route(routeName),
                { ...filter.value },
                {
                    preserveState: true,
                    replace: true,
                    preserveScroll: true,
                },
            );
        }, 300),
        { deep: true },
    );

    return {
        filter,
    };
}

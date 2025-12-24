import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type Direction = 'asc' | 'desc';

interface TableFilterOptions {
    route: string;
    initial?: {
        search?: string;
        sort?: string;
        direction?: Direction;
        per_page?: number;
        [key: string]: unknown;
    };
}

export function useTableFilters(options: TableFilterOptions) {
    const search = ref<string>(String(options.initial?.search ?? ''));
    const sort = ref<string>(String(options.initial?.sort ?? ''));
    const direction = ref<Direction>(
        (options.initial?.direction as Direction) ?? 'asc',
    );
    const perPage = ref<number>(Number(options.initial?.per_page ?? 10));

    const baseParams = computed<Record<string, unknown>>(() => {
        return {
            search: search.value || undefined,
            sort: sort.value || undefined,
            direction: sort.value ? direction.value : undefined,
            per_page: perPage.value,
        };
    });

    const apply = (extra: Record<string, unknown> = {}) => {
        router.get(
            options.route,
            {
                ...baseParams.value,
                ...extra,
            },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            },
        );
    };

    const toggleSort = (field: string) => {
        if (sort.value === field) {
            direction.value = direction.value === 'asc' ? 'desc' : 'asc';
        } else {
            sort.value = field;
            direction.value = 'asc';
        }

        apply();
    };

    return {
        search,
        sort,
        direction,
        perPage,
        apply,
        toggleSort,
    };
}

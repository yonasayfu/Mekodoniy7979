<template>
    <Head title="Edit Pledge" />
    <AppLayout
        :breadcrumbs="[
            { title: 'Sponsorships', href: '/sponsorships' },
            { title: 'Pledge #' + preSponsorship.id },
        ]"
    >
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.5em] text-slate-500">Guest pledge</p>
                    <h1 class="text-2xl font-bold">Edit pledge {{ preSponsorship.id }}</h1>
                </div>
                <Link
                    :href="route('sponsorships.index')"
                    class="text-sm font-semibold text-indigo-600 hover:text-indigo-500"
                >Back to sponsorships</Link>
            </div>
            <GlassCard>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.4em] text-slate-400">
                                Elder
                            </label>
                            <select
                                v-model.number="form.elder_id"
                                class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-300 focus:ring-0"
                            >
                                <option value="">Select elder</option>
                                <option v-for="elder in elders" :key="elder.id" :value="elder.id">
                                    {{ elder?.name ?? '—' }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.4em] text-slate-400">
                                Amount (ETB)
                            </label>
                            <input
                                v-model.number="form.amount"
                                type="number"
                                min="1"
                                class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-300 focus:ring-0"
                            />
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.4em] text-slate-400">
                                Currency
                            </label>
                            <input
                                v-model="form.currency"
                                type="text"
                                maxlength="3"
                                class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-300 focus:ring-0"
                            />
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.4em] text-slate-400">
                                Relationship
                            </label>
                            <select
                                v-model="form.relationship_type"
                                class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-300 focus:ring-0"
                            >
                                <option value="father">Father</option>
                                <option value="mother">Mother</option>
                                <option value="brother">Brother</option>
                                <option value="sister">Sister</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.4em] text-slate-400">
                            Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-300 focus:ring-0"
                        ></textarea>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <GlassButton type="submit" color="indigo">Save changes</GlassButton>
                        <GlassButton
                            as="button"
                            type="button"
                            color="danger"
                            @click="reject"
                        >Reject pledge</GlassButton>
                        <GlassButton
                            as="button"
                            type="button"
                            color="success"
                            :disabled="!form.elder_id"
                            @click="confirm"
                        >Confirm sponsorship</GlassButton>
                    </div>
                </form>
            </GlassCard>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed, reactive, watch } from 'vue';

type ElderOption = {
    id: number;
    name: string;
};

const props = defineProps<{
    preSponsorship?: {
        id: number;
        elder_id: number | null;
        amount: number | null;
        currency: string | null;
        relationship_type: string;
        notes: string | null;
        status: string;
    };
    elders: ElderOption[];
}>();

const preSponsorship = computed(() => ({
    id: props.preSponsorship?.id ?? 0,
    elder_id: props.preSponsorship?.elder_id ?? null,
    amount: props.preSponsorship?.amount ?? 0,
    currency: props.preSponsorship?.currency ?? 'ETB',
    relationship_type: props.preSponsorship?.relationship_type ?? 'father',
    notes: props.preSponsorship?.notes ?? '',
}));

const form = reactive({
    elder_id: preSponsorship.value.elder_id,
    amount: preSponsorship.value.amount,
    currency: preSponsorship.value.currency,
    relationship_type: preSponsorship.value.relationship_type,
    notes: preSponsorship.value.notes,
});

watch(
    () => preSponsorship.value,
    (value) => {
        form.elder_id = value.elder_id;
        form.amount = value.amount;
        form.currency = value.currency;
        form.relationship_type = value.relationship_type;
        form.notes = value.notes;
    },
    { deep: true, immediate: true },
);

const preSponsorshipId = computed(() => props.preSponsorship?.id ?? 0);

const submit = () => {
    if (!preSponsorshipId.value) {
        return;
    }

    router.put(route('pre-sponsorships.update', preSponsorshipId.value), form, {
        preserveScroll: true,
    });
};

const confirm = () => {
    if (!preSponsorshipId.value) {
        return;
    }

    router.post(route('pre-sponsorships.promote', preSponsorshipId.value), {}, {
        preserveScroll: true,
    });
};

const reject = () => {
    if (!preSponsorshipId.value) {
        return;
    }

    router.post(route('pre-sponsorships.reject', preSponsorshipId.value), {}, {
        preserveScroll: true,
    });
};
</script>

<script setup lang="ts">
import GlassCard from '@/components/GlassCard.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps<{
    donations: {
        data: any[];
    };
}>();
</script>

<template>
    <Head title="Donations Report" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Reports', href: '/reports' },
            { title: 'Donations Report' },
        ]"
    >
        <div class="space-y-6">
            <div>
                <h1
                    class="text-2xl font-semibold text-slate-900 dark:text-slate-100"
                >
                    Donations Report
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-300">
                    A detailed list of all donations.
                </p>
            </div>

            <GlassCard>
                <div class="p-6">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Donor</TableHead>
                                <TableHead>Elder</TableHead>
                                <TableHead>Amount</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Date</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="donation in donations.data"
                                :key="donation.id"
                            >
                                <TableCell>{{
                                    donation.user?.name ?? 'Guest'
                                }}</TableCell>
                                <TableCell>{{
                                    donation.elder?.name
                                }}</TableCell>
                                <TableCell
                                    >{{ donation.amount }}
                                    {{ donation.currency }}</TableCell
                                >
                                <TableCell>{{ donation.status }}</TableCell>
                                <TableCell>{{
                                    new Date(
                                        donation.created_at,
                                    ).toLocaleDateString()
                                }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </GlassCard>
        </div>
    </AppLayout>
</template>

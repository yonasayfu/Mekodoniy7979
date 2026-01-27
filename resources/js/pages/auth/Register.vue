<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useRoute } from '@/composables/useRoute';
import { Form, Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface BranchOption {
    id: number;
    name: string;
}

const props = defineProps<{
    branches: BranchOption[];
    defaultRole?: 'donor' | 'internal' | 'external';
}>();

const route = useRoute();

const staffRole = ref<'internal' | 'external'>(
    props.defaultRole && props.defaultRole !== 'donor'
        ? props.defaultRole as 'internal' | 'external'
        : 'internal',
);

const donorForm = useForm({
    name: '',
    phone_number: '',
    password: '',
    password_confirmation: '',
    email: '',
    role: 'donor',
});

const staffForm = useForm({
    name: '',
    branch_id: '',
    email: '',
    password: '',
    password_confirmation: '',
    address: '',
    city: '',
    country: '',
    phone_number: '',
    date_of_birth: '',
    gender: '',
    role: staffRole.value,
});

const staffRoleDescriptions: Record<'internal' | 'external', string> = {
    internal: 'Admins and staff who need branch, elder, and reconciliation access.',
    external: 'Guest-level donors who submit pledges with richer profile data.',
};

watch(staffRole, (value) => {
    staffForm.role = value;
});

const donorEmailFallback = (phone: string) => {
    const digits = phone.replace(/\D/g, '');
    return `guest+${digits || 'unknown'}@mekodonia.local`;
};

const submitDonor = () => {
    if (!donorForm.email) {
        donorForm.email = donorEmailFallback(donorForm.phone_number);
    }
    donorForm.post(route('register.store'));
};

const submitStaff = () => {
    staffForm.post(route('register.store'));
};
</script>

<template>
    <Head title="Register" />

    <div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
        <div class="mx-auto flex min-h-screen w-full max-w-6xl flex-col gap-10 px-6 py-10 lg:px-12">
            <div class="space-y-3 text-center sm:text-left">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-500">
                    Mekodonia Home Connect
                </p>
                <h1 class="text-3xl font-semibold leading-tight">Create an account</h1>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Choose the path that fits you—quick donor setup or the full staff onboarding.
                </p>
            </div>

            <div class="grid gap-8 lg:grid-cols-[1.4fr_1fr]">
                <Form
                    @submit.prevent="submitDonor"
                    :form="donorForm"
                    v-slot="{ errors: donorErrors, processing: donorProcessing }"
                    class="space-y-6 rounded-3xl border border-slate-200/70 bg-white/90 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/70 lg:min-h-[32rem]"
                >
                    <div class="space-y-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-500">
                            Donor quick signup
                        </p>
                        <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                            Sign in later with phone and password
                        </h2>
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            Provide the essentials now and we will text you login details once your donor account is ready.
                        </p>
                    </div>

                    <div class="grid gap-4">
                        <div class="grid gap-2">
                            <Label for="name">Full name</Label>
                            <Input
                                id="name"
                                type="text"
                                required
                                autocomplete="name"
                                :tabindex="1"
                                v-model="donorForm.name"
                                placeholder="Full name"
                            />
                            <InputError :message="donorErrors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="phone_number">Phone number</Label>
                            <Input
                                id="phone_number"
                                type="text"
                                required
                                autocomplete="tel"
                                :tabindex="2"
                                v-model="donorForm.phone_number"
                                placeholder="Phone number"
                            />
                            <InputError :message="donorErrors.phone_number" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password">Password</Label>
                            <Input
                                id="password"
                                type="password"
                                required
                                :tabindex="3"
                                autocomplete="new-password"
                                v-model="donorForm.password"
                                placeholder="Password"
                            />
                            <InputError :message="donorErrors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password_confirmation">Confirm password</Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                required
                                :tabindex="4"
                                autocomplete="new-password"
                                v-model="donorForm.password_confirmation"
                                placeholder="Confirm password"
                            />
                            <InputError :message="donorErrors.password_confirmation" />
                        </div>
                    </div>

                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        We automatically generate an email alias so you can sign in with the phone number you just provided.
                    </p>

                    <Button
                        type="submit"
                        class="w-full"
                        :disabled="donorProcessing"
                    >
                        <LoaderCircle
                            v-if="donorProcessing"
                            class="mr-2 h-4 w-4 animate-spin"
                        />
                        Create donor account
                    </Button>
                </Form>

                <Form
                    @submit.prevent="submitStaff"
                    :form="staffForm"
                    v-slot="{ errors: staffErrors, processing: staffProcessing }"
                    class="space-y-6 rounded-3xl border border-slate-200/70 bg-white/90 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/70 lg:min-h-[32rem]"
                >
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-600 dark:text-slate-400">
                                Internal & external onboarding
                            </p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                {{ staffRoleDescriptions[staffRole] }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                class="rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.35em] transition"
                                :class="staffRole === 'internal'
                                    ? 'border-emerald-500 bg-emerald-50 text-emerald-600 dark:border-emerald-400 dark:bg-emerald-500/10 dark:text-emerald-200'
                                    : 'border-slate-200 text-slate-600 hover:border-slate-400 dark:border-slate-700 dark:text-slate-300'"
                                @click.prevent="staffRole = 'internal'"
                            >
                                Internal staff
                            </button>
                            <button
                                type="button"
                                class="rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.35em] transition"
                                :class="staffRole === 'external'
                                    ? 'border-slate-900 bg-slate-900 text-white dark:border-white dark:bg-white dark:text-slate-900'
                                    : 'border-slate-200 text-slate-600 hover:border-slate-400 dark:border-slate-700 dark:text-slate-300'"
                                @click.prevent="staffRole = 'external'"
                            >
                                Guest donor
                            </button>
                        </div>
                    </div>

                    <div class="grid gap-4">
                        <div class="grid gap-2">
                            <Label for="branch_id">Branch</Label>
                            <select
                                id="branch_id"
                                required
                                :tabindex="5"
                                v-model="staffForm.branch_id"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:border-ring focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="" disabled>Select your branch</option>
                                <option
                                    v-for="branch in branches"
                                    :key="branch.id"
                                    :value="branch.id"
                                >
                                    {{ branch.name }}
                                </option>
                            </select>
                            <InputError :message="staffErrors.branch_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email address</Label>
                            <Input
                                id="email"
                                type="email"
                                required
                                :tabindex="6"
                                autocomplete="email"
                                v-model="staffForm.email"
                                placeholder="email@example.com"
                            />
                            <InputError :message="staffErrors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="address">Address</Label>
                            <Input
                                id="address"
                                type="text"
                                :tabindex="7"
                                autocomplete="street-address"
                                v-model="staffForm.address"
                                placeholder="Street address"
                            />
                            <InputError :message="staffErrors.address" />
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-4">
                            <div class="grid gap-2">
                                <Label for="city">City</Label>
                                <Input
                                    id="city"
                                    type="text"
                                    :tabindex="8"
                                    autocomplete="address-level2"
                                    v-model="staffForm.city"
                                    placeholder="City"
                                />
                                <InputError :message="staffErrors.city" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="country">Country</Label>
                                <Input
                                    id="country"
                                    type="text"
                                    :tabindex="9"
                                    autocomplete="country-name"
                                    v-model="staffForm.country"
                                    placeholder="Country"
                                />
                                <InputError :message="staffErrors.country" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="phone_number_staff">Phone number</Label>
                            <Input
                                id="phone_number_staff"
                                type="text"
                                :tabindex="10"
                                autocomplete="tel"
                                v-model="staffForm.phone_number"
                                placeholder="Phone number"
                            />
                            <InputError :message="staffErrors.phone_number" />
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-4">
                            <div class="grid gap-2">
                                <Label for="date_of_birth">Date of birth</Label>
                                <Input
                                    id="date_of_birth"
                                    type="date"
                                    :tabindex="11"
                                    v-model="staffForm.date_of_birth"
                                />
                                <InputError :message="staffErrors.date_of_birth" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="gender">Gender</Label>
                                <select
                                    id="gender"
                                    :tabindex="12"
                                    v-model="staffForm.gender"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:border-ring focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="">Select gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                <InputError :message="staffErrors.gender" />
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-4">
                            <div class="grid gap-2">
                                <Label for="staff-password">Password</Label>
                                <Input
                                    id="staff-password"
                                    type="password"
                                    required
                                    :tabindex="13"
                                    autocomplete="new-password"
                                    v-model="staffForm.password"
                                    placeholder="Password"
                                />
                                <InputError :message="staffErrors.password" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="staff-password_confirmation">Confirm password</Label>
                                <Input
                                    id="staff-password_confirmation"
                                    type="password"
                                    required
                                    maxlength="255"
                                    :tabindex="14"
                                    autocomplete="new-password"
                                    v-model="staffForm.password_confirmation"
                                    placeholder="Confirm password"
                                />
                                <InputError :message="staffErrors.password_confirmation" />
                            </div>
                        </div>
                    </div>

                    <Button
                        type="submit"
                        class="w-full"
                        :disabled="staffProcessing"
                    >
                        <LoaderCircle
                            v-if="staffProcessing"
                            class="mr-2 h-4 w-4 animate-spin"
                        />
                        Create account
                    </Button>
                </Form>
            </div>

            <div class="flex flex-col-reverse items-center justify-between gap-4 text-sm text-slate-500 dark:text-slate-400 lg:flex-row">
                <TextLink
                    :href="route('login', undefined, false)"
                    class="underline underline-offset-4"
                >
                    Already have an account? Log in
                </TextLink>
                <p>
                    Signing up for the donor card only fills the essentials so you can manage pledges swiftly.
                </p>
            </div>
        </div>
    </div>
</template>

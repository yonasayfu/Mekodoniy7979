<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useRoute } from '@/composables/useRoute';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
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

const form = RegisteredUserController.store.form();
const selectedRole = ref<'donor' | 'internal' | 'external'>(
    props.defaultRole ?? 'external',
);

const roleDescriptions: Record<
    'donor' | 'internal' | 'external',
    string
> = {
    donor: 'Use this to join as a donor, track your pledges, and manage receipts.',
    internal: 'For admins or staff who need branch, elder, and reconciliation access.',
    external: 'A basic guest donor account—use this unless you need admin privileges.',
};

const selectRole = (role: 'donor' | 'internal' | 'external') => {
    selectedRole.value = role;
};

form.setData('role', selectedRole.value);

watch(selectedRole, (value) => {
    form.setData('role', value);
});

const submit = () => {
    form.post(route('register.store'));
};
</script>

<template>
    <AuthBase
        title="Create an account"
        description="Enter your details below to create your account"
    >
        <Head title="Register" />

        <div class="rounded-2xl border border-slate-200/70 bg-white/70 p-4 text-sm text-slate-700 shadow-sm dark:border-slate-800 dark:bg-slate-900/60">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-500">
                Registering as
            </p>
            <div class="mt-2 flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    class="rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.35em] transition"
                    :class="selectedRole === 'donor'
                        ? 'border-indigo-500 bg-indigo-50 text-indigo-600 dark:border-indigo-400 dark:bg-indigo-500/10 dark:text-indigo-200'
                        : 'border-slate-200 text-slate-600 hover:border-slate-400 dark:border-slate-700 dark:text-slate-300'"
                    @click.prevent="selectRole('donor')"
                >
                    Donor
                </button>
                <button
                    type="button"
                    class="rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.35em] transition"
                    :class="selectedRole === 'internal'
                        ? 'border-emerald-500 bg-emerald-50 text-emerald-600 dark:border-emerald-400 dark:bg-emerald-500/10 dark:text-emerald-200'
                        : 'border-slate-200 text-slate-600 hover:border-slate-400 dark:border-slate-700 dark:text-slate-300'"
                    @click.prevent="selectRole('internal')"
                >
                    Internal staff
                </button>
                <button
                    type="button"
                    class="rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.35em] transition"
                    :class="selectedRole === 'external'
                        ? 'border-slate-900 bg-slate-900 text-white dark:border-white dark:bg-white dark:text-slate-900'
                        : 'border-slate-200 text-slate-600 hover:border-slate-400 dark:border-slate-700 dark:text-slate-300'"
                    @click.prevent="selectRole('external')"
                >
                    Guest donor
                </button>
            </div>
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                {{ roleDescriptions[selectedRole] }}
            </p>
        </div>

        <Form
            @submit.prevent="submit"
            :form="form"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <input type="hidden" name="role" :value="selectedRole" />
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        v-model="form.name"
                        placeholder="Full name"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="branch_id">Branch</Label>
                    <select
                        id="branch_id"
                        required
                        :tabindex="2"
                        v-model="form.branch_id"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
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
                    <InputError :message="form.errors.branch_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="3"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="5"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <div class="grid gap-2">
                    <Label for="address">Address</Label>
                    <Input
                        id="address"
                        type="text"
                        :tabindex="6"
                        autocomplete="street-address"
                        v-model="form.address"
                        placeholder="Street address"
                    />
                    <InputError :message="form.errors.address" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="city">City</Label>
                        <Input
                            id="city"
                            type="text"
                            :tabindex="7"
                            autocomplete="address-level2"
                            v-model="form.city"
                            placeholder="City"
                        />
                        <InputError :message="form.errors.city" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="country">Country</Label>
                        <Input
                            id="country"
                            type="text"
                            :tabindex="8"
                            autocomplete="country-name"
                            v-model="form.country"
                            placeholder="Country"
                        />
                        <InputError :message="form.errors.country" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="phone_number">Phone Number</Label>
                    <Input
                        id="phone_number"
                        type="text"
                        :tabindex="9"
                        autocomplete="tel"
                        v-model="form.phone_number"
                        placeholder="Phone number"
                    />
                    <InputError :message="form.errors.phone_number" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="date_of_birth">Date of Birth</Label>
                        <Input
                            id="date_of_birth"
                            type="date"
                            :tabindex="10"
                            v-model="form.date_of_birth"
                        />
                        <InputError :message="form.errors.date_of_birth" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="gender">Gender</Label>
                        <select
                            id="gender"
                            :tabindex="11"
                            v-model="form.gender"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <InputError :message="form.errors.gender" />
                    </div>
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    tabindex="12"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-4 w-4 animate-spin"
                    />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink
                    :href="route('login', undefined, false)"
                    class="underline underline-offset-4"
                    :tabindex="13"
                >
                    Log in
                </TextLink>
            </div>
        </Form>
    </AuthBase>
</template>

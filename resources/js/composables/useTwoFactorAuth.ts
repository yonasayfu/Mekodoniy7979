import { router, usePage } from '@inertiajs/vue3';
import http from '@/lib/http';
import { computed, ref } from 'vue';

export function useTwoFactorAuth() {
    const page = usePage();
    const user = computed(() => page.props.auth.user);

    const confirming = ref(false);
    const qrCode = ref(null);
    const setupKey = ref(null);
    const recoveryCodes = ref([]);
    const enabling = ref(false);
    const disabling = ref(false);

    const twoFactorEnabled = computed(
        () => !enabling.value && user.value?.two_factor_enabled,
    );

    const enableTwoFactorAuthentication = () => {
        enabling.value = true;

        http.post(route('two-factor.enable')).then(() => {
            Promise.all([
                showQrCode(),
                showSetupKey(),
                showRecoveryCodes(),
            ]).then(() => {
                confirming.value = true;
                enabling.value = false;
            });
        });
    };

    const confirmTwoFactorAuthentication = (confirmationForm) => {
        confirmationForm.post(route('two-factor.confirm'), {
            preserveScroll: true,
            onSuccess: () => {
                confirming.value = false;
                qrCode.value = null;
                setupKey.value = null;
            },
        });
    };

    const regenerateRecoveryCodes = () => {
        http
            .post(route('two-factor.recovery-codes'))
            .then(() => showRecoveryCodes());
    };

    const showQrCode = () => {
        return http.get(route('two-factor.qr-code')).then((response) => {
            qrCode.value = response.data.svg;
        });
    };

    const showSetupKey = () => {
        return http.get(route('two-factor.secret-key')).then((response) => {
            setupKey.value = response.data.secretKey;
        });
    };

    const showRecoveryCodes = () => {
        return http
            .get(route('two-factor.recovery-codes'))
            .then((response) => {
                recoveryCodes.value = response.data;
            });
    };

    const disableTwoFactorAuthentication = () => {
        disabling.value = true;
        router.delete(route('two-factor.disable'), {
            preserveScroll: true,
            onSuccess: () => {
                disabling.value = false;
                confirming.value = false;
            },
        });
    };

    return {
        confirming,
        qrCode,
        setupKey,
        recoveryCodes,
        enabling,
        disabling,
        twoFactorEnabled,
        enableTwoFactorAuthentication,
        confirmTwoFactorAuthentication,
        regenerateRecoveryCodes,
        showQrCode,
        showRecoveryCodes,
        disableTwoFactorAuthentication,
    };
}

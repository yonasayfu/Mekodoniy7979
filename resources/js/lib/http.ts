import axios from 'axios';
import { queueRequest } from '@/lib/offlineQueue';

declare global {
    interface Window {
        __APP_ONLINE__?: { value: boolean };
    }
}

const instance = axios.create();

instance.interceptors.response.use(
    (response) => response,
    async (error) => {
        const config = error.config;
        const isNetworkError = ! error.response;
        const onlineState = window.__APP_ONLINE__;

        if (
            isNetworkError &&
            config &&
            onlineState &&
            onlineState.value === false
        ) {
            await queueRequest({
                url: config.url ?? '/',
                method: config.method ?? 'get',
                headers: config.headers ?? {},
                body: config.data,
            });

            if ('serviceWorker' in navigator && 'SyncManager' in window) {
                navigator.serviceWorker.ready.then((registration) => {
                    registration.sync.register('offline-submission-sync');
                });
            }

            return new Promise(() => {});
        }

        return Promise.reject(error);
    },
);

export default instance;

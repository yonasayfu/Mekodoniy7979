import { openDB, type DBSchema, IDBPDatabase } from 'idb';

interface OfflineQueueDB extends DBSchema {
    'sync-queue': {
        key: number;
        value: {
            id?: number;
            url: string;
            method: string;
            headers: Record<string, string>;
            body: unknown;
            created_at: string;
        };
    };
}

const DB_NAME = 'offline-queue-db';
const STORE_NAME = 'sync-queue';
let dbPromise: Promise<IDBPDatabase<OfflineQueueDB>> | null = null;

async function getDb() {
    if (! dbPromise) {
        dbPromise = openDB<OfflineQueueDB>(DB_NAME, 1, {
            upgrade(db) {
                if (! db.objectStoreNames.contains(STORE_NAME)) {
                    db.createObjectStore(STORE_NAME, {
                        keyPath: 'id',
                        autoIncrement: true,
                    });
                }
            },
        });
    }

    return dbPromise;
}

export async function queueRequest(payload: Omit<OfflineQueueDB['sync-queue']['value'], 'created_at'>) {
    const db = await getDb();
    const headers = payload.headers ?? {};
    let body: BodyInit | null = null;

    if (payload.body instanceof FormData) {
        body = payload.body;
    } else if (typeof payload.body === 'string') {
        body = payload.body;
    } else if (payload.body !== undefined && payload.body !== null) {
        headers['Content-Type'] = headers['Content-Type'] ?? 'application/json';
        body = JSON.stringify(payload.body);
    }

    await db.add(STORE_NAME, {
        ...payload,
        headers,
        body,
        created_at: new Date().toISOString(),
    });
}

export async function drainQueue() {
    const db = await getDb();
    const tx = db.transaction(STORE_NAME, 'readwrite');
    const store = tx.store;
    const items = await store.getAll();

    for (const item of items) {
        try {
            const response = await fetch(item.url, {
                method: item.method,
                headers: item.headers,
                body: item.body as BodyInit | null,
            });

            if (! response.ok) {
                throw new Error(`Failed sync: ${response.statusText}`);
            }

            await store.delete(item.id!);
        } catch (error) {
            // stop draining to retry later
            throw error;
        }
    }

    await tx.done;
}

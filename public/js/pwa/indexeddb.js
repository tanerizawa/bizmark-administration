/**
 * Bizmark Mobile PWA - IndexedDB Manager
 * Version: 1.0.0
 * Date: April 14, 2026
 * 
 * Features:
 * - Unified IndexedDB interface for PWA
 * - Pending requests queue for offline sync
 * - Dashboard data caching
 * - Form draft storage
 */

const DB_NAME = 'bizmark-mobile-db';
const DB_VERSION = 1;

// Store names
const STORES = {
    PENDING_REQUESTS: 'pending-requests',
    DASHBOARD_CACHE: 'dashboard-cache',
    FORM_DRAFTS: 'form-drafts',
    USER_PREFERENCES: 'user-preferences'
};

let db = null;

/**
 * Initialize IndexedDB
 */
function initDB() {
    return new Promise((resolve, reject) => {
        if (db) {
            resolve(db);
            return;
        }

        const request = indexedDB.open(DB_NAME, DB_VERSION);

        request.onerror = (event) => {
            console.error('[IndexedDB] Error opening database:', event.target.error);
            reject(event.target.error);
        };

        request.onsuccess = (event) => {
            db = event.target.result;
            console.log('[IndexedDB] Database opened successfully');
            resolve(db);
        };

        request.onupgradeneeded = (event) => {
            const database = event.target.result;
            console.log('[IndexedDB] Upgrading database...');

            // Pending requests store (for offline sync)
            if (!database.objectStoreNames.contains(STORES.PENDING_REQUESTS)) {
                const pendingStore = database.createObjectStore(STORES.PENDING_REQUESTS, {
                    keyPath: 'id',
                    autoIncrement: true
                });
                pendingStore.createIndex('timestamp', 'timestamp', { unique: false });
                pendingStore.createIndex('type', 'type', { unique: false });
                pendingStore.createIndex('status', 'status', { unique: false });
            }

            // Dashboard cache store
            if (!database.objectStoreNames.contains(STORES.DASHBOARD_CACHE)) {
                const dashboardStore = database.createObjectStore(STORES.DASHBOARD_CACHE, {
                    keyPath: 'key'
                });
                dashboardStore.createIndex('timestamp', 'timestamp', { unique: false });
            }

            // Form drafts store
            if (!database.objectStoreNames.contains(STORES.FORM_DRAFTS)) {
                const draftsStore = database.createObjectStore(STORES.FORM_DRAFTS, {
                    keyPath: 'id',
                    autoIncrement: true
                });
                draftsStore.createIndex('formType', 'formType', { unique: false });
                draftsStore.createIndex('timestamp', 'timestamp', { unique: false });
            }

            // User preferences store
            if (!database.objectStoreNames.contains(STORES.USER_PREFERENCES)) {
                database.createObjectStore(STORES.USER_PREFERENCES, {
                    keyPath: 'key'
                });
            }

            console.log('[IndexedDB] Database upgrade complete');
        };
    });
}

// ============= Pending Requests (Background Sync) =============

/**
 * Add a request to the pending queue
 */
async function addPendingRequest(url, method, body, type = 'general') {
    const database = await initDB();
    
    return new Promise((resolve, reject) => {
        const transaction = database.transaction([STORES.PENDING_REQUESTS], 'readwrite');
        const store = transaction.objectStore(STORES.PENDING_REQUESTS);
        
        const request = {
            url: url,
            method: method,
            body: body,
            type: type,
            status: 'pending',
            timestamp: Date.now(),
            retryCount: 0,
            maxRetries: 3
        };
        
        const addRequest = store.add(request);
        
        addRequest.onsuccess = () => {
            console.log('[IndexedDB] Pending request added:', request.id);
            resolve(addRequest.result);
        };
        
        addRequest.onerror = (event) => {
            console.error('[IndexedDB] Error adding pending request:', event.target.error);
            reject(event.target.error);
        };
    });
}

/**
 * Get all pending requests
 */
async function getPendingRequests() {
    const database = await initDB();
    
    return new Promise((resolve, reject) => {
        const transaction = database.transaction([STORES.PENDING_REQUESTS], 'readonly');
        const store = transaction.objectStore(STORES.PENDING_REQUESTS);
        const index = store.index('status');
        const request = index.getAll('pending');
        
        request.onsuccess = () => {
            resolve(request.result || []);
        };
        
        request.onerror = (event) => {
            reject(event.target.error);
        };
    });
}

/**
 * Get pending requests count
 */
async function getPendingRequestsCount() {
    const database = await initDB();
    
    return new Promise((resolve, reject) => {
        const transaction = database.transaction([STORES.PENDING_REQUESTS], 'readonly');
        const store = transaction.objectStore(STORES.PENDING_REQUESTS);
        const index = store.index('status');
        const request = index.count('pending');
        
        request.onsuccess = () => {
            resolve(request.result);
        };
        
        request.onerror = (event) => {
            reject(event.target.error);
        };
    });
}

/**
 * Update pending request status
 */
async function updatePendingRequest(id, updates) {
    const database = await initDB();
    
    return new Promise((resolve, reject) => {
        const transaction = database.transaction([STORES.PENDING_REQUESTS], 'readwrite');
        const store = transaction.objectStore(STORES.PENDING_REQUESTS);
        
        const getRequest = store.get(id);
        
        getRequest.onsuccess = () => {
            const request = getRequest.result;
            if (request) {
                const updatedRequest = { ...request, ...updates };
                const putRequest = store.put(updatedRequest);
                
                putRequest.onsuccess = () => {
                    resolve(updatedRequest);
                };
                
                putRequest.onerror = (event) => {
                    reject(event.target.error);
                };
            } else {
                reject(new Error('Request not found'));
            }
        };
        
        getRequest.onerror = (event) => {
            reject(event.target.error);
        };
    });
}

/**
 * Remove pending request
 */
async function removePendingRequest(id) {
    const database = await initDB();
    
    return new Promise((resolve, reject) => {
        const transaction = database.transaction([STORES.PENDING_REQUESTS], 'readwrite');
        const store = transaction.objectStore(STORES.PENDING_REQUESTS);
        const request = store.delete(id);
        
        request.onsuccess = () => {
            console.log('[IndexedDB] Pending request removed:', id);
            resolve();
        };
        
        request.onerror = (event) => {
            reject(event.target.error);
        };
    });
}

/**
 * Clear all completed requests
 */
async function clearCompletedRequests() {
    const database = await initDB();
    
    return new Promise((resolve, reject) => {
        const transaction = database.transaction([STORES.PENDING_REQUESTS], 'readwrite');
        const store = transaction.objectStore(STORES.PENDING_REQUESTS);
        const index = store.index('status');
        const request = index.openCursor('completed');
        
        request.onsuccess = (event) => {
            const cursor = event.target.result;
            if (cursor) {
                cursor.delete();
                cursor.continue();
            } else {
                resolve();
            }
        };
        
        request.onerror = (event) => {
            reject(event.target.error);
        };
    });
}

// ============= Dashboard Cache =============

/**
 * Cache dashboard data
 */
async function cacheDashboardData(key, data) {
    const database = await initDB();
    
    return new Promise((resolve, reject) => {
        const transaction = database.transaction([STORES.DASHBOARD_CACHE], 'readwrite');
        const store = transaction.objectStore(STORES.DASHBOARD_CACHE);
        
        const cacheItem = {
            key: key,
            data: data,
            timestamp: Date.now()
        };
        
        const request = store.put(cacheItem);
        
        request.onsuccess = () => {
            resolve();
        };
        
        request.onerror = (event) => {
            reject(event.target.error);
        };
    });
}

/**
 * Get cached dashboard data
 */
async function getCachedDashboardData(key, maxAge = 5 * 60 * 1000) {
    const database = await initDB();
    
    return new Promise((resolve, reject) => {
        const transaction = database.transaction([STORES.DASHBOARD_CACHE], 'readonly');
        const store = transaction.objectStore(STORES.DASHBOARD_CACHE);
        const request = store.get(key);
        
        request.onsuccess = () => {
            const result = request.result;
            if (result) {
                const age = Date.now() - result.timestamp;
                if (age < maxAge) {
                    resolve(result.data);
                } else {
                    resolve(null); // Expired
                }
            } else {
                resolve(null);
            }
        };
        
        request.onerror = (event) => {
            reject(event.target.error);
        };
    });
}

// ============= Form Drafts =============

/**
 * Save form draft
 */
async function saveFormDraft(formType, formData) {
    const database = await initDB();
    
    return new Promise((resolve, reject) => {
        const transaction = database.transaction([STORES.FORM_DRAFTS], 'readwrite');
        const store = transaction.objectStore(STORES.FORM_DRAFTS);
        
        const draft = {
            formType: formType,
            data: formData,
            timestamp: Date.now()
        };
        
        const request = store.add(draft);
        
        request.onsuccess = () => {
            console.log('[IndexedDB] Form draft saved');
            resolve(request.result);
        };
        
        request.onerror = (event) => {
            reject(event.target.error);
        };
    });
}

/**
 * Get form drafts by type
 */
async function getFormDrafts(formType) {
    const database = await initDB();
    
    return new Promise((resolve, reject) => {
        const transaction = database.transaction([STORES.FORM_DRAFTS], 'readonly');
        const store = transaction.objectStore(STORES.FORM_DRAFTS);
        const index = store.index('formType');
        const request = index.getAll(formType);
        
        request.onsuccess = () => {
            resolve(request.result || []);
        };
        
        request.onerror = (event) => {
            reject(event.target.error);
        };
    });
}

/**
 * Delete form draft
 */
async function deleteFormDraft(id) {
    const database = await initDB();
    
    return new Promise((resolve, reject) => {
        const transaction = database.transaction([STORES.FORM_DRAFTS], 'readwrite');
        const store = transaction.objectStore(STORES.FORM_DRAFTS);
        const request = store.delete(id);
        
        request.onsuccess = () => {
            resolve();
        };
        
        request.onerror = (event) => {
            reject(event.target.error);
        };
    });
}

// ============= User Preferences =============

/**
 * Set user preference
 */
async function setPreference(key, value) {
    const database = await initDB();
    
    return new Promise((resolve, reject) => {
        const transaction = database.transaction([STORES.USER_PREFERENCES], 'readwrite');
        const store = transaction.objectStore(STORES.USER_PREFERENCES);
        
        const request = store.put({ key: key, value: value });
        
        request.onsuccess = () => {
            resolve();
        };
        
        request.onerror = (event) => {
            reject(event.target.error);
        };
    });
}

/**
 * Get user preference
 */
async function getPreference(key) {
    const database = await initDB();
    
    return new Promise((resolve, reject) => {
        const transaction = database.transaction([STORES.USER_PREFERENCES], 'readonly');
        const store = transaction.objectStore(STORES.USER_PREFERENCES);
        const request = store.get(key);
        
        request.onsuccess = () => {
            resolve(request.result?.value);
        };
        
        request.onerror = (event) => {
            reject(event.target.error);
        };
    });
}

// ============= Utility Functions =============

/**
 * Clear all IndexedDB data
 */
async function clearAllData() {
    const database = await initDB();
    
    const stores = [STORES.PENDING_REQUESTS, STORES.DASHBOARD_CACHE, STORES.FORM_DRAFTS];
    
    return Promise.all(stores.map(storeName => {
        return new Promise((resolve, reject) => {
            const transaction = database.transaction([storeName], 'readwrite');
            const store = transaction.objectStore(storeName);
            const request = store.clear();
            
            request.onsuccess = () => resolve();
            request.onerror = (event) => reject(event.target.error);
        });
    }));
}

/**
 * Get database stats
 */
async function getDatabaseStats() {
    const pendingCount = await getPendingRequestsCount();
    const drafts = await initDB().then(database => {
        return new Promise((resolve) => {
            const transaction = database.transaction([STORES.FORM_DRAFTS], 'readonly');
            const store = transaction.objectStore(STORES.FORM_DRAFTS);
            const request = store.count();
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => resolve(0);
        });
    });
    
    return {
        pendingRequests: pendingCount,
        formDrafts: drafts,
        databaseName: DB_NAME,
        version: DB_VERSION
    };
}

// Export for use in both browser and service worker contexts
if (typeof window !== 'undefined') {
    window.BizmarkDB = {
        init: initDB,
        // Pending requests
        addPendingRequest,
        getPendingRequests,
        getPendingRequestsCount,
        updatePendingRequest,
        removePendingRequest,
        clearCompletedRequests,
        // Dashboard cache
        cacheDashboardData,
        getCachedDashboardData,
        // Form drafts
        saveFormDraft,
        getFormDrafts,
        deleteFormDraft,
        // Preferences
        setPreference,
        getPreference,
        // Utility
        clearAllData,
        getDatabaseStats,
        STORES
    };
}

// For service worker context
if (typeof self !== 'undefined' && typeof window === 'undefined') {
    self.BizmarkDB = {
        init: initDB,
        getPendingRequests,
        updatePendingRequest,
        removePendingRequest,
        STORES
    };
}

console.log('[IndexedDB] Bizmark Mobile IndexedDB Manager loaded');

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Axios Interceptor for Offline Queue
window.axios.interceptors.response.use(
    response => response,
    async error => {
        const { config, message } = error;

        // If it's a network error and we are offline
        if (!navigator.onLine || message === 'Network Error') {
            const isMutation = ['post', 'put', 'patch', 'delete'].includes(config.method.toLowerCase());

            if (isMutation) {
                console.warn('📡 Network error detected. Queuing request for offline sync...');

                await window.OfflineManager.queueRequest(config.url, {
                    method: config.method,
                    headers: config.headers,
                    body: config.data // Axios data is already the body
                });

                // Return a "faux" success or handled state to prevent UI from breaking
                // Or let the caller handle the "Queued" state if we want more granular feedback.
                // For now, let's just alert.
                return Promise.resolve({ data: { success: true, message: 'Request queued for offline sync' }, status: 202 });
            }
        }

        return Promise.reject(error);
    }
);

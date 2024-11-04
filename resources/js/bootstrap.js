/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

axios.interceptors.response.use(response => response, async error => {
    if (error.hasOwnProperty('code') && error.code === 'ERR_CANCELED') {
        return Promise.reject(error)
    }

    const status = error.response.status;

    if (status === 419) {
        // Refresh session token
        const responseCsrfToken = await axios.get('/mixpost/refresh-csrf-token', {
            headers: {
                'Accept': 'text/html',
            },
        })

        const isLoginResponse = responseCsrfToken.request.responseURL.includes('/mixpost/login');

        if (isLoginResponse) {
            window.location.reload();
        }

        if (!isLoginResponse) {
            // Return a new request using the original request's configuration
            return axios(error.response.config)
        }
    }

    // 401 Unauthorized for non-Inertia requests
    if (status === 401) {
        window.location.reload();
    }

    return Promise.reject(error)
})

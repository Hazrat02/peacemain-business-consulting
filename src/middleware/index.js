import { reactive, toRef } from 'vue';
import axios from 'axios';
import { setloading } from '../utils/extra';

// Reactive state for authentication
const state = reactive({
  authenticated: false,
});

const authenticatedRef = toRef(state, 'authenticated');

// Check localStorage for token during initialization
if (localStorage.getItem('auth_token')) {
  authenticatedRef.value = true;
} else {
  authenticatedRef.value = false;
}

export function logout() {
  localStorage.removeItem('auth_token');
  localStorage.removeItem('user_data');
  authenticatedRef.value = false;
}

export function login(token) {
  localStorage.setItem('auth_token', token);
  authenticatedRef.value = true;
}

export function isAuthenticated() {
  return authenticatedRef.value;
}

export function setupRouterGuard(router) {
  router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('auth_token');

    axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000';
    if (token) {
      axios.defaults.headers.common.Authorization = `Bearer ${token}`;
    } else {
      delete axios.defaults.headers.common.Authorization;
    }

    setloading(true);
    next();
  });

  // Hook to run after each navigation
  router.afterEach(() => {
    setloading(false); // Set loading to false after the navigation is complete
  });
}

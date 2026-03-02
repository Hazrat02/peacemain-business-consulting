import './assets/main.css';
// import "owl.carousel/dist/assets/owl.carousel.css";
import 'vue3-carousel/dist/carousel.css';
import { setupRouterGuard } from './middleware/index';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { useAuthUserStore } from './stores/user';

import animationDirective from './directives/animationDirective';
import Toast, { POSITION } from 'vue-toastification';
import { useToast } from "vue-toastification";
import 'vue-toastification/dist/index.css';

import App from './App.vue';
import router from './router';

import {setloading,isLoading} from './utils/extra.js';

const options = {
  position: POSITION.TOP_RIGHT, // Default position for all toasts
  timeout: 5000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: 'button',
  icon: true,
  rtl: false,
  zIndex: 9999,
};

const app = createApp(App);
setupRouterGuard(router);
const pinia = createPinia();
app.use(pinia);
app.use(router);
app.use(Toast, options);
app.directive('animate', animationDirective);

const authStore = useAuthUserStore(pinia);
authStore.loadUserFromLocalStorage();

// Make toast globally available
const toast = useToast();
app.config.globalProperties.$toast = toast;

app.config.globalProperties.$setLoading = setloading;
app.config.globalProperties.$isLoading = isLoading;

app.mount('#app');

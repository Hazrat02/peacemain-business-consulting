import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import * as bootstrap from 'bootstrap';
import feather from 'feather-icons';
import SimpleBar from 'simplebar';
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

window.bootstrap = bootstrap;
window.feather = feather;

let layoutInitialized = false;
let lastToastSignature = '';

const toaster = new Notyf({
    duration: 3500,
    position: { x: 'right', y: 'top' },
    dismissible: true,
});

const initializeSidebarToggle = () => {
    if (layoutInitialized) {
        return;
    }

    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('.js-sidebar-toggle');
        if (!toggle) {
            return;
        }

        event.preventDefault();

        const sidebar = document.querySelector('.js-sidebar');
        if (!sidebar) {
            return;
        }

        sidebar.classList.toggle('collapsed');
        sidebar.addEventListener(
            'transitionend',
            () => window.dispatchEvent(new Event('resize')),
            { once: true }
        );
    });

    layoutInitialized = true;
};

const initializeSimplebar = () => {
    document.querySelectorAll('.js-simplebar').forEach((element) => {
        if (element.dataset.simplebarInitialized === 'true') {
            return;
        }

        new SimpleBar(element);
        element.dataset.simplebarInitialized = 'true';
    });
};

const refreshFeatherIcons = () => {
    if (typeof window !== 'undefined' && window.feather?.replace) {
        window.requestAnimationFrame(() => {
            window.feather.replace();
        });
    }
};

const initializeLayout = () => {
    initializeSidebarToggle();
    initializeSimplebar();
    refreshFeatherIcons();
};

const getFirstErrorMessage = (errors) => {
    if (!errors || typeof errors !== 'object') {
        return null;
    }

    for (const value of Object.values(errors)) {
        if (Array.isArray(value) && value.length > 0 && typeof value[0] === 'string') {
            return value[0];
        }

        if (typeof value === 'string' && value.trim() !== '') {
            return value;
        }
    }

    return null;
};

const showToastsFromPage = (page) => {
    const status = page?.props?.flash?.status || '';
    const errorMessage = getFirstErrorMessage(page?.props?.errors);
    const signature = `${status}|${errorMessage || ''}`;

    if (signature === lastToastSignature) {
        return;
    }

    lastToastSignature = signature;

    if (status) {
        toaster.success(status);
    }

    if (errorMessage) {
        toaster.error(errorMessage);
    }
};

router.on('navigate', initializeLayout);
router.on('success', (event) => showToastsFromPage(event.detail.page));

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);

        initializeLayout();
        showToastsFromPage(props.initialPage);

        return app;
    },
    progress: {
        color: '#0f766e',
    },
});

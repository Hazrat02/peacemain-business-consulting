<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const logoutForm = useForm({});
const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
const currentUrl = computed(() => (page.url || '').split('?')[0]);
const avatarSrc = computed(() => page.props.auth?.user?.profile_image_url || '/adminkit/img/avatars/avatar.jpg');
const brandLogoUrl = computed(() => page.props.brand?.logoUrl || '');
const defaultBrandIcon = '/adminkit/img/icons/icon-48x48.png';

const userMenus = [
    { label: 'Dashboard', icon: 'fas fa-home', href: '/dashboard' },
    { label: 'Documents', icon: 'fas fa-file-upload', href: '/dashboard/documents' },
    { label: 'Profile', icon: 'fas fa-user', href: '/profile' },
];

const isActive = (item) => currentUrl.value === item.href;

const toggleSidebar = () => {
    if (window.innerWidth < 992) {
        sidebarOpen.value = !sidebarOpen.value;
        return;
    }

    sidebarCollapsed.value = !sidebarCollapsed.value;
};

const closeSidebarOnMobile = () => {
    if (window.innerWidth < 992) {
        sidebarOpen.value = false;
    }
};

const logout = () => {
    logoutForm.post('/logout');
};
</script>

<template>
    <Head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous">
        <link
            href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"
            rel="stylesheet"
        >
        <link
            rel="stylesheet"
            href="https://adminlte.io/themes/v3/plugins/fontawesome-free/css/all.min.css"
        >
    </Head>

    <div
        class="app-shell hold-transition sidebar-mini layout-fixed layout-navbar-fixed"
        :class="{ 'sidebar-collapse': sidebarCollapsed, 'sidebar-open': sidebarOpen }"
    >
        <div class="wrapper">
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#" role="button" @click.prevent="toggleSidebar">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <Link href="/dashboard" class="nav-link">Dashboard</Link>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <Link href="/dashboard/documents" class="nav-link">Documents</Link>
                    </li>
                </ul>

                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img :src="avatarSrc" class="user-image img-circle elevation-2" alt="User">
                            <span class="d-none d-md-inline">{{ page.props.auth?.user?.full_name || page.props.auth?.user?.email }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <li class="user-header bg-info">
                                <img :src="avatarSrc" class="img-circle elevation-2" alt="User">
                                <p>
                                    {{ page.props.auth?.user?.full_name || page.props.auth?.user?.email }}
                                    <small>User Dashboard</small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <Link href="/profile" class="btn btn-default btn-flat">Profile</Link>
                                <a href="#" class="btn btn-default btn-flat float-right" @click.prevent="logout">Logout</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <Link href="/dashboard" class="brand-link">
                    <img
                        v-if="brandLogoUrl"
                        :src="brandLogoUrl"
                        alt="Brand Logo"
                        class="brand-image img-circle elevation-3"
                        style="opacity: 0.8"
                    >
                    <img
                        v-else
                        :src="defaultBrandIcon"
                        alt="User Logo"
                        class="brand-image img-circle elevation-3"
                        style="opacity: 0.8"
                    >
                    <span class="brand-text font-weight-light">PEACEMAIN</span>
                </Link>

                <div class="sidebar">
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            <img :src="avatarSrc" class="img-circle elevation-2" alt="User">
                        </div>
                        <div class="info">
                            <Link href="/profile" class="d-block">
                                {{ page.props.auth?.user?.full_name || page.props.auth?.user?.email }}
                            </Link>
                        </div>
                    </div>

                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-accordion="false">
                            <li v-for="item in userMenus" :key="item.href" class="nav-item">
                                <Link
                                    :href="item.href"
                                    class="nav-link"
                                    :class="{ active: isActive(item) }"
                                    @click="closeSidebarOnMobile"
                                >
                                    <i class="nav-icon" :class="item.icon"></i>
                                    <p>{{ item.label }}</p>
                                </Link>
                            </li>

                            <li class="nav-header">SUPPORT</li>
                            <li class="nav-item">
                                <a
                                    href="https://www.peacemain.com/risk/disclosure"
                                    class="nav-link"
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    <i class="nav-icon fas fa-life-ring"></i>
                                    <p>Help Center</p>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </aside>

            <div class="content-wrapper">
                <section class="content">
                    <div class="container-fluid">
                        <slot />
                    </div>
                </section>
            </div>

            <footer class="main-footer">
                <strong>Copyright &copy; 2026 <Link href="/">PEACEMAIN</Link>.</strong>
                All rights reserved.
                <div class="float-right d-none d-sm-inline-block">
                    <b>Panel</b> User
                </div>
            </footer>
        </div>
    </div>
</template>

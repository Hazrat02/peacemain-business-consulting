<script setup>
import { computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const logoutForm = useForm({});
const currentUrl = computed(() => (page.url || '').split('?')[0]);
const avatarSrc = computed(() => page.props.auth?.user?.profile_image_url || '/adminkit/img/avatars/avatar.jpg');
const brandLogoUrl = computed(() => page.props.brand?.logoUrl || '');

const isExact = (url) => currentUrl.value === url;

const logout = () => {
    logoutForm.post('/logout');
};
</script>

<template>
    <div class="wrapper">
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <Link class="sidebar-brand" href="/dashboard">
                    <img v-if="brandLogoUrl" :src="brandLogoUrl" alt="Brand Logo" style="max-height: 44px; width: auto;" />
                    <span v-else class="align-middle">User Panel</span>
                </Link>

                <ul class="sidebar-nav">
                    <li class="sidebar-header">User Dashboard</li>

                    <li class="sidebar-item" :class="{ active: isExact('/dashboard') }">
                        <Link class="sidebar-link" href="/dashboard">
                            <i class="align-middle" data-feather="home"></i> <span class="align-middle">Dashboard</span>
                        </Link>
                    </li>
                    <li class="sidebar-item" :class="{ active: isExact('/dashboard/documents') }">
                        <Link class="sidebar-link" href="/dashboard/documents">
                            <i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Documents</span>
                        </Link>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="main">
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle js-sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>

                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">
                        <li class="nav-item dropdown">
                            <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                                <i class="align-middle" data-feather="settings"></i>
                            </a>

                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                                <img :src="avatarSrc" class="avatar img-fluid rounded me-1" alt="User" />
                                <span class="text-dark">{{ page.props.auth?.user?.full_name || page.props.auth?.user?.email }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="/profile"><i class="align-middle me-1" data-feather="user"></i> Profile</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="https://www.peacemain.com/risk/disclosure"><i class="align-middle me-1" data-feather="help-circle"></i> Help Center</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" @click.prevent="logout">Log out</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="content">
                <div class="container-fluid p-0">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

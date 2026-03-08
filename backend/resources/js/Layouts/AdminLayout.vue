<script setup>
import { computed } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const logoutForm = useForm({});
const currentUrl = computed(() => (page.url || '').split('?')[0]);
const avatarSrc = computed(() => page.props.auth?.user?.profile_image_url || '/adminkit/img/avatars/avatar.jpg');
const contactMessages = computed(() => page.props.adminHeader?.contactMessages || []);
const contactMessageCount = computed(() => page.props.adminHeader?.unreadContactCount || 0);
const recentDocs = computed(() => page.props.adminHeader?.recentDocs || []);
const unreadRecentDocsCount = computed(() => page.props.adminHeader?.unreadRecentDocsCount || 0);
const brandLogoUrl = computed(() => page.props.adminHeader?.brandLogoUrl || '');

const isExact = (url) => currentUrl.value === url;
const isPrefix = (url) => currentUrl.value.startsWith(url);
const formatTime = (value) => {
    if (!value) {
        return '';
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return '';
    }

    return date.toLocaleString();
};
const previewText = (item) => {
    const text = item?.subject || item?.message || item?.email || '';
    return text.length > 70 ? `${text.slice(0, 70)}...` : text;
};
const docPreviewText = (item) => {
    const text = item?.document_title || item?.file_name || 'Document';
    return text.length > 60 ? `${text.slice(0, 60)}...` : text;
};
const markAsRead = (id) => {
    router.patch(`/admin/contact-us/${id}/read`, {}, { preserveState: true, preserveScroll: true, only: ['adminHeader'] });
};
const markDocSeen = (id) => {
    router.patch(`/admin/document-submissions/${id}/seen`, {}, { preserveState: true, preserveScroll: true, only: ['adminHeader'] });
};

const logout = () => {
    logoutForm.post('/logout');
};
</script>

<template>
    <div class="wrapper">
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <Link class="sidebar-brand" href="/admin/dashboard">
                    <img v-if="brandLogoUrl" :src="brandLogoUrl" alt="Admin Logo" style="max-height: 44px; width: auto;" />
                    <span v-else class="align-middle">AdminKit</span>
                </Link>

                <ul class="sidebar-nav">
                    <li class="sidebar-header">Admin Panel</li>

                    <li class="sidebar-item" :class="{ active: isExact('/admin/dashboard') }">
                        <Link class="sidebar-link" href="/admin/dashboard">
                            <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
                        </Link>
                    </li>

                    <li class="sidebar-item" :class="{ active: isPrefix('/admin/users') }">
                        <Link class="sidebar-link" href="/admin/users">
                            <i class="align-middle" data-feather="users"></i> <span class="align-middle">User Manage</span>
                        </Link>
                    </li>

                    <li class="sidebar-item" :class="{ active: isExact('/admin/contact-us') }">
                        <Link class="sidebar-link" href="/admin/contact-us">
                            <i class="align-middle" data-feather="mail"></i> <span class="align-middle">Contact-us</span>
                        </Link>
                    </li>

                    <li class="sidebar-item" :class="{ active: isExact('/admin/roles') }">
                        <Link class="sidebar-link" href="/admin/roles">
                            <i class="align-middle" data-feather="shield"></i> <span class="align-middle">Role Manage</span>
                        </Link>
                    </li>

                    <li class="sidebar-header">Content Manage</li>

                    <li class="sidebar-item" :class="{ active: isExact('/admin/content/banner') }">
                        <Link class="sidebar-link" href="/admin/content/banner">
                            <i class="align-middle" data-feather="image"></i> <span class="align-middle">Banner</span>
                        </Link>
                    </li>
                    <li class="sidebar-item" :class="{ active: isExact('/admin/content/sidebar') }">
                        <Link class="sidebar-link" href="/admin/content/sidebar">
                            <i class="align-middle" data-feather="list"></i> <span class="align-middle">Sidebar</span>
                        </Link>
                    </li>
                    <li class="sidebar-item" :class="{ active: isExact('/admin/content/faq') }">
                        <Link class="sidebar-link" href="/admin/content/faq">
                            <i class="align-middle" data-feather="help-circle"></i> <span class="align-middle">FAQ</span>
                        </Link>
                    </li>
                    <li class="sidebar-item" :class="{ active: isExact('/admin/content/contact-info') }">
                        <Link class="sidebar-link" href="/admin/content/contact-info">
                            <i class="align-middle" data-feather="phone"></i> <span class="align-middle">Contact Us Info</span>
                        </Link>
                    </li>

                    <li class="sidebar-item" :class="{ active: isExact('/admin/settings') }">
                        <Link class="sidebar-link" href="/admin/settings">
                            <i class="align-middle" data-feather="settings"></i> <span class="align-middle">Settings</span>
                        </Link>
                    </li>

                    <li class="sidebar-header">Documents</li>

                    <li class="sidebar-item" :class="{ active: isPrefix('/admin/documents') }">
                        <Link class="sidebar-link" href="/admin/documents">
                            <i class="align-middle" data-feather="folder"></i> <span class="align-middle">Master Checklist</span>
                        </Link>
                    </li>
                    <li class="sidebar-item" :class="{ active: isExact('/admin/document-checklists') }">
                        <Link class="sidebar-link" href="/admin/document-checklists">
                            <i class="align-middle" data-feather="check-square"></i> <span class="align-middle">User Reviews</span>
                        </Link>
                    </li>
                    <li class="sidebar-item" :class="{ active: isExact('/admin/recent-docs') }">
                        <Link class="sidebar-link" href="/admin/recent-docs">
                            <i class="align-middle" data-feather="clock"></i> <span class="align-middle">Recent docs</span>
                        </Link>
                    </li>
                </ul>

                <div class="sidebar-cta">
                    <div class="sidebar-cta-content">
                        <strong class="d-inline-block mb-2">Logged in</strong>
                        <div class="mb-3 text-sm">
                            {{ page.props.auth?.user?.full_name || page.props.auth?.user?.email }}
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary" type="button" @click="logout">Logout</button>
                        </div>
                    </div>
                </div>
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
                            <a class="nav-icon dropdown-toggle" href="#" id="alertsDropdown" data-bs-toggle="dropdown">
                                <div class="position-relative">
                                    <i class="align-middle" data-feather="bell"></i>
                                    <span v-if="unreadRecentDocsCount" class="indicator">{{ unreadRecentDocsCount }}</span>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="alertsDropdown">
                                <div class="dropdown-menu-header">{{ recentDocs.length }} Recent Documents</div>
                                <div class="list-group">
                                    <Link v-for="doc in recentDocs" :key="`doc-alert-${doc.id}`" href="/admin/recent-docs" class="list-group-item">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <i class="text-primary" data-feather="file-text"></i>
                                            </div>
                                            <div class="col-10">
                                                <div class="text-dark" :class="{ 'fw-bold': !doc.is_seen }">{{ doc.user_name }}</div>
                                                <div class="text-muted small mt-1">{{ docPreviewText(doc) }}</div>
                                                <div class="d-flex align-items-center justify-content-between mt-1">
                                                    <div class="text-muted small">{{ doc.user_email }} | {{ formatTime(doc.created_at) }}</div>
                                                    <button
                                                        v-if="!doc.is_seen"
                                                        type="button"
                                                        class="btn btn-link btn-sm p-0 text-decoration-none"
                                                        @click.prevent.stop="markDocSeen(doc.id)"
                                                    >
                                                        Mark seen
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </Link>
                                    <div v-if="!recentDocs.length" class="list-group-item text-muted small">
                                        No recent document submissions.
                                    </div>
                                </div>
                                <div class="dropdown-menu-footer">
                                    <Link href="/admin/recent-docs" class="text-muted">Show all submissions</Link>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-icon dropdown-toggle" href="#" id="messagesDropdown" data-bs-toggle="dropdown">
                                <div class="position-relative">
                                    <i class="align-middle" data-feather="message-square"></i>
                                    <span v-if="contactMessageCount" class="indicator">{{ contactMessageCount }}</span>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="messagesDropdown">
                                <div class="dropdown-menu-header">
                                    <div class="position-relative">{{ contactMessages.length }} Latest Contact Messages</div>
                                </div>
                                <div class="list-group">
                                    <Link
                                        v-for="item in contactMessages"
                                        :key="`contact-${item.id}`"
                                        :href="`/admin/contact-us/${item.id}/reply`"
                                        class="list-group-item"
                                    >
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <img :src="'/adminkit/img/avatars/avatar.jpg'" class="avatar img-fluid rounded-circle" :alt="item.name" />
                                            </div>
                                            <div class="col-10 ps-2">
                                                <div class="text-dark" :class="{ 'fw-bold': !item.is_read }">{{ item.name }}</div>
                                                <div class="text-muted small mt-1">{{ previewText(item) }}</div>
                                                <div class="d-flex align-items-center justify-content-between mt-1">
                                                    <div class="text-muted small">{{ formatTime(item.created_at) }}</div>
                                                    <button
                                                        v-if="!item.is_read"
                                                        type="button"
                                                        class="btn btn-link btn-sm p-0 text-decoration-none"
                                                        @click.prevent.stop="markAsRead(item.id)"
                                                    >
                                                        Mark read
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </Link>
                                    <div v-if="!contactMessages.length" class="list-group-item text-muted small">
                                        No contact messages yet.
                                    </div>
                                </div>
                                <div class="dropdown-menu-footer">
                                    <Link href="/admin/contact-us" class="text-muted">Show all messages</Link>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                                <i class="align-middle" data-feather="settings"></i>
                            </a>

                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                                <img :src="avatarSrc" class="avatar img-fluid rounded me-1" alt="Admin" />
                                <span class="text-dark">{{ page.props.auth?.user?.full_name || page.props.auth?.user?.email }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <Link class="dropdown-item" href="/admin/profile"><i class="align-middle me-1" data-feather="user"></i> Profile</Link>
                                <div class="dropdown-divider"></div>
                                <Link class="dropdown-item" href="/admin/settings"><i class="align-middle me-1" data-feather="settings"></i> Settings & Privacy</Link>
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

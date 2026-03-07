<script setup>
import { computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const logoutForm = useForm({});
const currentUrl = computed(() => (page.url || '').split('?')[0]);
const avatarSrc = computed(() => page.props.auth?.user?.profile_image_url || '/adminkit/img/avatars/avatar.jpg');

const isExact = (url) => currentUrl.value === url;
const isPrefix = (url) => currentUrl.value.startsWith(url);

const logout = () => {
    logoutForm.post('/logout');
};
</script>

<template>
    <div class="wrapper">
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <Link class="sidebar-brand" href="/admin/dashboard">
                    <span class="align-middle">AdminKit</span>
                </Link>

                <ul class="sidebar-nav">
                    <li class="sidebar-header">Admin Panel</li>

                    <li class="sidebar-item" :class="{ active: isExact('/admin/dashboard') }">
                        <Link class="sidebar-link" href="/admin/dashboard">
                            <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
                        </Link>
                    </li>

                    <li class="sidebar-item" :class="{ active: isExact('/admin/users') }">
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
                                    <span class="indicator">4</span>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="alertsDropdown">
                                <div class="dropdown-menu-header">4 New Notifications</div>
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <i class="text-danger" data-feather="alert-circle"></i>
                                            </div>
                                            <div class="col-10">
                                                <div class="text-dark">Update completed</div>
                                                <div class="text-muted small mt-1">Restart server 12 to complete the update.</div>
                                                <div class="text-muted small mt-1">30m ago</div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <i class="text-warning" data-feather="bell"></i>
                                            </div>
                                            <div class="col-10">
                                                <div class="text-dark">Lorem ipsum</div>
                                                <div class="text-muted small mt-1">Aliquam ex eros, imperdiet vulputate hendrerit et.</div>
                                                <div class="text-muted small mt-1">2h ago</div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <i class="text-primary" data-feather="home"></i>
                                            </div>
                                            <div class="col-10">
                                                <div class="text-dark">Login from 192.186.1.8</div>
                                                <div class="text-muted small mt-1">5h ago</div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <i class="text-success" data-feather="user-plus"></i>
                                            </div>
                                            <div class="col-10">
                                                <div class="text-dark">New connection</div>
                                                <div class="text-muted small mt-1">Christina accepted your request.</div>
                                                <div class="text-muted small mt-1">14h ago</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="dropdown-menu-footer">
                                    <a href="#" class="text-muted">Show all notifications</a>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-icon dropdown-toggle" href="#" id="messagesDropdown" data-bs-toggle="dropdown">
                                <div class="position-relative">
                                    <i class="align-middle" data-feather="message-square"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="messagesDropdown">
                                <div class="dropdown-menu-header">
                                    <div class="position-relative">4 New Messages</div>
                                </div>
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <img :src="'/adminkit/img/avatars/avatar-5.jpg'" class="avatar img-fluid rounded-circle" alt="Vanessa Tucker" />
                                            </div>
                                            <div class="col-10 ps-2">
                                                <div class="text-dark">Vanessa Tucker</div>
                                                <div class="text-muted small mt-1">Nam pretium turpis et arcu. Duis arcu tortor.</div>
                                                <div class="text-muted small mt-1">15m ago</div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <img :src="'/adminkit/img/avatars/avatar-2.jpg'" class="avatar img-fluid rounded-circle" alt="William Harris" />
                                            </div>
                                            <div class="col-10 ps-2">
                                                <div class="text-dark">William Harris</div>
                                                <div class="text-muted small mt-1">Curabitur ligula sapien euismod vitae.</div>
                                                <div class="text-muted small mt-1">2h ago</div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <img :src="'/adminkit/img/avatars/avatar-4.jpg'" class="avatar img-fluid rounded-circle" alt="Christina Mason" />
                                            </div>
                                            <div class="col-10 ps-2">
                                                <div class="text-dark">Christina Mason</div>
                                                <div class="text-muted small mt-1">Pellentesque auctor neque nec urna.</div>
                                                <div class="text-muted small mt-1">4h ago</div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-2">
                                                <img :src="'/adminkit/img/avatars/avatar-3.jpg'" class="avatar img-fluid rounded-circle" alt="Sharon Lessman" />
                                            </div>
                                            <div class="col-10 ps-2">
                                                <div class="text-dark">Sharon Lessman</div>
                                                <div class="text-muted small mt-1">Aenean tellus metus, bibendum sed, posuere ac, mattis non.</div>
                                                <div class="text-muted small mt-1">5h ago</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="dropdown-menu-footer">
                                    <a href="#" class="text-muted">Show all messages</a>
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
                                <a class="dropdown-item" href="/admin/profile"><i class="align-middle me-1" data-feather="user"></i> Profile</a>
                                <a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="pie-chart"></i> Analytics</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="settings"></i> Settings & Privacy</a>
                                <a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="help-circle"></i> Help Center</a>
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

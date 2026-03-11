<script setup>
import { computed, ref } from "vue";
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";

const page = usePage();
const logoutForm = useForm({});
const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
const currentUrl = computed(() => (page.url || "").split("?")[0]);
const avatarSrc = computed(
  () =>
    page.props.auth?.user?.profile_image_url ||
    "/adminkit/img/avatars/avatar.jpg"
);
const contactMessages = computed(
  () => page.props.adminHeader?.contactMessages || []
);
const contactMessageCount = computed(
  () => page.props.adminHeader?.unreadContactCount || 0
);
const recentDocs = computed(() => page.props.adminHeader?.recentDocs || []);
const unreadRecentDocsCount = computed(
  () => page.props.adminHeader?.unreadRecentDocsCount || 0
);
const brandLogoUrl = computed(() => page.props.adminHeader?.brandLogoUrl || "");
const defaultBrandIcon = "/adminkit/img/icons/icon-48x48.png";

const adminMenus = [
  {
    label: "Dashboard",
    icon: "fas fa-tachometer-alt",
    href: "/admin/dashboard",
    match: "exact",
  },
  {
    label: "User Manage",
    icon: "fas fa-users",
    href: "/admin/users",
    match: "prefix",
  },
  {
    label: "Contact-us",
    icon: "far fa-envelope",
    href: "/admin/contact-us",
    match: "prefix",
  },
  {
    label: "Role Manage",
    icon: "fas fa-user-shield",
    href: "/admin/roles",
    match: "exact",
  },
];

const contentMenus = [
  { label: "Banner", href: "/admin/content/banner" },
  { label: "Sidebar", href: "/admin/content/sidebar" },
  { label: "FAQ", href: "/admin/content/faq" },
  { label: "Contact Us Info", href: "/admin/content/contact-info" },
  { label: "Settings", href: "/admin/settings" },
];

const documentMenus = [
  { label: "Master Checklist", href: "/admin/documents", match: "prefix" },
  {
    label: "User Reviews",
    href: "/admin/document-checklists",
    match: "prefix",
  },
  { label: "Recent Docs", href: "/admin/recent-docs", match: "exact" },
];

const openGroups = ref({
  content: false,
  documents: false,
});

const isExact = (url) => currentUrl.value === url;
const isActive = (item) =>
  item.match === "prefix"
    ? currentUrl.value.startsWith(item.href)
    : isExact(item.href);
const groupIsActive = (items) => items.some((item) => isActive(item));
const toggleGroup = (key) => {
  openGroups.value[key] = !openGroups.value[key];
};

const formatTime = (value) => {
  if (!value) {
    return "";
  }

  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return "";
  }

  return date.toLocaleString();
};

const previewText = (item) => {
  const text = item?.subject || item?.message || item?.email || "";
  return text.length > 70 ? `${text.slice(0, 70)}...` : text;
};

const docPreviewText = (item) => {
  const text = item?.document_title || item?.file_name || "Document";
  return text.length > 60 ? `${text.slice(0, 60)}...` : text;
};

const markAsRead = (id) => {
  router.patch(
    `/admin/contact-us/${id}/read`,
    {},
    { preserveState: true, preserveScroll: true, only: ["adminHeader"] }
  );
};

const markDocSeen = (id) => {
  router.patch(
    `/admin/document-submissions/${id}/seen`,
    {},
    { preserveState: true, preserveScroll: true, only: ["adminHeader"] }
  );
};

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
  logoutForm.post("/logout");
};
</script>

<template>
  <Head>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
      rel="preconnect"
      href="https://fonts.gstatic.com"
      crossorigin="anonymous"
    />
    <link
      href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://adminlte.io/themes/v3/plugins/fontawesome-free/css/all.min.css"
    />
  </Head>

  <div
    class="app-shell hold-transition sidebar-mini layout-fixed layout-navbar-fixed"
    :class="{
      'sidebar-collapse': sidebarCollapsed,
      'sidebar-open': sidebarOpen,
    }"
  >
    <div class="wrapper">
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a
              class="nav-link"
              href="#"
              role="button"
              @click.prevent="toggleSidebar"
            >
              <i class="fas fa-bars"></i>
            </a>
          </li>
          <li class="nav-item d-none d-sm-inline-block">
            <Link href="/admin/dashboard" class="nav-link">Home</Link>
          </li>
          <li class="nav-item d-none d-sm-inline-block">
            <Link href="/admin/settings" class="nav-link">Settings</Link>
          </li>
        </ul>

        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a class="nav-link" href="#" data-bs-toggle="dropdown">
              <i class="far fa-file-alt"></i>
              <span
                v-if="unreadRecentDocsCount"
                class="badge badge-warning navbar-badge"
                >{{ unreadRecentDocsCount }}</span
              >
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
              <span class="dropdown-item dropdown-header"
                >{{ recentDocs.length }} Recent Documents</span
              >
              <div class="dropdown-divider"></div>
              <template v-if="recentDocs.length">
                <Link
                  v-for="doc in recentDocs"
                  :key="`doc-${doc.id}`"
                  href="/admin/recent-docs"
                  class="dropdown-item"
                  @click="closeSidebarOnMobile"
                >
                  <div
                    class="d-flex justify-content-between align-items-start gap-2"
                  >
                    <div>
                      <div :class="{ 'font-weight-bold': !doc.is_seen }">
                        {{ doc.user_name }}
                      </div>
                      <div class="text-sm text-muted">
                        {{ docPreviewText(doc) }}
                      </div>
                      <div class="text-xs text-muted">
                        {{ doc.user_email }} | {{ formatTime(doc.created_at) }}
                      </div>
                    </div>
                    <button
                      v-if="!doc.is_seen"
                      type="button"
                      class="btn btn-link btn-sm p-0"
                      @click.prevent.stop="markDocSeen(doc.id)"
                    >
                      Seen
                    </button>
                  </div>
                </Link>
                <div class="dropdown-divider"></div>
              </template>
              <span v-else class="dropdown-item text-sm text-muted"
                >No recent document submissions.</span
              >
              <Link
                href="/admin/recent-docs"
                class="dropdown-item dropdown-footer"
                >Show all submissions</Link
              >
            </div>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link" href="#" data-bs-toggle="dropdown">
              <i class="far fa-comments"></i>
              <span
                v-if="contactMessageCount"
                class="badge badge-danger navbar-badge"
                >{{ contactMessageCount }}</span
              >
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
              <span class="dropdown-item dropdown-header"
                >{{ contactMessages.length }} Latest Contact Messages</span
              >
              <div class="dropdown-divider"></div>
              <template v-if="contactMessages.length">
                <Link
                  v-for="item in contactMessages"
                  :key="`contact-${item.id}`"
                  :href="`/admin/contact-us/${item.id}/reply`"
                  class="dropdown-item"
                  @click="closeSidebarOnMobile"
                >
                  <div
                    class="d-flex justify-content-between align-items-start gap-2"
                  >
                    <div>
                      <div :class="{ 'font-weight-bold': !item.is_read }">
                        {{ item.name }}
                      </div>
                      <div class="text-sm text-muted">
                        {{ previewText(item) }}
                      </div>
                      <div class="text-xs text-muted">
                        {{ formatTime(item.created_at) }}
                      </div>
                    </div>
                    <button
                      v-if="!item.is_read"
                      type="button"
                      class="btn btn-link btn-sm p-0"
                      @click.prevent.stop="markAsRead(item.id)"
                    >
                      Read
                    </button>
                  </div>
                </Link>
                <div class="dropdown-divider"></div>
              </template>
              <span v-else class="dropdown-item text-sm text-muted"
                >No contact messages yet.</span
              >
              <Link
                href="/admin/contact-us"
                class="dropdown-item dropdown-footer"
                >Show all messages</Link
              >
            </div>
          </li>

          <li class="nav-item dropdown user-menu">
            <a
              href="#"
              class="nav-link dropdown-toggle"
              data-bs-toggle="dropdown"
            >
              <img
                :src="avatarSrc"
                class="user-image img-circle elevation-2"
                alt="Admin"
              />
              <span class="d-none d-md-inline">{{
                page.props.auth?.user?.full_name || page.props.auth?.user?.email
              }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
              <li class="user-header bg-primary">
                <img
                  :src="avatarSrc"
                  class="img-circle elevation-2"
                  alt="Admin"
                />
                <p>
                  {{
                    page.props.auth?.user?.full_name ||
                    page.props.auth?.user?.email
                  }}
                  <small>Administrator</small>
                </p>
              </li>
              <li class="user-footer">
                <Link href="/admin/profile" class="btn btn-default btn-flat"
                  >Profile</Link
                >
                <a
                  href="#"
                  class="btn btn-default btn-flat float-right"
                  @click.prevent="logout"
                  >Logout</a
                >
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <Link href="/admin/dashboard" class="brand-link">
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
                        alt="Admin Logo"
                        class="brand-image img-circle elevation-3"
                        style="opacity: 0.8"
                    >
                    <span class="brand-text font-weight-light">PEACEMAIN</span>
                </Link>

                <div class="sidebar">
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            <img :src="avatarSrc" class="img-circle elevation-2" alt="Admin">
                        </div>
                        <div class="info">
                            <Link href="/admin/profile" class="d-block">
                                {{ page.props.auth?.user?.full_name || page.props.auth?.user?.email }}
                            </Link>
                        </div>
                    </div>

                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-accordion="false">
                            <li v-for="item in adminMenus" :key="item.href" class="nav-item">
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
                            
                            <li class="nav-item" :class="{ 'menu-open': openGroups.documents }">
                                <a href="#" class="nav-link" :class="{ active: groupIsActive(documentMenus) }" @click.prevent="toggleGroup('documents')">
                                    <i class="nav-icon fas fa-folder"></i>
                                    <p>
                                        Document Manage
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li v-for="item in documentMenus" :key="item.href" class="nav-item">
                                        <Link
                                            :href="item.href"
                                            class="nav-link"
                                            :class="{ active: isActive(item) }"
                                            @click="closeSidebarOnMobile"
                                        >
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>{{ item.label }}</p>
                                        </Link>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item" :class="{ 'menu-open': openGroups.content }">
                                <a href="#" class="nav-link" :class="{ active: groupIsActive(contentMenus) }" @click.prevent="toggleGroup('content')">
                                    <i class="nav-icon fas fa-copy"></i>
                                    <p>
                                        Content Manage
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li v-for="item in contentMenus" :key="item.href" class="nav-item">
                                        <Link
                                            :href="item.href"
                                            class="nav-link"
                                            :class="{ active: isActive(item) }"
                                            @click="closeSidebarOnMobile"
                                        >
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>{{ item.label }}</p>
                                        </Link>
                                    </li>
                                </ul>
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
          <b>Panel</b> Admin
        </div>
      </footer>
    </div>
  </div>
</template>

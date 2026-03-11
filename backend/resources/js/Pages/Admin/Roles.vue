<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({
    roles: {
        type: Array,
        required: true,
    },
    admins: {
        type: Array,
        required: true,
    },
    users: {
        type: Array,
        required: true,
    },
});

const search = ref('');
const selectedUserId = ref('');
const promoteForm = useForm({});

const filteredUsers = computed(() => {
    const term = search.value.trim().toLowerCase();

    if (!term) {
        return props.users;
    }

    return props.users.filter((user) =>
        [user.full_name, user.email, user.phone, user.country]
            .filter(Boolean)
            .some((value) => value.toLowerCase().includes(term))
    );
});

const selectedUser = computed(() => {
    return props.users.find((user) => String(user.id) === String(selectedUserId.value)) || null;
});

const promoteUser = () => {
    if (!selectedUserId.value) {
        return;
    }

    promoteForm.patch(`/admin/roles/${selectedUserId.value}/promote`, {
        preserveScroll: true,
        onSuccess: () => {
            selectedUserId.value = '';
            search.value = '';
        },
    });
};
</script>

<template>
    <Head title="Role Manage" />
    <AdminLayout>
        <div class="adminlte-page-header">
            <div>
                <h1 class="m-0">Role Manage</h1>
                <p class="text-muted mb-0">Search users, promote them to admin, and review the current admin list.</p>
            </div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><Link href="/admin/dashboard">Home</Link></li>
                <li class="breadcrumb-item active">Role Manage</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-4 col-12">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ roles[0]?.users || 0 }}</h3>
                        <p>Admins</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ roles[1]?.users || 0 }}</h3>
                        <p>Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ filteredUsers.length }}</h3>
                        <p>Search Results</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Promote User To Admin</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Search User</label>
                            <input
                                v-model="search"
                                type="text"
                                class="form-control"
                                placeholder="Search by name, email, phone, country"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Select User</label>
                            <select v-model="selectedUserId" class="form-select">
                                <option value="">Choose user</option>
                                <option v-for="user in filteredUsers" :key="user.id" :value="user.id">
                                    {{ user.full_name }} - {{ user.email }}
                                </option>
                            </select>
                        </div>

                        <div v-if="selectedUser" class="callout callout-info">
                            <h5 class="mb-1">{{ selectedUser.full_name }}</h5>
                            <p class="mb-1">{{ selectedUser.email }}</p>
                            <p class="mb-0 text-muted">{{ selectedUser.phone || 'No phone' }} | {{ selectedUser.country || 'No country' }}</p>
                        </div>

                        <button
                            type="button"
                            class="btn btn-primary"
                            :disabled="!selectedUserId || promoteForm.processing"
                            @click="promoteUser"
                        >
                            Make Admin
                        </button>
                    </div>
                </div>

            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Admin List</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Country</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="admin in admins" :key="admin.id">
                                    <td>{{ admin.full_name }}</td>
                                    <td>{{ admin.email }}</td>
                                    <td>{{ admin.phone || 'N/A' }}</td>
                                    <td>{{ admin.country || 'N/A' }}</td>
                                </tr>
                                <tr v-if="!admins.length">
                                    <td colspan="4" class="text-center text-muted">No admin users found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Role Summary</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Users</th>
                                    <th>Permissions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="role in roles" :key="role.name">
                                    <td>{{ role.name }}</td>
                                    <td>{{ role.users }}</td>
                                    <td>{{ role.permissions }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
    countries: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const filterForm = useForm({
    country: props.filters.country || '',
    status: props.filters.status || '',
    completion: props.filters.completion || '',
});

const applyFilters = () => {
    router.get('/admin/users', filterForm.data(), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="User Manage" />
    <AdminLayout>
        <h1 class="h3 mb-3">User Manage</h1>

        <div class="card mb-3">
            <div class="card-body">
                <form class="row g-2" @submit.prevent="applyFilters">
                    <div class="col-md-4">
                        <select v-model="filterForm.country" class="form-select">
                            <option value="">Filter by country</option>
                            <option v-for="country in countries" :key="country" :value="country">{{ country }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select v-model="filterForm.status" class="form-select">
                            <option value="">All status</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                            <option value="active">Active</option>
                            <option value="banned">Banned</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input v-model="filterForm.completion" class="form-control" type="number" min="0" max="100" placeholder="Min completion" />
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Apply</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Users List</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Country</th>
                            <th>Role</th>
                            <th>Account</th>
                            <th>Completion</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>{{ user.id }}</td>
                            <td>{{ user.full_name }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.phone }}</td>
                            <td>{{ user.country }}</td>
                            <td>{{ user.is_admin ? 'Admin' : 'User' }}</td>
                            <td>
                                <span class="badge" :class="user.is_banned ? 'bg-danger' : 'bg-success'">
                                    {{ user.is_banned ? 'Banned' : 'Active' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge" :class="user.completion_percentage >= 100 ? 'bg-success' : 'bg-warning text-dark'">
                                    {{ user.completion_percentage }}%
                                </span>
                            </td>
                            <td>
                                <Link class="btn btn-sm btn-outline-primary" :href="`/admin/users/${user.id}`">View</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

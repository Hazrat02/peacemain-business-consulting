<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

defineProps({
    users: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="User Manage" />
    <AdminLayout>
        <h1 class="h3 mb-3">User Manage</h1>

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
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

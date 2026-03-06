<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

defineProps({
    roles: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    role_name: '',
    permissions: '',
});
</script>

<template>
    <Head title="Role Manage" />
    <AdminLayout>
        <h1 class="h3 mb-3">Role Manage</h1>

        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Roles</h5>
                    </div>
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
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add Role</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Role Name</label>
                                <input v-model="form.role_name" class="form-control" type="text" placeholder="Manager" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Permissions</label>
                                <textarea v-model="form.permissions" class="form-control" rows="4" placeholder="Users, Content, Contact"></textarea>
                            </div>
                            <button type="button" class="btn btn-primary">Save Role</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

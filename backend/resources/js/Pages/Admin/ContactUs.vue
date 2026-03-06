<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

defineProps({
    contacts: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Contact Us" />
    <AdminLayout>
        <h1 class="h3 mb-3">Contact-us</h1>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Contact Queries</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="contact in contacts" :key="contact.id">
                            <td>{{ contact.id }}</td>
                            <td>{{ contact.name }}</td>
                            <td>{{ contact.email }}</td>
                            <td>{{ contact.subject }}</td>
                            <td>{{ contact.status }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

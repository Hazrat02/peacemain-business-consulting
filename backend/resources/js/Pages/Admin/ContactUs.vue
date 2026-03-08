<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';
import { reactive } from 'vue';

const props = defineProps({
    contacts: {
        type: Array,
        required: true,
    },
});

const statusMap = reactive(
    props.contacts.reduce((acc, item) => {
        acc[item.id] = item.status ?? 'New';
        return acc;
    }, {})
);

const saveStatus = (id) => {
    router.patch(`/admin/contact-us/${id}`, { status: statusMap[id] }, { preserveScroll: true });
};
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
                            <th>Message</th>
                            <th>Received</th>
                            <th>Read</th>
                            <th>Replied</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="contact in contacts" :key="contact.id">
                            <td>{{ contact.id }}</td>
                            <td>{{ contact.name }}</td>
                            <td>{{ contact.email }}</td>
                            <td>{{ contact.subject }}</td>
                            <td style="min-width: 260px">{{ contact.message }}</td>
                            <td>{{ contact.created_at }}</td>
                            <td>
                                <span :class="contact.is_read ? 'badge bg-success' : 'badge bg-warning text-dark'">
                                    {{ contact.is_read ? 'Read' : 'Unseen' }}
                                </span>
                            </td>
                            <td>{{ contact.replied_at || '-' }}</td>
                            <td style="min-width: 210px">
                                <div class="d-flex gap-2">
                                    <select v-model="statusMap[contact.id]" class="form-select form-select-sm">
                                        <option>New</option>
                                        <option>In Progress</option>
                                        <option>Resolved</option>
                                    </select>
                                    <button type="button" class="btn btn-sm btn-primary" @click="saveStatus(contact.id)">Save</button>
                                </div>
                            </td>
                            <td>
                                <Link class="btn btn-sm btn-outline-primary" :href="`/admin/contact-us/${contact.id}/reply`">Reply</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

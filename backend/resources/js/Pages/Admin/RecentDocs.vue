<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

defineProps({
    submissions: {
        type: Object,
        required: true,
    },
});

const badgeClass = (status) => {
    if (status === 'approved') return 'bg-success';
    if (status === 'rejected') return 'bg-danger';
    if (status === 'pending') return 'bg-warning text-dark';
    return 'bg-secondary';
};
</script>

<template>
    <Head title="Recent Docs" />
    <AdminLayout>
        <h1 class="h3 mb-3">Recent docs</h1>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">All Submitted Documents</h5>
                <small class="text-muted">Total: {{ submissions.total }}</small>
            </div>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Document</th>
                            <th>File</th>
                            <th>Version</th>
                            <th>Status</th>
                            <th>Submitted At</th>
                            <th>Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in submissions.data" :key="row.id">
                            <td>{{ row.id }}</td>
                            <td>
                                <div class="fw-semibold">{{ row.user?.full_name || 'Unknown User' }}</div>
                                <small class="text-muted">{{ row.user?.email || '-' }}</small>
                            </td>
                            <td>{{ row.document_title || '-' }}</td>
                            <td>
                                <a :href="row.file_url">{{ row.file_name }}</a>
                            </td>
                            <td>v{{ row.version }}</td>
                            <td>
                                <span class="badge text-uppercase" :class="badgeClass(row.review_status)">
                                    {{ row.review_status }}
                                </span>
                            </td>
                            <td>{{ row.created_at || '-' }}</td>
                            <td>
                                <Link
                                    v-if="row.user?.id"
                                    class="btn btn-sm btn-outline-primary"
                                    :href="`/admin/document-checklists/${row.user.id}`"
                                >
                                    Review
                                </Link>
                                <span v-else class="text-muted small">-</span>
                            </td>
                        </tr>
                        <tr v-if="!submissions.data.length">
                            <td colspan="8" class="text-center text-muted py-3">No submitted documents found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex flex-wrap gap-2">
                <Link
                    v-for="link in submissions.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    class="btn btn-sm"
                    :class="link.active ? 'btn-primary' : 'btn-outline-secondary'"
                    :disabled="!link.url"
                    v-html="link.label"
                />
            </div>
        </div>
    </AdminLayout>
</template>

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
    router.get('/admin/document-checklists', filterForm.data(), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="User Document Reviews" />
    <AdminLayout>
        <h1 class="h3 mb-3">User Document Reviews</h1>

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
                            <option value="completed">Completed (100%)</option>
                            <option value="pending">Has pending</option>
                            <option value="rejected">Has rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input v-model="filterForm.completion" class="form-control" type="number" min="0" max="100" placeholder="Min completion %" />
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Apply</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Country</th>
                            <th>Completion</th>
                            <th>Required</th>
                            <th>Approved</th>
                            <th>Pending</th>
                            <th>Rejected</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>
                                <div class="fw-semibold">{{ user.full_name }}</div>
                                <small class="text-muted">{{ user.email }}</small>
                            </td>
                            <td>{{ user.country || '-' }}</td>
                            <td>
                                <span class="badge" :class="user.progress.completion_percentage >= 100 ? 'bg-success' : 'bg-warning'">
                                    {{ user.progress.completion_percentage }}%
                                </span>
                            </td>
                            <td>{{ user.progress.required }}</td>
                            <td>{{ user.progress.approved + user.progress.waived }}</td>
                            <td>{{ user.progress.pending }}</td>
                            <td>{{ user.progress.rejected }}</td>
                            <td class="text-end">
                                <Link class="btn btn-sm btn-outline-primary" :href="`/admin/document-checklists/${user.id}`">Review</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

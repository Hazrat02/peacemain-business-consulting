<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    label: '',
    url: '',
    status: 'Active',
});
</script>

<template>
    <Head title="Content - Sidebar" />
    <AdminLayout>
        <h1 class="h3 mb-3">Content Manage / Sidebar</h1>

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Sidebar Link Form</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Label</label>
                                <input v-model="form.label" class="form-control" type="text" placeholder="Application Status" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL</label>
                                <input v-model="form.url" class="form-control" type="text" placeholder="/dashboard/overseas" />
                            </div>
                            <button type="button" class="btn btn-primary">Save Link</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Sidebar Links</h5>
                    </div>
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>URL</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="link in links" :key="link.label">
                                <td>{{ link.label }}</td>
                                <td>{{ link.url }}</td>
                                <td>{{ link.status }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

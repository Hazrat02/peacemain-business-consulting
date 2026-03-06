<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineProps({
    banners: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    title: '',
    subtitle: '',
    position: 'Home Top',
    status: 'Active',
});
</script>

<template>
    <Head title="Content - Banner" />
    <AdminLayout>
        <h1 class="h3 mb-3">Content Manage / Banner</h1>

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Banner Form</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input v-model="form.title" class="form-control" type="text" placeholder="Study in UK" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subtitle</label>
                                <textarea v-model="form.subtitle" class="form-control" rows="3" placeholder="Start your global education journey"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Position</label>
                                <select v-model="form.position" class="form-select">
                                    <option>Home Top</option>
                                    <option>Overseas Page</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-primary">Save Banner</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Existing Banners</h5>
                    </div>
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Position</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="banner in banners" :key="banner.title">
                                <td>{{ banner.title }}</td>
                                <td>{{ banner.position }}</td>
                                <td>{{ banner.status }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

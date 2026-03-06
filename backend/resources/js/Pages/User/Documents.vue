<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import UserLayout from '../../Layouts/UserLayout.vue';

defineProps({
    documents: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    title: '',
    type: '',
});
</script>

<template>
    <Head title="Documents" />
    <UserLayout>
        <h1 class="h3 mb-3">Documents</h1>

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Upload Document</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Document Title</label>
                                <input v-model="form.title" class="form-control" type="text" placeholder="Passport Copy" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <input v-model="form.type" class="form-control" type="text" placeholder="Identity" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">File</label>
                                <input class="form-control" type="file" />
                            </div>
                            <button type="button" class="btn btn-primary">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Document List</h5>
                    </div>
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="document in documents" :key="document.name">
                                <td>{{ document.name }}</td>
                                <td>{{ document.type }}</td>
                                <td>{{ document.status }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

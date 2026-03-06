<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

const props = defineProps({
    info: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    phone: props.info.phone,
    email: props.info.email,
    address: props.info.address,
    map_url: props.info.map_url,
});
</script>

<template>
    <Head title="Content - Contact Info" />
    <AdminLayout>
        <h1 class="h3 mb-3">Content Manage / Contact Us Info</h1>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Contact Information Form</h5>
            </div>
            <div class="card-body">
                <form class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input v-model="form.phone" class="form-control" type="text" placeholder="+1-234-567-8901" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input v-model="form.email" class="form-control" type="email" placeholder="support@example.com" />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Address</label>
                        <input v-model="form.address" class="form-control" type="text" placeholder="25 Market Street, San Francisco, CA" />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Google Map URL</label>
                        <input v-model="form.map_url" class="form-control" type="text" placeholder="https://maps.google.com" />
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-primary">Save Contact Info</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

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
    map_embed_url: props.info.map_embed_url ?? '',
    welcome_subject: props.info.welcome_subject ?? 'Welcome to PEACEMAIN',
    welcome_message: props.info.welcome_message ?? '',
});

const saveContactInfo = () => {
    form.put('/admin/content/contact-info', {
        preserveScroll: true,
    });
};
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
                <form class="row" @submit.prevent="saveContactInfo">
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
                    <div class="col-12 mb-3">
                        <label class="form-label">Google Map Embed URL</label>
                        <textarea v-model="form.map_embed_url" class="form-control" rows="3" placeholder="https://www.google.com/maps/embed?..."></textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Welcome Mail Subject</label>
                        <input v-model="form.welcome_subject" class="form-control" type="text" placeholder="Welcome to PEACEMAIN" />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Welcome Message</label>
                        <textarea v-model="form.welcome_message" class="form-control" rows="4" placeholder="Thanks for contacting us..."></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Save Contact Info' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

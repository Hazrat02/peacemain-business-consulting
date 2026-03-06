<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';
import { COUNTRIES } from '../../constants/countries';

const props = defineProps({
    settings: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    site_name: props.settings.site_name,
    timezone: props.settings.timezone,
    default_country: props.settings.default_country,
    support_email: props.settings.support_email,
});
</script>

<template>
    <Head title="Settings" />
    <AdminLayout>
        <h1 class="h3 mb-3">Settings</h1>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">General Settings</h5>
            </div>
            <div class="card-body">
                <form class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Site Name</label>
                        <input v-model="form.site_name" class="form-control" type="text" placeholder="PeaceMain" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Timezone</label>
                        <input v-model="form.timezone" class="form-control" type="text" placeholder="America/Los_Angeles" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Default Country</label>
                        <select v-model="form.default_country" class="form-select">
                            <option value="">Select country</option>
                            <option v-for="country in COUNTRIES" :key="country" :value="country">{{ country }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Support Email</label>
                        <input v-model="form.support_email" class="form-control" type="email" placeholder="support@example.com" />
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

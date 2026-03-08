<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';
import { COUNTRIES } from '../../constants/countries';
import { computed, ref } from 'vue';

const props = defineProps({
    settings: {
        type: Object,
        required: true,
    },
    smtp: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    site_name: props.settings.site_name,
    timezone: props.settings.timezone,
    default_country: props.settings.default_country,
    support_email: props.settings.support_email,
    logo: null,
    smtp_host: props.smtp.host ?? '',
    smtp_port: props.smtp.port ?? 587,
    smtp_username: props.smtp.username ?? '',
    smtp_password: props.smtp.password ?? '',
    smtp_encryption: props.smtp.encryption ?? 'tls',
    from_email: props.smtp.from_email ?? '',
    from_name: props.smtp.from_name ?? '',
    mail_template_html: props.smtp.mail_template_html ?? '',
});

const logoPreview = ref(props.settings.logo_url || '');

const templatePreview = computed(() => form.mail_template_html || '<div style="padding:16px;font-family:Arial">Template preview...</div>');

const saveSettings = () => {
    form
        .transform((data) => ({
            ...data,
            _method: 'put',
        }))
        .post('/admin/settings', { preserveScroll: true, forceFormData: true });
};

const onLogoChange = (event) => {
    const [file] = event.target.files || [];
    form.logo = file || null;
    logoPreview.value = file ? URL.createObjectURL(file) : (props.settings.logo_url || '');
};
</script>

<template>
    <Head title="Settings" />
    <AdminLayout>
        <h1 class="h3 mb-3">Settings</h1>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">General Settings</h5>
            </div>
            <div class="card-body">
                <form class="row" @submit.prevent="saveSettings">
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
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Admin Sidebar Logo</label>
                        <input class="form-control" type="file" accept="image/*" @change="onLogoChange" />
                        <div v-if="form.errors.logo" class="text-danger mt-1">{{ form.errors.logo }}</div>
                        <div v-if="logoPreview" class="mt-2">
                            <img :src="logoPreview" alt="Logo preview" class="rounded border" style="max-height: 70px; width: auto;" />
                        </div>
                    </div>
                    <div class="col-12">
                        <h5 class="mt-3">SMTP Settings</h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">SMTP Host</label>
                        <input v-model="form.smtp_host" class="form-control" type="text" placeholder="smtp.gmail.com" />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">SMTP Port</label>
                        <input v-model="form.smtp_port" class="form-control" type="number" min="1" placeholder="587" />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Encryption</label>
                        <select v-model="form.smtp_encryption" class="form-select">
                            <option value="tls">tls</option>
                            <option value="ssl">ssl</option>
                            <option value="none">none</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">SMTP Username</label>
                        <input v-model="form.smtp_username" class="form-control" type="text" placeholder="username" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">SMTP Password</label>
                        <input v-model="form.smtp_password" class="form-control" type="text" placeholder="password or app password" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">From Email</label>
                        <input v-model="form.from_email" class="form-control" type="email" placeholder="info@example.com" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">From Name</label>
                        <input v-model="form.from_name" class="form-control" type="text" placeholder="PEACEMAIN" />
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Demo Mail Template HTML</label>
                        <textarea v-model="form.mail_template_html" class="form-control" rows="8" placeholder="<div>Welcome [[name]]</div>"></textarea>
                        <small class="text-muted">Available placeholders: [[name]], [[email]], [[subject]], [[message]], [[welcome_message]], [[from_name]]</small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Template Preview</label>
                        <iframe class="w-100 border rounded" style="height: 280px" :srcdoc="templatePreview"></iframe>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Save Settings' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

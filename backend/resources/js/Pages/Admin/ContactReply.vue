<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({
    contact: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    subject: `Re: ${props.contact.subject || ''}`,
    message: '',
    mark_resolved: true,
});

const submit = () => {
    form.post(`/admin/contact-us/${props.contact.id}/reply`);
};
</script>

<template>
    <Head :title="`Reply: ${contact.name}`" />
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Reply Contact</h1>
            <Link href="/admin/contact-us" class="btn btn-outline-secondary btn-sm">Back</Link>
        </div>

        <div class="row g-3">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Contact Details</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Name:</strong> {{ contact.name }}</p>
                        <p class="mb-2"><strong>Email:</strong> {{ contact.email }}</p>
                        <p class="mb-2"><strong>Subject:</strong> {{ contact.subject }}</p>
                        <p class="mb-2"><strong>Received:</strong> {{ contact.created_at }}</p>
                        <p class="mb-2">
                            <strong>Read:</strong>
                            <span :class="contact.is_read ? 'badge bg-success' : 'badge bg-warning text-dark'">
                                {{ contact.is_read ? 'Read' : 'Unseen' }}
                            </span>
                        </p>
                        <p class="mb-0"><strong>Message:</strong></p>
                        <div class="border rounded p-2 mt-2">{{ contact.message }}</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Send Reply</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label class="form-label">Reply Subject</label>
                                <input v-model="form.subject" type="text" class="form-control" required />
                                <div v-if="form.errors.subject" class="text-danger small mt-1">{{ form.errors.subject }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Reply Message</label>
                                <textarea v-model="form.message" rows="8" class="form-control" required></textarea>
                                <div v-if="form.errors.message" class="text-danger small mt-1">{{ form.errors.message }}</div>
                            </div>

                            <div class="form-check mb-3">
                                <input id="mark-resolved" v-model="form.mark_resolved" type="checkbox" class="form-check-input" />
                                <label class="form-check-label" for="mark-resolved">Mark status as Resolved after sending</label>
                            </div>

                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                {{ form.processing ? 'Sending...' : 'Send Reply' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>


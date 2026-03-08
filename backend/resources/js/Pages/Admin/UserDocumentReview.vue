<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    progress: {
        type: Object,
        required: true,
    },
    requirements: {
        type: Array,
        required: true,
    },
});

const forms = {};

const getReviewForm = (requirementId) => {
    if (!forms[requirementId]) {
        forms[requirementId] = useForm({
            action: 'approved',
            note: '',
        });
    }

    return forms[requirementId];
};

const submitReview = (requirementId) => {
    const form = getReviewForm(requirementId);
    form.post(`/admin/document-checklists/${requirementId}/review`, {
        preserveScroll: true,
    });
};

const statusBadge = (status) => {
    const map = {
        missing: 'bg-secondary',
        uploaded: 'bg-info',
        under_review: 'bg-warning',
        approved: 'bg-success',
        rejected: 'bg-danger',
        waived: 'bg-primary',
    };

    return map[status] || 'bg-secondary';
};
</script>

<template>
    <Head :title="`Review - ${user.full_name}`" />
    <AdminLayout>
        <h1 class="h3 mb-3">Review Checklist: {{ user.full_name }}</h1>

        <div class="card mb-3">
            <div class="card-body">
                <p class="mb-1"><strong>Email:</strong> {{ user.email }}</p>
                <p class="mb-1"><strong>Country:</strong> {{ user.country || '-' }}</p>
                <p class="mb-0">
                    <strong>Progress:</strong>
                    {{ progress.completion_percentage }}%
                    ({{ progress.approved + progress.waived }} / {{ progress.required }})
                </p>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Document</th>
                            <th>Status</th>
                            <th>Latest Submission</th>
                            <th>History</th>
                            <th style="min-width: 280px;">Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in requirements" :key="item.id">
                            <td>
                                <div class="fw-semibold">{{ item.document.title }}</div>
                                <small class="text-muted">{{ item.document.section }}</small>
                            </td>
                            <td>
                                <span class="badge text-uppercase" :class="statusBadge(item.status)">
                                    {{ item.status.replaceAll('_', ' ') }}
                                </span>
                            </td>
                            <td>
                                <template v-if="item.latest_submission">
                                    <a :href="item.latest_submission.file_url">
                                        {{ item.latest_submission.file_name }}
                                    </a>
                                    <div class="small text-muted">
                                        v{{ item.latest_submission.version }} | {{ item.latest_submission.created_at }}
                                    </div>
                                    <div v-if="item.latest_submission.review_note" class="small text-danger">
                                        Note: {{ item.latest_submission.review_note }}
                                    </div>
                                </template>
                                <span v-else class="text-muted">No submission</span>
                            </td>
                            <td>
                                <ul class="mb-0">
                                    <li v-for="history in item.history" :key="history.id" class="small">
                                        <a :href="history.file_url">{{ history.file_name }}</a>
                                        <span class="text-muted"> | v{{ history.version }} - {{ history.review_status }}</span>
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <form @submit.prevent="submitReview(item.id)">
                                    <div class="mb-2">
                                        <select v-model="getReviewForm(item.id).action" class="form-select form-select-sm">
                                            <option value="approved">Approve</option>
                                            <option value="rejected">Reject</option>
                                            <option value="waived">Waive / Not Required</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <textarea
                                            v-model="getReviewForm(item.id).note"
                                            class="form-control form-control-sm"
                                            rows="2"
                                            placeholder="Rejection note or waiver reason"
                                        ></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary" :disabled="getReviewForm(item.id).processing">
                                        Save
                                    </button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

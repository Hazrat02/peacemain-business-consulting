<script setup>
import { reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import UserLayout from '../../Layouts/UserLayout.vue';

defineProps({
    sections: {
        type: Array,
        required: true,
    },
    progress: {
        type: Object,
        required: true,
    },
});

const uploadState = reactive({});

const badgeClass = (status) => {
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

const statusLabel = (status) => status.replaceAll('_', ' ');
const canUpload = (item) => item.status !== 'approved';

const uploadDocument = (requirementId, event) => {
    const file = event?.target?.files?.[0];
    if (!file) {
        return;
    }

    uploadState[requirementId] = true;

    router.post(
        `/dashboard/documents/${requirementId}/submissions`,
        { file },
        {
            forceFormData: true,
            preserveScroll: true,
            onFinish: () => {
                uploadState[requirementId] = false;
                event.target.value = '';
            },
        }
    );
};
</script>

<template>
    <Head title="Documents" />
    <UserLayout>
        <h1 class="h3 mb-3">Document Checklist</h1>

        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-1 text-muted">Required</h6>
                        <h3 class="mb-0">{{ progress.required }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-1 text-muted">Approved</h6>
                        <h3 class="mb-0">{{ progress.approved }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-1 text-muted">Pending</h6>
                        <h3 class="mb-0">{{ progress.pending }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-1 text-muted">Rejected</h6>
                        <h3 class="mb-0">{{ progress.rejected }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Completion</h5>
                <div class="progress mb-2" style="height: 10px;">
                    <div
                        class="progress-bar"
                        role="progressbar"
                        :style="{ width: `${progress.completion_percentage}%` }"
                        :aria-valuenow="progress.completion_percentage"
                        aria-valuemin="0"
                        aria-valuemax="100"
                    ></div>
                </div>
                <p class="mb-0">
                    {{ progress.completion_percentage }}%
                    ({{ progress.approved + progress.waived }} of {{ progress.required }} required completed)
                </p>
            </div>
        </div>

        <div v-for="section in sections" :key="section.name" class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ section.name }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Document</th>
                            <th>Status</th>
                            <th>Latest Upload</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in section.documents" :key="item.requirement_id">
                            <td>
                                <div class="fw-semibold">{{ item.title }}</div>
                                <small class="text-muted">{{ item.description || (item.is_required ? 'Required' : 'Optional') }}</small>
                                <div v-if="item.status === 'rejected' && item.latest_submission?.review_note" class="text-danger mt-1">
                                    Rejection Note: {{ item.latest_submission.review_note }}
                                </div>
                                <details v-if="item.history?.length" class="mt-2">
                                    <summary class="text-muted">Upload history</summary>
                                    <ul class="mb-0 mt-1">
                                        <li v-for="history in item.history" :key="history.id" class="small">
                                            v{{ history.version }} - {{ history.file_name }} - {{ statusLabel(history.review_status) }}
                                        </li>
                                    </ul>
                                </details>
                            </td>
                            <td>
                                <span class="badge text-uppercase" :class="badgeClass(item.status)">
                                    {{ statusLabel(item.status) }}
                                </span>
                            </td>
                            <td>
                                <template v-if="item.latest_submission">
                                    <a :href="item.latest_submission.file_url">
                                        {{ item.latest_submission.file_name }}
                                    </a>
                                    <div class="small text-muted">v{{ item.latest_submission.version }}</div>
                                </template>
                                <span v-else class="text-muted">No file</span>
                            </td>
                            <td>
                                <template v-if="canUpload(item)">
                                    <label class="btn btn-sm btn-outline-primary mb-0">
                                        {{ uploadState[item.requirement_id] ? 'Uploading...' : 'Upload / Re-upload' }}
                                        <input
                                            class="d-none"
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                            :disabled="uploadState[item.requirement_id]"
                                            @change="uploadDocument(item.requirement_id, $event)"
                                        />
                                    </label>
                                </template>
                                <span v-else class="badge bg-success">Approved - Locked</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </UserLayout>
</template>

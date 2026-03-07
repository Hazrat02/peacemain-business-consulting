<script setup>
import { Head } from '@inertiajs/vue3';
import UserLayout from '../../Layouts/UserLayout.vue';

defineProps({
    stats: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="User Dashboard" />
    <UserLayout>
        <h1 class="h3 mb-3"><strong>User</strong> Dashboard</h1>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Applications</h5>
                        <h1 class="mt-1 mb-2">{{ stats.applications }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Approved Documents</h5>
                        <h1 class="mt-1 mb-2">{{ stats.approved_documents }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pending Documents</h5>
                        <h1 class="mt-1 mb-2">{{ stats.pending_documents }}</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Document Completion</h5>
                <div class="progress mb-2" style="height: 10px;">
                    <div
                        class="progress-bar"
                        role="progressbar"
                        :style="{ width: `${stats.completion_percentage}%` }"
                        :aria-valuenow="stats.completion_percentage"
                        aria-valuemin="0"
                        aria-valuemax="100"
                    ></div>
                </div>
                <p class="mb-0">
                    {{ stats.completion_percentage }}% complete
                    (Approved + Waived: {{ stats.approved_documents + stats.waived_documents }} / {{ stats.required_documents }})
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Welcome</h5>
                <p class="mb-0">Track your documents and overseas applications from the left menu.</p>
            </div>
        </div>
    </UserLayout>
</template>

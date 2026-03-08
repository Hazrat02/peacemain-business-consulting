<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

defineProps({
    user: {
        type: Object,
        required: true,
    },
    document_summary: {
        type: Object,
        required: true,
    },
    recent_submissions: {
        type: Array,
        required: true,
    },
});

const emptyText = (value) => value || '-';
const statusBadgeClass = (status) => {
    if (status === 'approved') return 'bg-success';
    if (status === 'rejected') return 'bg-danger';
    if (status === 'waived') return 'bg-info';
    if (status === 'missing') return 'bg-secondary';
    return 'bg-warning text-dark';
};

const loginAsForm = useForm({});
const banForm = useForm({});

const loginAsUser = (userId) => {
    loginAsForm.post(`/admin/users/${userId}/login-as`);
};

const toggleBan = (userId) => {
    banForm.patch(`/admin/users/${userId}/ban`, { preserveScroll: true });
};
</script>

<template>
    <Head :title="`User Profile: ${user.full_name || user.name || user.email}`" />
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">User Profile</h1>
            <div class="d-flex gap-2">
                <button
                    type="button"
                    class="btn btn-sm btn-primary"
                    :disabled="loginAsForm.processing || user.is_banned || user.is_admin"
                    @click="loginAsUser(user.id)"
                >
                    {{ loginAsForm.processing ? 'Logging in...' : 'LogIn As User' }}
                </button>
                <Link class="btn btn-sm btn-outline-info" :href="`/admin/document-checklists/${user.id}`">View Document</Link>
                <button
                    type="button"
                    class="btn btn-sm"
                    :class="user.is_banned ? 'btn-outline-success' : 'btn-outline-danger'"
                    :disabled="banForm.processing || user.is_admin"
                    @click="toggleBan(user.id)"
                >
                    {{ banForm.processing ? 'Updating...' : (user.is_banned ? 'Unban' : 'Ban') }}
                </button>
                <Link href="/admin/users" class="btn btn-outline-secondary btn-sm">Back to Users</Link>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-4">
                <div class="card profile-card h-100">
                    <div class="card-body text-center">
                        <img :src="user.profile_image_url" :alt="user.full_name || 'User'" class="profile-image mb-3" />
                        <h4 class="mb-1">{{ user.full_name || user.name || '-' }}</h4>
                        <p class="text-muted mb-2">{{ user.email }}</p>
                        <div class="d-flex justify-content-center gap-2">
                            <span class="badge" :class="user.is_admin ? 'bg-primary' : 'bg-dark'">
                                {{ user.is_admin ? 'Admin' : 'User' }}
                            </span>
                            <span class="badge" :class="user.is_banned ? 'bg-danger' : 'bg-success'">
                                {{ user.is_banned ? 'Banned' : 'Active' }}
                            </span>
                        </div>

                        <hr class="my-4" />

                        <div class="text-start small">
                            <div class="row mb-2">
                                <div class="col-5 text-muted">User ID</div>
                                <div class="col-7 fw-semibold">#{{ user.id }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 text-muted">Phone</div>
                                <div class="col-7 fw-semibold">{{ emptyText(user.phone) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 text-muted">Country</div>
                                <div class="col-7 fw-semibold">{{ emptyText(user.country) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 text-muted">Destination</div>
                                <div class="col-7 fw-semibold">{{ emptyText(user.destination_country) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 text-muted">Created</div>
                                <div class="col-7 fw-semibold">{{ emptyText(user.created_at) }}</div>
                            </div>
                            <div class="row">
                                <div class="col-5 text-muted">Updated</div>
                                <div class="col-7 fw-semibold">{{ emptyText(user.updated_at) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="card metric-card border-start border-4 border-primary">
                            <div class="card-body">
                                <div class="small text-muted">Total Required Docs</div>
                                <div class="h3 mb-0">{{ document_summary.total }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card metric-card border-start border-4 border-success">
                            <div class="card-body">
                                <div class="small text-muted">Approved Docs</div>
                                <div class="h3 mb-0">{{ document_summary.approved }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card metric-card border-start border-4 border-danger">
                            <div class="card-body">
                                <div class="small text-muted">Rejected Docs</div>
                                <div class="h3 mb-0">{{ document_summary.rejected }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="card metric-card border-start border-4 border-secondary">
                            <div class="card-body">
                                <div class="small text-muted">Missing Docs</div>
                                <div class="h3 mb-0">{{ document_summary.missing }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card metric-card border-start border-4 border-info">
                            <div class="card-body">
                                <div class="small text-muted">Waived Docs</div>
                                <div class="h3 mb-0">{{ document_summary.waived }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card metric-card border-start border-4 border-warning">
                            <div class="card-body">
                                <div class="small text-muted">Total Submissions</div>
                                <div class="h3 mb-0">{{ document_summary.submissions_total }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Personal & Application Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="text-muted small">Full Name</div>
                                <div class="fw-semibold">{{ emptyText(user.full_name || user.name) }}</div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="text-muted small">Email</div>
                                <div class="fw-semibold">{{ emptyText(user.email) }}</div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="text-muted small">Marital Status</div>
                                <div class="fw-semibold">{{ emptyText(user.marital_status) }}</div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="text-muted small">Passport Type</div>
                                <div class="fw-semibold">{{ emptyText(user.passport_type) }}</div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="text-muted small">Email Verified At</div>
                                <div class="fw-semibold">{{ emptyText(user.email_verified_at) }}</div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="text-muted small">Account Type</div>
                                <div class="fw-semibold">{{ user.is_admin ? 'Admin' : 'Regular User' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Submissions</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>File</th>
                                    <th>Status</th>
                                    <th>Note</th>
                                    <th>Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in recent_submissions" :key="item.id">
                                    <td>{{ item.id }}</td>
                                    <td>{{ item.file_name }}</td>
                                    <td>
                                        <span class="badge" :class="statusBadgeClass(item.review_status)">
                                            {{ item.review_status }}
                                        </span>
                                    </td>
                                    <td>{{ emptyText(item.review_note) }}</td>
                                    <td>{{ emptyText(item.created_at) }}</td>
                                </tr>
                                <tr v-if="!recent_submissions.length">
                                    <td colspan="5" class="text-center text-muted py-3">No submissions yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.profile-card {
    border: 0;
    box-shadow: 0 0.35rem 1rem rgba(0, 0, 0, 0.08);
}

.profile-image {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 0.3rem 1rem rgba(0, 0, 0, 0.12);
}

.metric-card {
    border: 0;
    box-shadow: 0 0.2rem 0.8rem rgba(0, 0, 0, 0.06);
}
</style>

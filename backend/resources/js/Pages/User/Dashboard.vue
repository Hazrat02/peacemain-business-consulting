<script setup>
import { Head, Link } from '@inertiajs/vue3';
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
        <div class="adminlte-page-header">
            <div>
                <h1 class="m-0">Dashboard</h1>
                <p class="text-muted mb-0">Your existing application and document progress in the new AdminLTE design.</p>
            </div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><Link href="/dashboard">Home</Link></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info adminlte-stat-card">
                    <div class="inner">
                        <h3>{{ stats.applications }}</h3>
                        <p>Applications</p>
                    </div>
                    <div class="icon"><i class="fas fa-briefcase"></i></div>
                    <Link href="/dashboard/documents" class="small-box-footer">Open documents <i class="fas fa-arrow-circle-right"></i></Link>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success adminlte-stat-card">
                    <div class="inner">
                        <h3>{{ stats.approved_documents }}</h3>
                        <p>Approved</p>
                    </div>
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <Link href="/dashboard/documents" class="small-box-footer">Review uploads <i class="fas fa-arrow-circle-right"></i></Link>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning adminlte-stat-card">
                    <div class="inner">
                        <h3>{{ stats.pending_documents }}</h3>
                        <p>Pending</p>
                    </div>
                    <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                    <Link href="/dashboard/documents" class="small-box-footer">Track status <i class="fas fa-arrow-circle-right"></i></Link>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger adminlte-stat-card">
                    <div class="inner">
                        <h3>{{ stats.waived_documents }}</h3>
                        <p>Waived</p>
                    </div>
                    <div class="icon"><i class="fas fa-ban"></i></div>
                    <Link href="/dashboard/documents" class="small-box-footer">View checklist <i class="fas fa-arrow-circle-right"></i></Link>
                </div>
            </div>
        </div>

        <div class="row">
            <section class="col-lg-7 connectedSortable">
                <div class="card adminlte-progress-card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-tasks mr-1"></i>
                            Document Completion
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="progress mb-3">
                            <div
                                class="progress-bar bg-primary"
                                role="progressbar"
                                :style="{ width: `${stats.completion_percentage}%` }"
                                :aria-valuenow="stats.completion_percentage"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            ></div>
                        </div>
                        <p class="mb-2"><strong>{{ stats.completion_percentage }}%</strong> complete</p>
                        <p class="text-muted mb-0">
                            Approved + Waived: {{ stats.approved_documents + stats.waived_documents }} / {{ stats.required_documents }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-1"></i>
                            Overview
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">Track your documents and overseas applications from the left menu. All underlying logic and data flow remain unchanged.</p>
                    </div>
                </div>
            </section>

            <section class="col-lg-5 connectedSortable">
                <div class="card bg-gradient-primary">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-passport mr-1"></i>
                            Progress Summary
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="progress-group">
                            Approved documents
                            <span class="float-right"><b>{{ stats.approved_documents }}</b>/{{ stats.required_documents }}</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" :style="{ width: `${stats.required_documents ? Math.round((stats.approved_documents / stats.required_documents) * 100) : 0}%` }"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            Pending documents
                            <span class="float-right"><b>{{ stats.pending_documents }}</b>/{{ stats.required_documents }}</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" :style="{ width: `${stats.required_documents ? Math.round((stats.pending_documents / stats.required_documents) * 100) : 0}%` }"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <Link href="/dashboard/documents" class="text-white">Go to document center</Link>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-link mr-1"></i>
                            Quick Links
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <Link href="/dashboard/documents" class="nav-link">
                                    Upload documents
                                    <span class="float-right badge bg-primary">Open</span>
                                </Link>
                            </li>
                            <li class="nav-item">
                                <Link href="/profile" class="nav-link">
                                    Update profile
                                    <span class="float-right badge bg-success">Edit</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </UserLayout>
</template>

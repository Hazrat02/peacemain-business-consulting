<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

defineProps({
    stats: {
        type: Object,
        required: true,
    },
    recent_users: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <div class="adminlte-page-header">
            <div>
                <h1 class="m-0">Dashboard</h1>
                <p class="text-muted mb-0">Admin overview with the existing live data from your current system.</p>
            </div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><Link href="/admin/dashboard">Home</Link></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info adminlte-stat-card">
                    <div class="inner">
                        <h3>{{ stats.total_users }}</h3>
                        <p>Total Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <Link href="/admin/users" class="small-box-footer">
                        Manage users <i class="fas fa-arrow-circle-right"></i>
                    </Link>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success adminlte-stat-card">
                    <div class="inner">
                        <h3>{{ stats.admin_users }}</h3>
                        <p>Admin Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <Link href="/admin/roles" class="small-box-footer">
                        View roles <i class="fas fa-arrow-circle-right"></i>
                    </Link>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning adminlte-stat-card">
                    <div class="inner">
                        <h3>{{ stats.active_users }}</h3>
                        <p>Active in 7 Days</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <Link href="/admin/users" class="small-box-footer">
                        Review activity <i class="fas fa-arrow-circle-right"></i>
                    </Link>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger adminlte-stat-card">
                    <div class="inner">
                        <h3>{{ recent_users.length }}</h3>
                        <p>Newest Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <Link href="/admin/users" class="small-box-footer">
                        Open user list <i class="fas fa-arrow-circle-right"></i>
                    </Link>
                </div>
            </div>
        </div>

        <div class="row">
            <section class="col-lg-7 connectedSortable">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-users mr-1"></i>
                            Latest Users
                        </h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Country</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in recent_users" :key="user.id">
                                    <td>{{ user.full_name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>{{ user.country || 'N/A' }}</td>
                                    <td>
                                        <span class="badge" :class="user.is_admin ? 'badge-danger' : 'badge-success'">
                                            {{ user.is_admin ? 'Admin' : 'User' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!recent_users.length">
                                    <td colspan="4" class="text-center text-muted">No recent users found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-bolt mr-1"></i>
                            Quick Access
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="info-box mb-3 bg-light">
                                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-id-card"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Profile</span>
                                        <Link href="/admin/profile" class="small">Update account</Link>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="info-box mb-3 bg-light">
                                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-cog"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Settings</span>
                                        <Link href="/admin/settings" class="small">Open settings</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="col-lg-5 connectedSortable">
                <div class="card bg-gradient-primary">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Admin Summary
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="progress-group">
                            User coverage
                            <span class="float-right"><b>{{ stats.admin_users }}</b>/{{ stats.total_users }}</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" :style="{ width: `${stats.total_users ? Math.round((stats.admin_users / stats.total_users) * 100) : 0}%` }"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            Recent activity
                            <span class="float-right"><b>{{ stats.active_users }}</b>/{{ stats.total_users }}</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" :style="{ width: `${stats.total_users ? Math.round((stats.active_users / stats.total_users) * 100) : 0}%` }"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <Link href="/admin/recent-docs" class="text-white">Open document activity</Link>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-1"></i>
                            Management Links
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <Link href="/admin/contact-us" class="nav-link">
                                    Contact messages
                                    <span class="float-right badge bg-primary">Open</span>
                                </Link>
                            </li>
                            <li class="nav-item">
                                <Link href="/admin/documents" class="nav-link">
                                    Master checklist
                                    <span class="float-right badge bg-success">Manage</span>
                                </Link>
                            </li>
                            <li class="nav-item">
                                <Link href="/admin/document-checklists" class="nav-link">
                                    User reviews
                                    <span class="float-right badge bg-warning">Review</span>
                                </Link>
                            </li>
                            <li class="nav-item">
                                <Link href="/admin/content/banner" class="nav-link">
                                    Content manager
                                    <span class="float-right badge bg-danger">Edit</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>

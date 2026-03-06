<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Backend Ready" />
    <main class="d-flex w-100" style="min-height: 100vh;">
        <div class="container d-flex flex-column justify-content-center">
            <div class="row">
                <div class="col-md-8 col-xl-6 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <h1 class="h2 mb-3">Laravel + Inertia Backend Ready</h1>
                            <p class="mb-4">
                                Use these links to open the new template-based pages.
                            </p>
                            <div class="d-flex gap-2 flex-wrap">
                                <Link class="btn btn-primary" href="/login">Login</Link>
                                <Link class="btn btn-outline-primary" href="/admin/dashboard">Admin Dashboard</Link>
                                <Link class="btn btn-outline-primary" href="/dashboard">User Dashboard</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

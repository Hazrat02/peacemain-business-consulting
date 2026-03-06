<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const form = useForm({
    email: '',
});

const submit = () => {
    form.post('/forgot-password');
};
</script>

<template>
    <Head title="Forgot Password" />
    <main class="auth-wrapper">
        <div class="container d-flex flex-column">
            <div class="row vh-100">
                <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
                    <div class="d-table-cell align-middle">
                        <div class="text-center mt-4">
                            <h1 class="h2">Forgot Password</h1>
                            <p class="lead">Enter your email to receive a reset code</p>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="m-sm-3">
                                    <form @submit.prevent="submit">
                                        <div class="mb-3">
                                            <label class="form-label required">Email</label>
                                            <input v-model="form.email" class="form-control form-control-lg" type="email" placeholder="you@example.com" required />
                                            <div v-if="form.errors.email" class="text-danger mt-1">{{ form.errors.email }}</div>
                                        </div>
                                        <div v-if="page.props.flash?.status" class="alert alert-success">
                                            {{ page.props.flash.status }}
                                        </div>
                                        <div class="d-grid gap-2 mt-3">
                                            <button class="btn btn-lg btn-primary" type="submit" :disabled="form.processing">Send Reset Code</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mb-3">Back to <Link href="/login">Login</Link></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

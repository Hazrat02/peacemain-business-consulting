<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import PasswordInput from '../../Components/PasswordInput.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login');
};
</script>

<template>
    <Head title="Login" />
    <main class="auth-wrapper">
        <div class="container d-flex flex-column">
            <div class="row vh-100">
                <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
                    <div class="d-table-cell align-middle">
                        <div class="text-center mt-4">
                            <h1 class="h2">Welcome back!</h1>
                            <p class="lead">Sign in to your account</p>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="m-sm-3">
                                    <form @submit.prevent="submit">
                                        <div class="mb-3">
                                            <label class="form-label required">Email</label>
                                            <input
                                                v-model="form.email"
                                                class="form-control form-control-lg"
                                                type="email"
                                                placeholder="you@example.com"
                                                required
                                            />
                                            <div v-if="form.errors.email" class="text-danger mt-1">{{ form.errors.email }}</div>
                                        </div>
                                        <PasswordInput
                                            v-model="form.password"
                                            label="Password"
                                            input-class="form-control form-control-lg"
                                            placeholder="Enter your password"
                                            :required="true"
                                            :error="form.errors.password"
                                        />
                                        <div class="mb-3">
                                            <label class="form-check">
                                                <input v-model="form.remember" class="form-check-input" type="checkbox" />
                                                <span class="form-check-label">Remember me</span>
                                            </label>
                                        </div>
                                        <div class="d-grid gap-2 mt-3">
                                            <button class="btn btn-lg btn-primary" type="submit" :disabled="form.processing">Sign in</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mb-3">
                            No account? <Link href="/register">Create one</Link> | <Link href="/forgot-password">Forgot password</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

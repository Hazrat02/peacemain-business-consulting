<script setup>
import { Head, useForm } from '@inertiajs/vue3';

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
    <main style="font-family: Arial, sans-serif; min-height: 100vh; display: grid; place-items: center;">
        <form
            @submit.prevent="submit"
            style="width: 100%; max-width: 380px; border: 1px solid #ddd; border-radius: 8px; padding: 20px;"
        >
            <h1 style="margin-top: 0;">Login</h1>

            <label for="email">Email</label>
            <input id="email" v-model="form.email" type="email" required style="width: 100%; margin-bottom: 10px;" />
            <p v-if="form.errors.email" style="color: #b91c1c;">{{ form.errors.email }}</p>

            <label for="password">Password</label>
            <input
                id="password"
                v-model="form.password"
                type="password"
                required
                style="width: 100%; margin-bottom: 10px;"
            />
            <p v-if="form.errors.password" style="color: #b91c1c;">{{ form.errors.password }}</p>

            <label style="display: flex; gap: 8px; margin: 8px 0 12px;">
                <input v-model="form.remember" type="checkbox" />
                Remember me
            </label>

            <button type="submit" :disabled="form.processing">Sign in</button>
            <p style="margin-top: 12px;">
                No account?
                <a href="/register">Create one</a>
            </p>
        </form>
    </main>
</template>

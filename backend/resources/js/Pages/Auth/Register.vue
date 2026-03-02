<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    full_name: '',
    phone: '',
    country: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register');
};
</script>

<template>
    <Head title="Register" />
    <main style="font-family: Arial, sans-serif; min-height: 100vh; display: grid; place-items: center;">
        <form
            @submit.prevent="submit"
            style="width: 100%; max-width: 480px; border: 1px solid #ddd; border-radius: 8px; padding: 20px;"
        >
            <h1 style="margin-top: 0;">Register</h1>

            <label for="full_name">Full Name</label>
            <input id="full_name" v-model="form.full_name" type="text" required style="width: 100%; margin-bottom: 10px;" />
            <p v-if="form.errors.full_name" style="color: #b91c1c;">{{ form.errors.full_name }}</p>

            <label for="phone">Phone</label>
            <input id="phone" v-model="form.phone" type="text" required style="width: 100%; margin-bottom: 10px;" />
            <p v-if="form.errors.phone" style="color: #b91c1c;">{{ form.errors.phone }}</p>

            <label for="country">Country</label>
            <input id="country" v-model="form.country" type="text" required style="width: 100%; margin-bottom: 10px;" />
            <p v-if="form.errors.country" style="color: #b91c1c;">{{ form.errors.country }}</p>

            <label for="email">Email</label>
            <input id="email" v-model="form.email" type="email" required style="width: 100%; margin-bottom: 10px;" />
            <p v-if="form.errors.email" style="color: #b91c1c;">{{ form.errors.email }}</p>

            <label for="password">Password</label>
            <input id="password" v-model="form.password" type="password" required style="width: 100%; margin-bottom: 10px;" />
            <p v-if="form.errors.password" style="color: #b91c1c;">{{ form.errors.password }}</p>

            <label for="password_confirmation">Confirm Password</label>
            <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                required
                style="width: 100%; margin-bottom: 10px;"
            />

            <button type="submit" :disabled="form.processing">Create account</button>
            <p style="margin-top: 12px;">
                Already registered?
                <a href="/login">Login</a>
            </p>
        </form>
    </main>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const logoutForm = useForm({});

const logout = () => {
    logoutForm.post('/logout');
};
</script>

<template>
    <main style="font-family: Arial, sans-serif; max-width: 960px; margin: 0 auto; padding: 24px;">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <h1 style="margin: 0;">User Dashboard</h1>
                <p style="margin: 4px 0 0; color: #555;">
                    Signed in as {{ page.props.auth?.user?.email }}
                </p>
            </div>
            <button type="button" @click="logout">Logout</button>
        </header>
        <slot />
    </main>
</template>

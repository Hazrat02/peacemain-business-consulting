<script setup>
import { Head } from '@inertiajs/vue3';
import UserLayout from '../../Layouts/UserLayout.vue';
</script>

<template>
    <Head title="User Dashboard" />
    <UserLayout>
        <section style="border: 1px solid #ddd; border-radius: 8px; padding: 16px;">
            <h2 style="margin: 0 0 8px;">Welcome</h2>
            <p style="margin: 0;">You are logged in as a user.</p>
        </section>
    </UserLayout>
</template>

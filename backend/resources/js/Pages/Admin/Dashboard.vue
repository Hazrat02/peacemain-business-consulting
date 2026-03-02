<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

defineProps({
    stats: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <section style="display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
            <article style="border: 1px solid #ddd; border-radius: 8px; padding: 16px;">
                <h2 style="margin: 0 0 8px;">Total Users</h2>
                <p style="font-size: 28px; margin: 0;">{{ stats.total_users }}</p>
            </article>
            <article style="border: 1px solid #ddd; border-radius: 8px; padding: 16px;">
                <h2 style="margin: 0 0 8px;">Admin Users</h2>
                <p style="font-size: 28px; margin: 0;">{{ stats.admin_users }}</p>
            </article>
        </section>
    </AdminLayout>
</template>

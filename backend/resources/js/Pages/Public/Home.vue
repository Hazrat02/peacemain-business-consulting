<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Backend Ready" />
    <main style="font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 24px;">
        <h1>Laravel + Inertia Backend Ready</h1>
        <p>Your old Vue app is still available in the root project. This backend now serves web auth and role dashboards.</p>
        <p><Link href="/login">Go to Login</Link></p>
    </main>
</template>

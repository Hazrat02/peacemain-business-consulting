<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineProps({
    faqs: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    question: '',
    answer: '',
    category: 'General',
});
</script>

<template>
    <Head title="Content - FAQ" />
    <AdminLayout>
        <h1 class="h3 mb-3">Content Manage / FAQ</h1>

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">FAQ Form</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Question</label>
                                <input v-model="form.question" class="form-control" type="text" placeholder="How long does visa processing take?" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Answer</label>
                                <textarea v-model="form.answer" class="form-control" rows="4" placeholder="Usually 4-8 weeks depending on country and season."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <input v-model="form.category" class="form-control" type="text" placeholder="Visa" />
                            </div>
                            <button type="button" class="btn btn-primary">Save FAQ</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">FAQ List</h5>
                    </div>
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Category</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="faq in faqs" :key="faq.question">
                                <td>{{ faq.question }}</td>
                                <td>{{ faq.category }}</td>
                                <td>{{ faq.status }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

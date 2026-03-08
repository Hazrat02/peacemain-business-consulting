<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    faqs: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    items: props.faqs.length
        ? props.faqs.map((item) => ({
            question: item.question ?? '',
            answer: item.answer ?? '',
            category: item.category ?? 'General',
            status: item.status ?? 'Published',
        }))
        : [],
    question: '',
    answer: '',
    category: 'General',
    status: 'Published',
});

const submitting = ref(false);
const editingIndex = ref(null);

const addFaq = () => {
    const payload = {
        question: form.question,
        answer: form.answer,
        category: form.category,
        status: form.status,
    };

    if (editingIndex.value !== null) {
        form.items[editingIndex.value] = payload;
    } else {
        form.items.push(payload);
    }

    form.question = '';
    form.answer = '';
    form.category = 'General';
    form.status = 'Published';
    editingIndex.value = null;

    saveFaqs();
};

const removeFaq = (index) => {
    form.items.splice(index, 1);
    saveFaqs();
};

const editFaq = (index) => {
    const item = form.items[index];
    form.question = item.question;
    form.answer = item.answer;
    form.category = item.category;
    form.status = item.status;
    editingIndex.value = index;
};

const saveFaqs = () => {
    submitting.value = true;
    form.put('/admin/content/faq', {
        preserveScroll: true,
        onFinish: () => {
            submitting.value = false;
        },
    });
};
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
                        <form @submit.prevent="addFaq">
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
                            <button type="submit" class="btn btn-primary" :disabled="!form.question || !form.answer">
                                {{ editingIndex === null ? 'Add FAQ' : 'Update FAQ' }}
                            </button>
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
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(faq, index) in form.items" :key="`${faq.question}-${index}`">
                                <td>{{ faq.question }}</td>
                                <td>{{ faq.category }}</td>
                                <td>{{ faq.status }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary me-2" @click="editFaq(index)">Edit</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="removeFaq(index)">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="card-body border-top">
                        <button type="button" class="btn btn-primary" :disabled="submitting" @click="saveFaqs">
                            {{ submitting ? 'Saving...' : 'Save FAQ Content' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

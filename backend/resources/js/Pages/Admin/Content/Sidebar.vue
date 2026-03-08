<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    links: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    items: props.links.length
        ? props.links.map((item, index) => ({
            label: item.label ?? '',
            url: item.url ?? '',
            status: item.status ?? 'Active',
            sort_order: item.sort_order ?? index + 1,
        }))
        : [],
    label: '',
    url: '',
    status: 'Active',
    sort_order: 0,
});

const submitting = ref(false);
const editingIndex = ref(null);

const addLink = () => {
    const payload = {
        label: form.label,
        url: form.url,
        status: form.status,
        sort_order: form.sort_order || form.items.length + 1,
    };

    if (editingIndex.value !== null) {
        form.items[editingIndex.value] = payload;
    } else {
        form.items.push(payload);
    }

    form.label = '';
    form.url = '';
    form.status = 'Active';
    form.sort_order = 0;
    editingIndex.value = null;

    saveLinks();
};

const removeLink = (index) => {
    form.items.splice(index, 1);
    saveLinks();
};

const editLink = (index) => {
    const item = form.items[index];
    form.label = item.label;
    form.url = item.url;
    form.status = item.status;
    form.sort_order = item.sort_order || 0;
    editingIndex.value = index;
};

const saveLinks = () => {
    submitting.value = true;
    form.put('/admin/content/sidebar', {
        preserveScroll: true,
        onFinish: () => {
            submitting.value = false;
        },
    });
};
</script>

<template>
    <Head title="Content - Sidebar" />
    <AdminLayout>
        <h1 class="h3 mb-3">Content Manage / Sidebar</h1>

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Sidebar Link Form</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="addLink">
                            <div class="mb-3">
                                <label class="form-label">Label</label>
                                <input v-model="form.label" class="form-control" type="text" placeholder="Application Status" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL</label>
                                <input v-model="form.url" class="form-control" type="text" placeholder="/dashboard/overseas" />
                            </div>
                            <button type="submit" class="btn btn-primary" :disabled="!form.label || !form.url">
                                {{ editingIndex === null ? 'Add Link' : 'Update Link' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Sidebar Links</h5>
                    </div>
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>URL</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(link, index) in form.items" :key="`${link.label}-${index}`">
                                <td>{{ link.label }}</td>
                                <td>{{ link.url }}</td>
                                <td>{{ link.status }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary me-2" @click="editLink(index)">Edit</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="removeLink(index)">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="card-body border-top">
                        <button type="button" class="btn btn-primary" :disabled="submitting" @click="saveLinks">
                            {{ submitting ? 'Saving...' : 'Save Sidebar Content' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

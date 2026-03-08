<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    banners: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    items: props.banners.length
        ? props.banners.map((item, index) => ({
            title: item.title ?? '',
            subtitle: item.subtitle ?? '',
            description: item.description ?? '',
            button_text: item.button_text ?? 'Contact Us',
            button_url: item.button_url ?? '/contact',
            image_url: item.image_url ?? '',
            position: item.position ?? 'Home Top',
            status: item.status ?? 'Active',
            sort_order: item.sort_order ?? index + 1,
        }))
        : [],
    title: '',
    subtitle: '',
    description: '',
    button_text: 'Contact Us',
    button_url: '/contact',
    image_url: '',
    position: 'Home Top',
    status: 'Active',
    sort_order: 0,
});

const submitting = ref(false);
const editingIndex = ref(null);

const addBanner = () => {
    const payload = {
        title: form.title,
        subtitle: form.subtitle,
        description: form.description,
        button_text: form.button_text || 'Contact Us',
        button_url: form.button_url || '/contact',
        image_url: form.image_url,
        position: form.position,
        status: form.status,
        sort_order: form.sort_order || form.items.length + 1,
    };

    if (editingIndex.value !== null) {
        form.items[editingIndex.value] = payload;
    } else {
        form.items.push(payload);
    }

    form.title = '';
    form.subtitle = '';
    form.description = '';
    form.button_text = 'Contact Us';
    form.button_url = '/contact';
    form.image_url = '';
    form.position = 'Home Top';
    form.status = 'Active';
    form.sort_order = 0;
    editingIndex.value = null;

    saveBanners();
};

const removeBanner = (index) => {
    form.items.splice(index, 1);
    saveBanners();
};

const editBanner = (index) => {
    const item = form.items[index];
    form.title = item.title;
    form.subtitle = item.subtitle;
    form.description = item.description;
    form.button_text = item.button_text;
    form.button_url = item.button_url;
    form.image_url = item.image_url;
    form.position = item.position;
    form.status = item.status;
    form.sort_order = item.sort_order || 0;
    editingIndex.value = index;
};

const saveBanners = () => {
    submitting.value = true;
    form.put('/admin/content/banner', {
        preserveScroll: true,
        onFinish: () => {
            submitting.value = false;
        },
    });
};
</script>

<template>
    <Head title="Content - Banner" />
    <AdminLayout>
        <h1 class="h3 mb-3">Content Manage / Banner</h1>

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Banner Form</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="addBanner">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input v-model="form.title" class="form-control" type="text" placeholder="Study in UK" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subtitle</label>
                                <textarea v-model="form.subtitle" class="form-control" rows="3" placeholder="Start your global education journey"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea v-model="form.description" class="form-control" rows="3" placeholder="Banner right content text"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Image URL</label>
                                <input v-model="form.image_url" class="form-control" type="text" placeholder="https://..." />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Button Text</label>
                                <input v-model="form.button_text" class="form-control" type="text" placeholder="Contact Us" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Button URL</label>
                                <input v-model="form.button_url" class="form-control" type="text" placeholder="/contact" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Position</label>
                                <select v-model="form.position" class="form-select">
                                    <option>Home Top</option>
                                    <option>Overseas Page</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" :disabled="!form.title || !form.image_url">
                                {{ editingIndex === null ? 'Add Banner' : 'Update Banner' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Existing Banners</h5>
                    </div>
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(banner, index) in form.items" :key="`${banner.title}-${index}`">
                                <td>{{ banner.title }}</td>
                                <td>{{ banner.position }}</td>
                                <td>{{ banner.status }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary me-2" @click="editBanner(index)">Edit</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="removeBanner(index)">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="card-body border-top">
                        <button type="button" class="btn btn-primary" :disabled="submitting" @click="saveBanners">
                            {{ submitting ? 'Saving...' : 'Save Banner Content' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

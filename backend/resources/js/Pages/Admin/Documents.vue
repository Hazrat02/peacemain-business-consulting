<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({
    sections: {
        type: Array,
        required: true,
    },
});

const sectionForm = useForm({
    name: '',
    sort_order: 0,
    is_active: true,
});

const documentForm = useForm({
    document_section_id: '',
    title: '',
    description: '',
    is_required: true,
    sort_order: 0,
    is_active: true,
});

const sectionEditForms = {};
const deleteForm = useForm({});
const editingSectionId = ref(null);

const submitSection = () => {
    sectionForm.post('/admin/documents/sections', {
        preserveScroll: true,
        onSuccess: () => sectionForm.reset(),
    });
};

const submitDocument = () => {
    documentForm.post('/admin/documents/master', {
        preserveScroll: true,
        onSuccess: () => documentForm.reset(),
    });
};

const getSectionEditForm = (section) => {
    if (!sectionEditForms[section.id]) {
        sectionEditForms[section.id] = useForm({
            name: section.name,
            sort_order: section.sort_order ?? 0,
            is_active: section.is_active,
        });
    }

    return sectionEditForms[section.id];
};

const startEditSection = (section) => {
    const form = getSectionEditForm(section);
    form.name = section.name;
    form.sort_order = section.sort_order ?? 0;
    form.is_active = section.is_active;
    form.clearErrors();
    editingSectionId.value = section.id;
};

const cancelEditSection = () => {
    editingSectionId.value = null;
};

const updateSection = (sectionId) => {
    const form = sectionEditForms[sectionId];
    if (!form) {
        return;
    }

    form.patch(`/admin/documents/sections/${sectionId}`, {
        preserveScroll: true,
        onSuccess: () => {
            editingSectionId.value = null;
        },
    });
};

const deleteSection = (sectionId) => {
    if (!window.confirm('Delete this section? This will also remove all documents in this section and linked checklist records.')) {
        return;
    }

    deleteForm.delete(`/admin/documents/sections/${sectionId}`, {
        preserveScroll: true,
        onSuccess: () => {
            if (editingSectionId.value === sectionId) {
                editingSectionId.value = null;
            }
        },
    });
};
</script>

<template>
    <Head title="Master Checklist" />
    <AdminLayout>
        <h1 class="h3 mb-3">Master Document Checklist</h1>

        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add Section</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="submitSection">
                            <div class="mb-2">
                                <label class="form-label">Section Name</label>
                                <input v-model="sectionForm.name" type="text" class="form-control" required />
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Sort Order</label>
                                <input v-model.number="sectionForm.sort_order" type="number" min="0" class="form-control" />
                            </div>
                            <button type="submit" class="btn btn-primary" :disabled="sectionForm.processing">Create Section</button>
                        </form>

                        
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add Master Document</h5>
                    </div>
                    <div class="card-body">
                        <form class="row g-2" @submit.prevent="submitDocument">
                            <div class="col-md-4">
                                <label class="form-label">Section</label>
                                <select v-model="documentForm.document_section_id" class="form-select" required>
                                    <option value="">Select section</option>
                                    <option v-for="section in sections" :key="section.id" :value="section.id">{{ section.name }}</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Title</label>
                                <input v-model="documentForm.title" type="text" class="form-control" required />
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Description</label>
                                <input v-model="documentForm.description" type="text" class="form-control" />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Required</label>
                                <select v-model="documentForm.is_required" class="form-select">
                                    <option :value="true">Yes</option>
                                    <option :value="false">No</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sort</label>
                                <input v-model.number="documentForm.sort_order" type="number" min="0" class="form-control" />
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary" :disabled="documentForm.processing">Add Document</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Docs Section Manage</h5>
                    </div>
                    <div class="card-body">
                        <div v-if="sections.length === 0" class="text-muted small">No sections added yet.</div>
                        <template v-else>
                            <div v-for="section in sections" :key="section.id" class="border rounded p-2 mb-2">
                                <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                    <div class="flex-grow-1 text-break">
                                        <div class="fw-semibold">{{ section.name }}</div>
                                        <small class="text-muted">Sort: {{ section.sort_order }} | {{ section.is_active ? 'Active' : 'Inactive' }}</small>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap justify-content-end">
                                        <Link :href="`/admin/documents/sections/${section.id}/rules`" class="btn btn-sm btn-outline-primary">
                                            View Docs
                                        </Link>
                                        <button
                                            v-if="editingSectionId !== section.id"
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            @click="startEditSection(section)"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            :disabled="deleteForm.processing"
                                            @click="deleteSection(section.id)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </div>

                                <form
                                    v-if="editingSectionId === section.id"
                                    class="row g-2 mt-2"
                                    @submit.prevent="updateSection(section.id)"
                                >
                                    <div class="col-12">
                                        <label class="form-label">Section Name</label>
                                        <input v-model="getSectionEditForm(section).name" type="text" class="form-control" required />
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label">Sort</label>
                                        <input v-model.number="getSectionEditForm(section).sort_order" type="number" min="0" class="form-control" />
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label">Active</label>
                                        <select v-model="getSectionEditForm(section).is_active" class="form-select">
                                            <option :value="true">Yes</option>
                                            <option :value="false">No</option>
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex gap-2 flex-wrap">
                                        <button type="submit" class="btn btn-sm btn-primary" :disabled="getSectionEditForm(section).processing">Save</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="cancelEditSection">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </template>

                        
                    </div>
                </div>
            </div>


        </div>
    </AdminLayout>
</template>

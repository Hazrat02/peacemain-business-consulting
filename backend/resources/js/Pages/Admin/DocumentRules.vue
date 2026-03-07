<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../Layouts/AdminLayout.vue';
import { COUNTRIES } from '../../constants/countries';

const props = defineProps({
    section: {
        type: Object,
        required: true,
    },
    documents: {
        type: Array,
        required: true,
    },
});

const ruleForms = {};
const documentEditForms = {};
const deleteForm = useForm({});
const editingDocumentId = ref(null);

const getRuleForm = (documentId) => {
    if (!ruleForms[documentId]) {
        ruleForms[documentId] = useForm({
            source_country: '',
            destination_country: '',
            marital_status: '',
            is_exclusion: false,
        });
    }

    return ruleForms[documentId];
};

const submitRule = (documentId) => {
    const form = getRuleForm(documentId);
    form.post(`/admin/documents/master/${documentId}/rules`, {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const getDocumentEditForm = (document) => {
    if (!documentEditForms[document.id]) {
        documentEditForms[document.id] = useForm({
            document_section_id: props.section.id,
            title: document.title,
            description: document.description ?? '',
            is_required: document.is_required,
            sort_order: document.sort_order ?? 0,
            is_active: document.is_active,
        });
    }

    return documentEditForms[document.id];
};

const startEditDocument = (document) => {
    const form = getDocumentEditForm(document);
    form.document_section_id = props.section.id;
    form.title = document.title;
    form.description = document.description ?? '';
    form.is_required = document.is_required;
    form.sort_order = document.sort_order ?? 0;
    form.is_active = document.is_active;
    form.clearErrors();
    editingDocumentId.value = document.id;
};

const cancelEditDocument = () => {
    editingDocumentId.value = null;
};

const updateDocument = (documentId) => {
    const form = documentEditForms[documentId];
    if (!form) {
        return;
    }

    form.document_section_id = props.section.id;
    form.patch(`/admin/documents/master/${documentId}`, {
        preserveScroll: true,
        onSuccess: () => {
            editingDocumentId.value = null;
        },
    });
};

const deleteDocument = (documentId) => {
    if (!window.confirm('Delete this master document? This will also remove linked rules and checklist records.')) {
        return;
    }

    deleteForm.delete(`/admin/documents/master/${documentId}`, {
        preserveScroll: true,
        onSuccess: () => {
            if (editingDocumentId.value === documentId) {
                editingDocumentId.value = null;
            }
        },
    });
};

const deleteRule = (ruleId) => {
    deleteForm.delete(`/admin/documents/rules/${ruleId}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Rules - ${section.name}`" />
    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
            <h1 class="h3 mb-0">Rules - {{ section.name }}</h1>
            <Link href="/admin/documents" class="btn btn-outline-secondary">Back to Documents</Link>
        </div>

        <div v-if="documents.length === 0" class="card">
            <div class="card-body">
                <p class="mb-0 text-muted">No master documents found for this section yet.</p>
            </div>
        </div>

        <div v-for="doc in documents" v-else :key="doc.id" class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1">{{ doc.title }}</h6>
                        <small class="text-muted">{{ doc.is_required ? 'Required' : 'Optional' }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button
                            v-if="editingDocumentId !== doc.id"
                            type="button"
                            class="btn btn-sm btn-outline-secondary"
                            @click="startEditDocument(doc)"
                        >
                            Edit
                        </button>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger"
                            :disabled="deleteForm.processing"
                            @click="deleteDocument(doc.id)"
                        >
                            Delete
                        </button>
                    </div>
                </div>

                <form
                    v-if="editingDocumentId === doc.id"
                    class="row g-2 border rounded p-2 mb-3 bg-light"
                    @submit.prevent="updateDocument(doc.id)"
                >
                    <div class="col-md-8">
                        <label class="form-label">Title</label>
                        <input v-model="getDocumentEditForm(doc).title" type="text" class="form-control" required />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sort</label>
                        <input v-model.number="getDocumentEditForm(doc).sort_order" type="number" min="0" class="form-control" />
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Description</label>
                        <input v-model="getDocumentEditForm(doc).description" type="text" class="form-control" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Required</label>
                        <select v-model="getDocumentEditForm(doc).is_required" class="form-select">
                            <option :value="true">Yes</option>
                            <option :value="false">No</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Active</label>
                        <select v-model="getDocumentEditForm(doc).is_active" class="form-select">
                            <option :value="true">Yes</option>
                            <option :value="false">No</option>
                        </select>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary" :disabled="getDocumentEditForm(doc).processing">Save</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="cancelEditDocument">Cancel</button>
                    </div>
                </form>

                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Source Country</th>
                            <th>Destination</th>
                            <th>Marital</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="rule in doc.rules" :key="rule.id">
                            <td>{{ rule.is_exclusion ? 'Exclude' : 'Include' }}</td>
                            <td>{{ rule.source_country || 'Any' }}</td>
                            <td>{{ rule.destination_country || 'Any' }}</td>
                            <td>{{ rule.marital_status || 'Any' }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger" @click="deleteRule(rule.id)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <form class="row g-2" @submit.prevent="submitRule(doc.id)">
                    <div class="col-md-2">
                        <select v-model="getRuleForm(doc.id).is_exclusion" class="form-select">
                            <option :value="false">Include</option>
                            <option :value="true">Exclude</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select v-model="getRuleForm(doc.id).source_country" class="form-select">
                            <option value="">All Countries</option>
                            <option v-for="country in COUNTRIES" :key="country" :value="country">{{ country }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select v-model="getRuleForm(doc.id).destination_country" class="form-select">
                            <option value="">All Countries</option>
                            <option v-for="country in COUNTRIES" :key="`dest-${country}`" :value="country">{{ country }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select v-model="getRuleForm(doc.id).marital_status" class="form-select">
                            <option value="">Any Marital</option>
                            <option value="single">single</option>
                            <option value="married">married</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-outline-primary w-100">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

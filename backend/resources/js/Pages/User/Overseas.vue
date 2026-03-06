<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import UserLayout from '../../Layouts/UserLayout.vue';

defineProps({
    applications: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    preferred_country: '',
    preferred_program: '',
    intake: '',
});
</script>

<template>
    <Head title="Overseas" />
    <UserLayout>
        <h1 class="h3 mb-3">Overseas</h1>

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">New Overseas Application</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Preferred Country</label>
                                <input v-model="form.preferred_country" class="form-control" type="text" placeholder="Canada" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Program</label>
                                <input v-model="form.preferred_program" class="form-control" type="text" placeholder="MBA" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Intake</label>
                                <input v-model="form.intake" class="form-control" type="text" placeholder="Fall 2026" />
                            </div>
                            <button type="button" class="btn btn-primary">Submit Application</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Application Status</h5>
                    </div>
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Country</th>
                                <th>Program</th>
                                <th>Intake</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="application in applications" :key="`${application.country}-${application.program}`">
                                <td>{{ application.country }}</td>
                                <td>{{ application.program }}</td>
                                <td>{{ application.intake }}</td>
                                <td>{{ application.status }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

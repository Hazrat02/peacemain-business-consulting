<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PasswordInput from '../../Components/PasswordInput.vue';
import { COUNTRIES } from '../../constants/countries';

const form = useForm({
    full_name: '',
    phone: '',
    country: '',
    destination_country: '',
    marital_status: '',
    passport_type: '',
    profile_image: null,
    email: '',
    password: '',
    password_confirmation: '',
});

const imagePreview = ref('');

const onImageChange = (event) => {
    const [file] = event.target.files || [];
    form.profile_image = file || null;
    imagePreview.value = file ? URL.createObjectURL(file) : '';
};

const submit = () => {
    form.post('/register', {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Register" />
    <main class="auth-wrapper">
        <div class="container d-flex flex-column">
            <div class="row vh-100">
                <div class="col-sm-11 col-md-9 col-lg-7 col-xl-6 mx-auto d-table h-100">
                    <div class="d-table-cell align-middle">
                        <div class="text-center mt-4">
                            <h1 class="h2">Create Account</h1>
                            <p class="lead">Start your user dashboard journey</p>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="m-sm-3">
                                    <form @submit.prevent="submit">
                                        <div class="mb-3">
                                            <label class="form-label required">Full Name</label>
                                            <input v-model="form.full_name" class="form-control form-control-lg" type="text" placeholder="John Doe" required />
                                            <div v-if="form.errors.full_name" class="text-danger mt-1">{{ form.errors.full_name }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label required">Phone</label>
                                            <input v-model="form.phone" class="form-control form-control-lg" type="text" placeholder="+1 555 123 4567" required />
                                            <div v-if="form.errors.phone" class="text-danger mt-1">{{ form.errors.phone }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label required">Country</label>
                                            <select v-model="form.country" class="form-select form-select-lg" required>
                                                <option value="" disabled>Select your country</option>
                                                <option v-for="country in COUNTRIES" :key="country" :value="country">{{ country }}</option>
                                            </select>
                                            <div v-if="form.errors.country" class="text-danger mt-1">{{ form.errors.country }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Destination Country</label>
                                            <input v-model="form.destination_country" class="form-control form-control-lg" type="text" placeholder="Canada" />
                                            <div v-if="form.errors.destination_country" class="text-danger mt-1">{{ form.errors.destination_country }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Marital Status</label>
                                            <select v-model="form.marital_status" class="form-select form-select-lg">
                                                <option value="">Select marital status</option>
                                                <option value="single">Single</option>
                                                <option value="married">Married</option>
                                            </select>
                                            <div v-if="form.errors.marital_status" class="text-danger mt-1">{{ form.errors.marital_status }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Passport Type</label>
                                            <input v-model="form.passport_type" class="form-control form-control-lg" type="text" placeholder="Regular" />
                                            <div v-if="form.errors.passport_type" class="text-danger mt-1">{{ form.errors.passport_type }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Profile Image</label>
                                            <input class="form-control form-control-lg" type="file" accept="image/*" @change="onImageChange" />
                                            <div v-if="form.errors.profile_image" class="text-danger mt-1">{{ form.errors.profile_image }}</div>
                                            <div v-if="imagePreview" class="mt-2">
                                                <img :src="imagePreview" alt="Profile preview" class="rounded" style="width: 80px; height: 80px; object-fit: cover;" />
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label required">Email</label>
                                            <input v-model="form.email" class="form-control form-control-lg" type="email" placeholder="you@example.com" required />
                                            <div v-if="form.errors.email" class="text-danger mt-1">{{ form.errors.email }}</div>
                                        </div>
                                        <PasswordInput
                                            v-model="form.password"
                                            label="Password"
                                            input-class="form-control form-control-lg"
                                            placeholder="Create a strong password"
                                            :required="true"
                                            :error="form.errors.password"
                                        />
                                        <PasswordInput
                                            v-model="form.password_confirmation"
                                            label="Confirm Password"
                                            input-class="form-control form-control-lg"
                                            placeholder="Re-enter password"
                                            :required="true"
                                        />
                                        <div class="d-grid gap-2 mt-3">
                                            <button class="btn btn-lg btn-primary" type="submit" :disabled="form.processing">Create account</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mb-3">Already registered? <Link href="/login">Login</Link></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

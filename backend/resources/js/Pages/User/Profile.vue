<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import UserLayout from '../../Layouts/UserLayout.vue';
import PasswordInput from '../../Components/PasswordInput.vue';
import { COUNTRIES } from '../../constants/countries';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const profileForm = useForm({
    full_name: props.user.full_name || '',
    email: props.user.email || '',
    phone: props.user.phone || '',
    country: props.user.country || '',
    profile_image: null,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const imagePreview = ref(props.user.profile_image_url || '');

const onImageChange = (event) => {
    const [file] = event.target.files || [];
    profileForm.profile_image = file || null;
    imagePreview.value = file ? URL.createObjectURL(file) : (props.user.profile_image_url || '');
};

const submitProfile = () => {
    profileForm
        .transform((data) => ({
            ...data,
            _method: 'patch',
        }))
        .post('/profile', {
        preserveScroll: true,
        forceFormData: true,
    });
};

const submitPassword = () => {
    passwordForm.patch('/profile/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};
</script>

<template>
    <Head title="My Profile" />
    <UserLayout>
        <h1 class="h3 mb-3">My Profile</h1>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="submitProfile">
                            <div class="mb-3">
                                <label class="form-label required">Full Name</label>
                                <input v-model="profileForm.full_name" class="form-control" type="text" placeholder="John Doe" required />
                                <div v-if="profileForm.errors.full_name" class="text-danger mt-1">{{ profileForm.errors.full_name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Email</label>
                                <input v-model="profileForm.email" class="form-control" type="email" placeholder="you@example.com" required />
                                <div v-if="profileForm.errors.email" class="text-danger mt-1">{{ profileForm.errors.email }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input v-model="profileForm.phone" class="form-control" type="text" placeholder="+1 555 123 4567" />
                                <div v-if="profileForm.errors.phone" class="text-danger mt-1">{{ profileForm.errors.phone }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <select v-model="profileForm.country" class="form-select">
                                    <option value="">Select country</option>
                                    <option v-for="country in COUNTRIES" :key="country" :value="country">{{ country }}</option>
                                </select>
                                <div v-if="profileForm.errors.country" class="text-danger mt-1">{{ profileForm.errors.country }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Profile Image</label>
                                <input class="form-control" type="file" accept="image/*" @change="onImageChange" />
                                <div v-if="profileForm.errors.profile_image" class="text-danger mt-1">{{ profileForm.errors.profile_image }}</div>
                                <div v-if="imagePreview" class="mt-2">
                                    <img :src="imagePreview" alt="Profile preview" class="rounded" style="width: 90px; height: 90px; object-fit: cover;" />
                                </div>
                            </div>
                            <button class="btn btn-primary" type="submit" :disabled="profileForm.processing">Save Profile</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Update Password</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="submitPassword">
                            <PasswordInput
                                v-model="passwordForm.current_password"
                                label="Current Password"
                                placeholder="Enter current password"
                                :required="true"
                                :error="passwordForm.errors.current_password"
                            />
                            <PasswordInput
                                v-model="passwordForm.password"
                                label="New Password"
                                placeholder="Enter new password"
                                :required="true"
                                :error="passwordForm.errors.password"
                            />
                            <PasswordInput
                                v-model="passwordForm.password_confirmation"
                                label="Confirm New Password"
                                placeholder="Re-enter new password"
                                :required="true"
                            />
                            <button class="btn btn-primary" type="submit" :disabled="passwordForm.processing">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

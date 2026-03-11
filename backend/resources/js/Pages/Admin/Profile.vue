<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';
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
    destination_country: props.user.destination_country || '',
    marital_status: props.user.marital_status || '',
    passport_type: props.user.passport_type || '',
    profile_image: null,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const imagePreview = ref(props.user.profile_image_url || '/adminkit/img/avatars/avatar.jpg');
const maritalStatusLabel = computed(() => {
    if (!props.user.marital_status) {
        return 'Not specified';
    }

    return props.user.marital_status.charAt(0).toUpperCase() + props.user.marital_status.slice(1);
});

const onImageChange = (event) => {
    const [file] = event.target.files || [];
    profileForm.profile_image = file || null;
    imagePreview.value = file ? URL.createObjectURL(file) : (props.user.profile_image_url || '/adminkit/img/avatars/avatar.jpg');
};

const submitProfile = () => {
    profileForm
        .transform((data) => ({
            ...data,
            _method: 'patch',
        }))
        .post('/admin/profile', {
            preserveScroll: true,
            forceFormData: true,
        });
};

const submitPassword = () => {
    passwordForm.patch('/admin/profile/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};
</script>

<template>
    <Head title="Admin Profile" />
    <AdminLayout>
        <div class="adminlte-page-header">
            <div>
                <h1 class="m-0">Profile</h1>
                <p class="text-muted mb-0">Manage your administrator account details and security settings.</p>
            </div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><Link href="/admin/dashboard">Home</Link></li>
                <li class="breadcrumb-item active">Admin Profile</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img
                                class="profile-user-img img-fluid img-circle"
                                :src="imagePreview"
                                alt="Admin profile picture"
                                style="width: 110px; height: 110px; object-fit: cover;"
                            >
                        </div>

                        <h3 class="profile-username text-center">{{ user.full_name || 'Administrator' }}</h3>
                        <p class="text-muted text-center">Admin Panel Manager</p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Email</b> <span class="float-right">{{ user.email }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Country</b> <span class="float-right">{{ user.country || 'N/A' }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Destination</b> <span class="float-right">{{ user.destination_country || 'N/A' }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Passport</b> <span class="float-right">{{ user.passport_type || 'N/A' }}</span>
                            </li>
                        </ul>

                        <a href="#profile-settings" class="btn btn-primary btn-block" data-bs-toggle="tab"><b>Edit Profile</b></a>
                    </div>
                </div>

            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#profile-overview" data-bs-toggle="tab">Overview</a></li>
                            <li class="nav-item"><a class="nav-link" href="#profile-settings" data-bs-toggle="tab">Settings</a></li>
                            <li class="nav-item"><a class="nav-link" href="#profile-security" data-bs-toggle="tab">Security</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="profile-overview">
                                <div class="post">
                                    <div class="user-block">
                                        <img class="img-circle img-bordered-sm" :src="imagePreview" alt="Admin image">
                                        <span class="username">
                                            <a href="#">{{ user.full_name || 'Administrator' }}</a>
                                        </span>
                                        <span class="description">Administrator account summary</span>
                                    </div>

                                    <p>
                                        This account controls the admin panel. Use the settings tab to update profile details and the
                                        security tab to rotate the password without changing any existing application logic.
                                    </p>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-envelope"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Email Address</span>
                                                <span class="info-box-number">{{ user.email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-phone"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Phone Number</span>
                                                <span class="info-box-number">{{ user.phone || 'Not set' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-warning"><i class="fas fa-globe"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Country</span>
                                                <span class="info-box-number">{{ user.country || 'Not set' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-danger"><i class="fas fa-plane"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Destination Country</span>
                                                <span class="info-box-number">{{ user.destination_country || 'Not set' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="profile-settings">
                                <form class="form-horizontal" @submit.prevent="submitProfile">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label required">Full Name</label>
                                        <div class="col-sm-9">
                                            <input v-model="profileForm.full_name" type="text" class="form-control" placeholder="John Doe" required>
                                            <div v-if="profileForm.errors.full_name" class="text-danger mt-1">{{ profileForm.errors.full_name }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label required">Email</label>
                                        <div class="col-sm-9">
                                            <input v-model="profileForm.email" type="email" class="form-control" placeholder="you@example.com" required>
                                            <div v-if="profileForm.errors.email" class="text-danger mt-1">{{ profileForm.errors.email }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Phone</label>
                                        <div class="col-sm-9">
                                            <input v-model="profileForm.phone" type="text" class="form-control" placeholder="+1 555 123 4567">
                                            <div v-if="profileForm.errors.phone" class="text-danger mt-1">{{ profileForm.errors.phone }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Country</label>
                                        <div class="col-sm-9">
                                            <select v-model="profileForm.country" class="form-select">
                                                <option value="">Select country</option>
                                                <option v-for="country in COUNTRIES" :key="country" :value="country">{{ country }}</option>
                                            </select>
                                            <div v-if="profileForm.errors.country" class="text-danger mt-1">{{ profileForm.errors.country }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Destination</label>
                                        <div class="col-sm-9">
                                            <input v-model="profileForm.destination_country" type="text" class="form-control" placeholder="Canada">
                                            <div v-if="profileForm.errors.destination_country" class="text-danger mt-1">{{ profileForm.errors.destination_country }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Marital Status</label>
                                        <div class="col-sm-9">
                                            <select v-model="profileForm.marital_status" class="form-select">
                                                <option value="">Select status</option>
                                                <option value="single">Single</option>
                                                <option value="married">Married</option>
                                            </select>
                                            <div v-if="profileForm.errors.marital_status" class="text-danger mt-1">{{ profileForm.errors.marital_status }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Passport Type</label>
                                        <div class="col-sm-9">
                                            <input v-model="profileForm.passport_type" type="text" class="form-control" placeholder="Regular">
                                            <div v-if="profileForm.errors.passport_type" class="text-danger mt-1">{{ profileForm.errors.passport_type }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Profile Image</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="file" accept="image/*" @change="onImageChange">
                                            <div v-if="profileForm.errors.profile_image" class="text-danger mt-1">{{ profileForm.errors.profile_image }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="offset-sm-3 col-sm-9">
                                            <button type="submit" class="btn btn-primary" :disabled="profileForm.processing">Save Profile</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane" id="profile-security">
                                <form class="form-horizontal" @submit.prevent="submitPassword">
                                    <div class="mb-3">
                                        <PasswordInput
                                            v-model="passwordForm.current_password"
                                            label="Current Password"
                                            placeholder="Enter current password"
                                            :required="true"
                                            :error="passwordForm.errors.current_password"
                                        />
                                    </div>
                                    <div class="mb-3">
                                        <PasswordInput
                                            v-model="passwordForm.password"
                                            label="New Password"
                                            placeholder="Enter new password"
                                            :required="true"
                                            :error="passwordForm.errors.password"
                                        />
                                    </div>
                                    <div class="mb-3">
                                        <PasswordInput
                                            v-model="passwordForm.password_confirmation"
                                            label="Confirm New Password"
                                            placeholder="Re-enter new password"
                                            :required="true"
                                        />
                                    </div>
                                    <button class="btn btn-danger" type="submit" :disabled="passwordForm.processing">Update Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

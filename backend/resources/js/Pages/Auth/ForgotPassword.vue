<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const sendForm = useForm({
    email: '',
});

const verifyForm = useForm({
    email: '',
    code: '',
});

const resetForm = useForm({
    email: '',
    code: '',
    password: '',
    password_confirmation: '',
});

const step = ref(1);
const verifyLoading = ref(false);
const resetLoading = ref(false);
const successMessage = ref('');
const resendMessage = ref('');

const emailForFlow = computed(() => verifyForm.email || sendForm.email);

const syncEmail = () => {
    verifyForm.email = sendForm.email;
    resetForm.email = sendForm.email;
    resetForm.code = verifyForm.code;
};

const submitEmail = () => {
    successMessage.value = '';
    resendMessage.value = '';
    sendForm.clearErrors();

    sendForm.post('/forgot-password', {
        preserveScroll: true,
        onSuccess: () => {
            syncEmail();
            step.value = 2;
            successMessage.value = 'Reset code sent. Check your email and enter the code below.';
        },
    });
};

const submitCode = async () => {
    successMessage.value = '';
    resendMessage.value = '';
    verifyForm.clearErrors();
    resetForm.clearErrors();
    resetForm.code = verifyForm.code;

    verifyLoading.value = true;

    try {
        await window.axios.post('/api/auth/forgot-password/verify', {
            email: verifyForm.email,
            code: verifyForm.code,
        });

        step.value = 3;
        successMessage.value = 'Code verified. Set your new password.';
    } catch (error) {
        const errors = error.response?.data?.errors ?? {};
        verifyForm.setError(errors);
        if (!Object.keys(errors).length) {
            verifyForm.setError('code', error.response?.data?.message ?? 'Invalid or expired code.');
        }
    } finally {
        verifyLoading.value = false;
    }
};

const resendCode = () => {
    resendMessage.value = '';
    successMessage.value = '';
    sendForm.email = emailForFlow.value;

    sendForm.post('/forgot-password', {
        preserveScroll: true,
        onSuccess: () => {
            syncEmail();
            resendMessage.value = 'A new reset code has been sent.';
            step.value = 2;
            verifyForm.code = '';
            resetForm.code = '';
        },
    });
};

const submitNewPassword = async () => {
    successMessage.value = '';
    resendMessage.value = '';
    resetForm.clearErrors();

    resetLoading.value = true;

    try {
        await window.axios.post('/api/auth/forgot-password/reset', {
            email: resetForm.email,
            code: resetForm.code,
            password: resetForm.password,
            password_confirmation: resetForm.password_confirmation,
        });

        resetForm.reset('password', 'password_confirmation');
        successMessage.value = 'Password reset successfully. You can now log in.';
        step.value = 4;
    } catch (error) {
        const errors = error.response?.data?.errors ?? {};
        resetForm.setError(errors);
        if (!Object.keys(errors).length) {
            resetForm.setError('password', error.response?.data?.message ?? 'Unable to reset password.');
        }
    } finally {
        resetLoading.value = false;
    }
};
</script>

<template>
    <Head title="Forgot Password" />
    <main class="auth-wrapper">
        <div class="container d-flex flex-column">
            <div class="row vh-100">
                <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
                    <div class="d-table-cell align-middle">
                        <div class="text-center mt-4">
                            <h1 class="h2">Forgot Password</h1>
                            <p class="lead">Send code, verify it, then set a new password.</p>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="m-sm-3">
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between small text-muted">
                                            <span :class="{ 'text-primary fw-bold': step >= 1 }">1. Email</span>
                                            <span :class="{ 'text-primary fw-bold': step >= 2 }">2. Code</span>
                                            <span :class="{ 'text-primary fw-bold': step >= 3 }">3. New Password</span>
                                        </div>
                                    </div>

                                    <div v-if="successMessage" class="alert alert-success">
                                        {{ successMessage }}
                                    </div>

                                    <div v-if="resendMessage" class="alert alert-info">
                                        {{ resendMessage }}
                                    </div>

                                    <form v-if="step === 1" @submit.prevent="submitEmail">
                                        <div class="mb-3">
                                            <label class="form-label required">Email</label>
                                            <input v-model="sendForm.email" class="form-control form-control-lg" type="email" placeholder="you@example.com" required />
                                            <div v-if="sendForm.errors.email" class="text-danger mt-1">{{ sendForm.errors.email }}</div>
                                        </div>
                                        <div class="d-grid gap-2 mt-3">
                                            <button class="btn btn-lg btn-primary" type="submit" :disabled="sendForm.processing">Send Reset Code</button>
                                        </div>
                                    </form>

                                    <form v-else-if="step === 2" @submit.prevent="submitCode">
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input :value="emailForFlow" class="form-control form-control-lg" type="email" disabled />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label required">Reset Code</label>
                                            <input v-model="verifyForm.code" class="form-control form-control-lg" type="text" inputmode="numeric" maxlength="6" placeholder="Enter 6-digit code" required />
                                            <div v-if="verifyForm.errors.code" class="text-danger mt-1">{{ verifyForm.errors.code }}</div>
                                            <div v-if="verifyForm.errors.email" class="text-danger mt-1">{{ verifyForm.errors.email }}</div>
                                        </div>
                                        <div class="d-grid gap-2 mt-3">
                                            <button class="btn btn-lg btn-primary" type="submit" :disabled="verifyLoading">Verify Code</button>
                                            <button class="btn btn-lg btn-outline-secondary" type="button" :disabled="sendForm.processing" @click="resendCode">Resend Code</button>
                                        </div>
                                    </form>

                                    <form v-else-if="step === 3" @submit.prevent="submitNewPassword">
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input :value="emailForFlow" class="form-control form-control-lg" type="email" disabled />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Verified Code</label>
                                            <input :value="resetForm.code" class="form-control form-control-lg" type="text" disabled />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label required">New Password</label>
                                            <input v-model="resetForm.password" class="form-control form-control-lg" type="password" placeholder="Enter new password" required />
                                            <div v-if="resetForm.errors.password" class="text-danger mt-1">{{ resetForm.errors.password }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label required">Confirm Password</label>
                                            <input v-model="resetForm.password_confirmation" class="form-control form-control-lg" type="password" placeholder="Confirm new password" required />
                                            <div v-if="resetForm.errors.password_confirmation" class="text-danger mt-1">{{ resetForm.errors.password_confirmation }}</div>
                                            <div v-if="resetForm.errors.code" class="text-danger mt-1">{{ resetForm.errors.code }}</div>
                                            <div v-if="resetForm.errors.email" class="text-danger mt-1">{{ resetForm.errors.email }}</div>
                                        </div>
                                        <div class="d-grid gap-2 mt-3">
                                            <button class="btn btn-lg btn-primary" type="submit" :disabled="resetLoading">Set New Password</button>
                                        </div>
                                    </form>

                                    <div v-else class="text-center py-3">
                                        <p class="mb-3">Your password has been updated.</p>
                                        <Link href="/login" class="btn btn-primary btn-lg">Go to Login</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mb-3">Back to <Link href="/login">Login</Link></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<template>
    <div
        class="min-h-screen relative flex items-center justify-center overflow-hidden bg-gradient-to-br from-sky-50 via-white to-sky-100">
        <!-- Background -->
        <div
            class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-sky-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob">
        </div>

        <div
            class="absolute top-[20%] right-[-10%] w-96 h-96 bg-cyan-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000">
        </div>

        <div
            class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-sky-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000">
        </div>

        <!-- Card -->
        <div class="relative w-full max-w-md px-6 py-12 lg:px-8 z-10">
            <div
                class="backdrop-blur-xl bg-white/70 shadow-[0_8px_30px_rgb(0,0,0,0.04)] ring-1 ring-sky-900/5 rounded-3xl p-8 sm:p-10">
                <!-- Header -->
                <div class="text-center">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-sky-100 mb-6 shadow-inner">
                        <MailOpen class="w-8 h-8 text-sky-600" />
                    </div>

                    <h2 class="text-3xl font-bold tracking-tight text-slate-800">
                        Reset Password
                    </h2>

                    <p class="mt-2 text-sm text-slate-500">
                        Enter your email to receive a password reset link
                    </p>
                </div>

                <!-- Form -->
                <div class="mt-10">
                    <!-- Success State -->
                    <div v-if="submitted" class="text-center space-y-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 mb-6">
                            <CheckCircle class="w-8 h-8 text-emerald-600" />
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-slate-800 mb-2">Check your email</h3>
                            <p class="text-sm text-slate-600 mb-4">
                                We've sent password reset instructions to <strong>{{ form.email }}</strong>
                            </p>
                            <p class="text-xs text-slate-400">
                                If you don't see the email, check your spam folder or try again.
                            </p>
                        </div>

                        <div class="pt-4 space-y-3">
                            <button @click="handleBackToLogin"
                                class="w-full flex items-center justify-center rounded-xl bg-sky-500 px-4 py-3 text-sm font-semibold text-white hover:bg-sky-400 transition-all cursor-pointer">
                                <ArrowLeft class="w-4 h-4 mr-2" />
                                Back to Login
                            </button>
                        </div>
                    </div>

                    <!-- Form State -->
                    <form v-else class="space-y-6" @submit.prevent="handleForgotPassword">

                        <!-- Error -->
                        <div v-if="error"
                            class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg flex items-center">
                            <CircleAlert class="w-5 h-5 text-red-500 mr-3" />

                            <p class="text-sm text-red-700 font-medium">
                                {{ error }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">
                                Email Address
                            </label>

                            <div class="relative">
                                <Mail class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />

                                <input id="email" v-model="form.email" type="email" name="email" autocomplete="email"
                                    required placeholder="you@example.com"
                                    class="block w-full rounded-xl border-0 py-2.5 pl-10 text-slate-900 bg-white/50 shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-sky-500 focus:bg-white sm:text-sm" />
                            </div>
                            <p class="text-xs text-slate-400 mt-1">
                                We'll send you a link to reset your password
                            </p>
                        </div>

                        <!-- Button -->
                        <button type="submit" :disabled="loading"
                            class="group flex w-full justify-center rounded-xl bg-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 hover:bg-sky-400 transition-all disabled:opacity-70 cursor-pointer">
                            <span v-if="!loading" class="flex items-center">
                                Send Reset Link

                                <ArrowRight class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" />
                            </span>

                            <span v-else class="flex items-center">
                                <LoaderCircle class="animate-spin mr-3 h-5 w-5" />

                                Sending...
                            </span>
                        </button>
                    </form>

                    <!-- Back to Login -->
                    <p class="mt-8 text-center text-sm text-slate-500">
                        Remember your password?

                        <router-link to="/login" class="font-semibold text-sky-600 hover:text-sky-500">
                            Sign in
                        </router-link>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';

import {
    ArrowLeft,
    ArrowRight,
    CheckCircle,
    CircleAlert,
    LoaderCircle,
    Mail,
    MailOpen,
} from 'lucide-vue-next';

import api from '../services/api';

const router = useRouter();

const form = ref({
    email: '',
});

const error = ref('');
const loading = ref(false);
const submitted = ref(false);

const handleForgotPassword = async () => {
    loading.value = true;
    error.value = '';

    try {
        const response = await api.post('/forgot-password', form.value);

        submitted.value = true;
    } catch (err: any) {
        error.value =
            err.response?.data?.message ||
            'Failed to send reset link. Please try again.';
    } finally {
        loading.value = false;
    }
};

const handleBackToLogin = () => {
    router.push('/login');
};
</script>

<style>
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }

    33% {
        transform: translate(30px, -50px) scale(1.1);
    }

    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }

    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>

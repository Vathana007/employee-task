<template>
    <div
        class="min-h-screen relative flex items-center justify-center overflow-hidden bg-gradient-to-br from-sky-50 via-white to-sky-100"
    >
        <!-- Background -->
        <div
            class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-sky-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"
        ></div>
        <div
            class="absolute top-[20%] right-[-10%] w-96 h-96 bg-cyan-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"
        ></div>
        <div
            class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-sky-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"
        ></div>

        <!-- Card -->
        <div class="relative w-full max-w-md px-6 py-12 lg:px-8 z-10">
            <div
                class="backdrop-blur-xl bg-white/70 shadow-[0_8px_30px_rgb(0,0,0,0.04)] ring-1 ring-sky-900/5 rounded-3xl p-8 sm:p-10"
            >
                <!-- Loading State -->
                <div v-if="verifying" class="text-center">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-sky-100 mb-6 shadow-inner"
                    >
                        <LoaderCircle
                            class="w-8 h-8 text-sky-600 animate-spin"
                        />
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">
                        Verifying Email...
                    </h2>
                    <p class="text-slate-500 mt-2">
                        Please wait while we verify your email address.
                    </p>
                </div>

                <!-- Success State -->
                <div v-else-if="verified" class="text-center">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-6 shadow-inner"
                    >
                        <CheckCircle class="w-8 h-8 text-green-600" />
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">
                        Email Verified!
                    </h2>
                    <p class="text-slate-500 mt-2">
                        Your email has been successfully verified. You can now
                        login.
                    </p>

                    <button
                        @click="goToLogin"
                        class="mt-6 w-full bg-sky-500 text-white py-2 rounded-lg hover:bg-sky-600"
                    >
                        Go to Login
                    </button>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="text-center">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-6 shadow-inner"
                    >
                        <CircleAlert class="w-8 h-8 text-red-600" />
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">
                        Verification Failed
                    </h2>
                    <p class="text-red-600 mt-2">{{ error }}</p>

                    <div class="mt-6 space-y-2">
                        <button
                            @click="resendEmail"
                            class="w-full bg-sky-500 text-white py-2 rounded-lg hover:bg-sky-600"
                        >
                            Resend Verification Email
                        </button>
                        <button
                            @click="goToLogin"
                            class="w-full bg-slate-200 text-slate-800 py-2 rounded-lg hover:bg-slate-300"
                        >
                            Back to Login
                        </button>
                    </div>
                </div>

                <!-- Initial State (No Query Params) -->
                <div v-else class="text-center">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-sky-100 mb-6 shadow-inner"
                    >
                        <Mail class="w-8 h-8 text-sky-600" />
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">
                        Check Your Email
                    </h2>
                    <p class="text-slate-500 mt-2">
                        We've sent a verification link to your email. Click the
                        link to verify.
                    </p>

                    <div v-if="email" class="mt-4 p-4 bg-sky-50 rounded-lg">
                        <p class="text-sm text-slate-600">
                            Verification link sent to:<br /><strong>{{
                                email
                            }}</strong>
                        </p>
                    </div>

                    <button
                        @click="goToLogin"
                        class="mt-6 w-full bg-slate-200 text-slate-800 py-2 rounded-lg hover:bg-slate-300"
                    >
                        Back to Login
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { CheckCircle, CircleAlert, LoaderCircle, Mail } from "lucide-vue-next";
import api from "../services/api";

const router = useRouter();
const route = useRoute();

const verifying = ref(false);
const verified = ref(false);
const error = ref("");
const email = ref("");

onMounted(async () => {
    email.value = (route.query.email as string) || "";
    const token = (route.query.token as string) || "";

    // If has token, auto-verify
    if (email.value && token) {
        await verifyEmail(email.value, token);
    }
});

const verifyEmail = async (userEmail: string, userToken: string) => {
    verifying.value = true;
    error.value = "";

    try {
        const response = await api.post("/verify-email", {
            email: userEmail,
            token: userToken,
        });

        verified.value = true;
    } catch (err: any) {
        error.value =
            err.response?.data?.message ||
            "Verification failed. Link may have expired.";
    } finally {
        verifying.value = false;
    }
};

const resendEmail = async () => {
    if (!email.value) {
        alert("Email address not found. Please register again.");
        return;
    }

    verifying.value = true;
    error.value = "";

    try {
        await api.post("/send-verification-email", {
            email: email.value,
        });

        alert("Verification email sent! Check your inbox.");
    } catch (err: any) {
        error.value = err.response?.data?.message || "Failed to resend email";
    } finally {
        verifying.value = false;
    }
};

const goToLogin = () => {
    router.push("/login");
};
</script>

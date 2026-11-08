<template>
    <AppLayout :title="t('errorLog.title')" :subtitle="t('errorLog.subtitle')">
        <div
            v-if="error"
            class="mb-5 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg text-sm text-red-700"
        >
            {{ error }}
        </div>

        <!-- Header Section -->
        <section
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-500 to-indigo-600 p-6 sm:p-8 text-white shadow-lg mb-6"
        >
            <div
                class="absolute -right-10 -top-10 w-44 h-44 rounded-full bg-white/10"
            ></div>
            <div
                class="absolute right-20 -bottom-16 w-56 h-56 rounded-full bg-white/5"
            ></div>
            <div class="relative z-10">
                <p class="text-indigo-200 text-sm font-medium mb-1">
                    {{ t("errorLog.systemHealth") }}
                </p>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">
                    {{ t("errorLog.errorMonitoring") }}
                </h1>
                <p class="text-indigo-100 text-sm leading-relaxed max-w-lg">
                    {{ t("errorLog.heroDesc") }}
                </p>
            </div>
        </section>

        <!-- Summary Cards -->
        <section class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
            <div
                class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition"
            >
                <div
                    class="w-10 h-10 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center mb-3"
                >
                    <BarChart3 class="w-5 h-5" />
                </div>
                <p class="text-xs text-slate-400">
                    {{ t("errorLog.totalErrors") }}
                </p>
                <p class="text-2xl font-bold text-slate-800 mt-1">
                    {{ summary.total_errors }}
                </p>
            </div>

            <div
                class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition"
            >
                <div
                    class="w-10 h-10 rounded-lg bg-red-50 text-red-500 flex items-center justify-center mb-3"
                >
                    <AlertCircle class="w-5 h-5" />
                </div>
                <p class="text-xs text-slate-400">
                    {{ t("errorLog.activeErrors") }}
                </p>
                <p class="text-2xl font-bold text-slate-800 mt-1">
                    {{ summary.active_errors }}
                </p>
            </div>

            <div
                class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition"
            >
                <div
                    class="w-10 h-10 rounded-lg bg-yellow-50 text-yellow-500 flex items-center justify-center mb-3"
                >
                    <Lock class="w-5 h-5" />
                </div>
                <p class="text-xs text-slate-400">
                    {{ t("errorLog.loginAttempts") }}
                </p>
                <p class="text-2xl font-bold text-slate-800 mt-1">
                    {{ summary.login_attempts }}
                </p>
            </div>

            <div
                class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition"
            >
                <div
                    class="w-10 h-10 rounded-lg bg-purple-50 text-purple-500 flex items-center justify-center mb-3"
                >
                    <Globe class="w-5 h-5" />
                </div>
                <p class="text-xs text-slate-400">
                    {{ t("errorLog.uniqueIps") }}
                </p>
                <p class="text-2xl font-bold text-slate-800 mt-1">
                    {{ summary.unique_ips }}
                </p>
            </div>
        </section>

        <!-- Filters -->
        <div class="flex gap-3 mb-6 flex-wrap">
            <button
                @click="loadAllErrors"
                :class="[
                    'px-4 py-2 rounded-lg font-medium transition text-sm cursor-pointer',
                    selectedType === null
                        ? 'bg-sky-500 text-white shadow-sm'
                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200',
                ]"
            >
                {{ t("errorLog.allErrors") }}
            </button>
            <button
                @click="filterByType('login_attempt')"
                :class="[
                    'px-4 py-2 rounded-lg font-medium transition text-sm cursor-pointer',
                    selectedType === 'login_attempt'
                        ? 'bg-sky-500 text-white shadow-sm'
                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200',
                ]"
            >
                {{ t("errorLog.loginAttempts") }}
            </button>
            <button
                @click="filterByType('system_error')"
                :class="[
                    'px-4 py-2 rounded-lg font-medium transition text-sm cursor-pointer',
                    selectedType === 'system_error'
                        ? 'bg-sky-500 text-white shadow-sm'
                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200',
                ]"
            >
                {{ t("errorLog.systemErrors") }}
            </button>
        </div>

        <!-- Log List Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="p-5 border-b border-slate-200">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">
                        {{ t("nav.errorLogs") }}
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ t("errorLog.subtitle") }}
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-slate-700"
                            >
                                {{ t("errorLog.type") }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-slate-700"
                            >
                                {{ t("errorLog.message") }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-slate-700"
                            >
                                {{ t("errorLog.ipAddress") }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-slate-700"
                            >
                                {{ t("errorLog.loginAttempts") }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-slate-700"
                            >
                                {{ t("errorLog.time") }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-slate-700"
                            >
                                {{ t("common.status") }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-slate-700"
                            >
                                {{ t("common.actions") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-if="loading" class="hover:bg-slate-50">
                            <td
                                colspan="7"
                                class="px-6 py-4 text-center text-slate-400 text-sm animate-pulse"
                            >
                                {{ t("common.loading") }}
                            </td>
                        </tr>
                        <tr
                            v-else-if="errors.length === 0"
                            class="hover:bg-slate-50"
                        >
                            <td
                                colspan="7"
                                class="px-6 py-4 text-center text-slate-400 text-sm"
                            >
                                {{ t("errorLog.noErrors") }}
                            </td>
                        </tr>
                        <tr
                            v-for="err in errors"
                            :key="err.id"
                            class="hover:bg-slate-50 transition"
                        >
                            <td class="px-6 py-4 text-sm">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                >
                                    {{ errorTypeLabel(err.error_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ messageLabel(err.message) }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm font-mono text-slate-800"
                            >
                                {{ err.ip_address }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-slate-800 font-semibold"
                            >
                                {{ err.attempt_count }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ formatDate(err.created_at) }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span
                                    :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        err.status === 'active'
                                            ? 'bg-emerald-100 text-emerald-800'
                                            : 'bg-red-100 text-red-800',
                                    ]"
                                >
                                    {{ statusLabel(err.status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <button
                                    v-if="
                                        err.status === 'active' &&
                                        isErrorType(err.error_type)
                                    "
                                    @click="resolveError(err.id)"
                                    class="px-3 py-1 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-xs font-medium cursor-pointer"
                                >
                                    {{ t("errorLog.resolve") }}
                                </button>
                                <span v-else class="text-slate-400 text-xs">
                                    {{
                                        err.status === "resolved"
                                            ? t("errorLog.resolved")
                                            : t("errorLog.normalActivity")
                                    }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { AlertCircle, BarChart3, Lock, Globe } from "lucide-vue-next";
import AppLayout from "../components/layout/AppLayout.vue";
import api from "../services/api";

const { t, te, locale } = useI18n();

interface AuditLog {
    id: number;
    error_type: string;
    message: string;
    ip_address: string;
    attempt_count: number;
    status: string;
    created_at: string;
}

interface Summary {
    total_errors: number;
    active_errors: number;
    login_attempts: number;
    unique_ips: number;
}

const isErrorType = (errorType: string) => {
    const errorTypes = [
        "login_failed",
        "system_error",
        "registration_failed",
        "email_error",
    ];
    return errorTypes.includes(errorType);
};

// Translate log type (falls back to a readable version of the raw value)
const errorTypeLabel = (errorType: string) => {
    const key = `errorLog.types.${errorType}`;
    if (te(key)) return t(key);
    return errorType
        .replace(/_/g, " ")
        .replace(/\b\w/g, (c) => c.toUpperCase());
};

// Translate known backend messages; unknown messages are shown unchanged
const messageLabel = (msg: string) => {
    let m = msg.match(/^Profile accessed by user:\s*(.+)$/i);
    if (m) return t("errorLog.messages.profileAccessed", { name: m[1] });

    m = msg.match(/^Login attempt for email:\s*(.+)$/i);
    if (m) return t("errorLog.messages.loginAttemptEmail", { email: m[1] });

    return msg;
};

// Translate status (active / resolved)
const statusLabel = (status: string) => {
    return status === "active" ? t("common.active") : t("errorLog.resolved");
};

const errors = ref<AuditLog[]>([]);
const summary = ref<Summary>({
    total_errors: 0,
    active_errors: 0,
    login_attempts: 0,
    unique_ips: 0,
});
const selectedType = ref<string | null>(null);
const loading = ref(false);
const error = ref("");

onMounted(() => {
    loadSummary();
    loadAllErrors();
});

const loadSummary = async () => {
    try {
        const response = await api.get("/admin/errors/summary");
        summary.value = response.data;
    } catch (e: any) {
        error.value =
            e.response?.data?.message || t("errorLog.loadSummaryFailed");
    }
};

const loadAllErrors = async () => {
    loading.value = true;
    try {
        const response = await api.get("/admin/errors");
        errors.value = response.data.errors.data || response.data.errors;
        selectedType.value = null;
    } catch (e: any) {
        error.value =
            e.response?.data?.message || t("errorLog.loadErrorsFailed");
    } finally {
        loading.value = false;
    }
};

const filterByType = async (type: string) => {
    loading.value = true;
    try {
        const response = await api.get(`/admin/errors/type/${type}`);
        errors.value = response.data.errors;
        selectedType.value = type;
    } catch (e: any) {
        error.value = e.response?.data?.message || t("errorLog.filterFailed");
    } finally {
        loading.value = false;
    }
};

const resolveError = async (id: number) => {
    try {
        await api.post(`/admin/errors/${id}/resolve`);
        loadAllErrors();
        loadSummary();
    } catch (e: any) {
        error.value = e.response?.data?.message || t("errorLog.resolveFailed");
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleString(locale.value);
};
</script>

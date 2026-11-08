<template>
    <AppLayout :title="t('projects.title')" :subtitle="t('projects.subtitle')">
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6"
        >
            <div class="relative flex-1 max-w-md">
                <Search
                    class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                />
                <input
                    v-model="searchQuery"
                    type="text"
                    :placeholder="t('projects.searchPlaceholder')"
                    class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 transition-shadow"
                />
            </div>
            <button
                @click="openCreate"
                class="inline-flex items-center justify-center gap-2 bg-sky-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm cursor-pointer whitespace-nowrap"
            >
                <Plus class="w-4 h-4" />
                {{ t("projects.newProject") }}
            </button>
        </div>

        <div
            v-if="error"
            class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg text-sm text-red-700"
        >
            {{ error }}
        </div>

        <div
            v-if="loading"
            class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4"
        >
            <div
                v-for="i in 3"
                :key="i"
                class="bg-white rounded-xl border border-slate-200 p-5 animate-pulse"
            >
                <div class="h-4 bg-slate-100 rounded w-3/4 mb-3"></div>
                <div class="h-3 bg-slate-100 rounded w-full mb-2"></div>
                <div class="h-3 bg-slate-100 rounded w-2/3"></div>
            </div>
        </div>

        <div
            v-else-if="filteredProjects.length === 0"
            class="text-center py-16 bg-white rounded-xl border border-slate-200 shadow-sm"
        >
            <FolderOpen class="w-12 h-12 text-slate-200 mx-auto mb-3" />
            <p class="text-slate-500 font-medium">
                {{ t("projects.noProjectsFound") }}
            </p>
            <p class="text-slate-400 text-sm mt-1" v-if="searchQuery">
                {{ t("common.tryAdjustSearch") }}
            </p>
            <p class="text-slate-400 text-sm mt-1" v-else>
                {{ t("projects.clickNewToStart") }}
            </p>
        </div>

        <div
            v-else
            class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5"
        >
            <div
                v-for="project in filteredProjects"
                :key="project.id"
                @click="openView(project)"
                class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md transition cursor-pointer group flex flex-col h-full"
            >
                <div class="flex items-start justify-between mb-3">
                    <div
                        class="w-10 h-10 rounded-lg bg-sky-50 text-sky-500 flex items-center justify-center"
                    >
                        <FolderOpen class="w-5 h-5" />
                    </div>
                    <span
                        :class="statusClass(project.status)"
                        class="text-xs font-semibold px-2.5 py-1 rounded-full capitalize"
                    >
                        {{ t(`status.${project.status}`) }}
                    </span>
                </div>
                <h3
                    class="font-bold text-slate-800 mb-1 group-hover:text-sky-600 transition"
                >
                    {{ project.name }}
                </h3>
                <p
                    class="text-xs text-slate-400 leading-relaxed line-clamp-2 mb-4"
                >
                    {{
                        project.description || t("common.noDescriptionProvided")
                    }}
                </p>

                <!-- Progress -->
                <div class="mb-4">
                    <div
                        class="flex justify-between text-xs text-slate-500 mb-1"
                    >
                        <span
                            >{{ project.completed_tasks_count ?? 0 }}/{{
                                project.tasks_count ?? 0
                            }}
                            {{ t("projects.tasksDone") }}</span
                        >
                        <span class="font-semibold"
                            >{{ progress(project) }}%</span
                        >
                    </div>
                    <div
                        class="h-1.5 bg-slate-100 rounded-full overflow-hidden"
                    >
                        <div
                            class="h-full bg-emerald-500 rounded-full transition-all"
                            :style="{ width: progress(project) + '%' }"
                        ></div>
                    </div>
                </div>

                <div
                    class="flex items-center justify-between pt-4 mt-auto border-t border-slate-100"
                    @click.stop
                >
                    <button
                        @click="openView(project)"
                        class="text-xs font-semibold text-slate-500 hover:text-sky-600 transition cursor-pointer flex items-center gap-1.5"
                    >
                        <Eye class="w-4 h-4" /> {{ t("common.view") }}
                    </button>
                    <div class="flex items-center gap-3">
                        <button
                            @click="openEdit(project)"
                            class="text-xs font-semibold text-sky-600 hover:text-sky-700 transition cursor-pointer flex items-center gap-1.5"
                        >
                            <Edit2 class="w-4 h-4" /> {{ t("common.edit") }}
                        </button>
                        <button
                            @click="confirmDelete(project.id)"
                            class="text-xs font-semibold text-red-500 hover:text-red-600 transition cursor-pointer flex items-center gap-1.5"
                        >
                            <Trash2 class="w-4 h-4" /> {{ t("common.delete") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Modal -->
        <AppModal v-model="showViewModal" :title="t('projects.projectDetails')">
            <div v-if="viewTarget" class="space-y-5">
                <div>
                    <p
                        class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                    >
                        {{ t("projects.projectName") }}
                    </p>
                    <p class="text-sm font-medium text-slate-800">
                        {{ viewTarget.name }}
                    </p>
                </div>
                <div>
                    <p
                        class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                    >
                        {{ t("common.status") }}
                    </p>
                    <span
                        :class="statusClass(viewTarget.status)"
                        class="text-xs font-semibold px-2.5 py-1 rounded-full capitalize inline-block mt-1"
                    >
                        {{ t(`status.${viewTarget.status}`) }}
                    </span>
                </div>
                <div>
                    <p
                        class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                    >
                        {{ t("common.progress") }}
                    </p>
                    <div
                        class="flex justify-between text-xs text-slate-500 mb-1 mt-1"
                    >
                        <span
                            >{{ viewTarget.completed_tasks_count ?? 0 }}/{{
                                viewTarget.tasks_count ?? 0
                            }}
                            {{ t("projects.tasksDone") }}</span
                        >
                        <span class="font-semibold"
                            >{{ progress(viewTarget) }}%</span
                        >
                    </div>
                    <div
                        class="h-1.5 bg-slate-100 rounded-full overflow-hidden"
                    >
                        <div
                            class="h-full bg-emerald-500 rounded-full transition-all"
                            :style="{ width: progress(viewTarget) + '%' }"
                        ></div>
                    </div>
                </div>
                <div>
                    <p
                        class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                    >
                        {{ t("common.description") }}
                    </p>
                    <p
                        class="text-sm text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-lg border border-slate-100 mt-1"
                    >
                        {{
                            viewTarget.description ||
                            t("common.noDescriptionProvided")
                        }}
                    </p>
                </div>
                <div
                    class="grid grid-cols-2 gap-4 pt-5 border-t border-slate-100"
                >
                    <div>
                        <p
                            class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                        >
                            {{ t("common.created") }}
                        </p>
                        <p class="text-sm text-slate-800">
                            {{ fmtDate(viewTarget.created_at) }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                        >
                            {{ t("common.lastUpdated") }}
                        </p>
                        <p class="text-sm text-slate-800">
                            {{ fmtDate(viewTarget.updated_at) }}
                        </p>
                    </div>
                </div>
                <div class="pt-2 flex justify-end">
                    <button
                        @click="showViewModal = false"
                        class="px-5 py-2.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition cursor-pointer"
                    >
                        {{ t("common.close") }}
                    </button>
                </div>
            </div>
        </AppModal>

        <!-- Create/Edit Modal -->
        <AppModal
            v-model="showModal"
            :title="
                editTarget
                    ? t('projects.editProject')
                    : t('projects.newProject')
            "
            size="md"
        >
            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div
                    v-if="formError"
                    class="bg-red-50 text-red-700 text-sm p-3 rounded-lg border-l-4 border-red-500"
                >
                    {{ formError }}
                </div>

                <div>
                    <label
                        class="block text-sm font-semibold text-slate-700 mb-1"
                        >{{ t("projects.projectName") }}</label
                    >
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        :placeholder="t('projects.namePlaceholder')"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                </div>

                <div>
                    <label
                        class="block text-sm font-semibold text-slate-700 mb-1"
                        >{{ t("common.description") }}</label
                    >
                    <textarea
                        v-model="form.description"
                        rows="3"
                        :placeholder="t('common.optionalDescription')"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 resize-none"
                    ></textarea>
                </div>

                <div>
                    <label
                        class="block text-sm font-semibold text-slate-700 mb-1"
                        >{{ t("common.status") }}</label
                    >
                    <select
                        v-model="form.status"
                        required
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                    >
                        <option value="pending">
                            {{ t("status.pending") }}
                        </option>
                        <option value="in_progress">
                            {{ t("status.in_progress") }}
                        </option>
                        <option value="completed">
                            {{ t("status.completed") }}
                        </option>
                    </select>
                </div>

                <div class="flex gap-3 pt-2">
                    <button
                        type="button"
                        @click="showModal = false"
                        class="flex-1 py-2.5 rounded-lg border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer"
                    >
                        {{ t("common.cancel") }}
                    </button>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="flex-1 py-2.5 rounded-lg bg-sky-500 text-white text-sm font-semibold hover:bg-sky-600 transition disabled:opacity-60 cursor-pointer"
                    >
                        {{
                            submitting
                                ? t("common.saving")
                                : editTarget
                                  ? t("common.update")
                                  : t("common.create")
                        }}
                    </button>
                </div>
            </form>
        </AppModal>

        <!-- Delete Confirmation Modal -->
        <AppModal
            v-model="showDeleteModal"
            :title="t('common.confirmDeletion')"
        >
            <div class="space-y-4">
                <div
                    class="flex items-start gap-4 p-4 bg-red-50 rounded-lg border border-red-100 text-red-800"
                >
                    <AlertTriangle
                        class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5"
                    />
                    <div>
                        <h4 class="text-sm font-semibold mb-1">
                            {{ t("common.areYouSure") }}
                        </h4>
                        <p class="text-sm text-red-600">
                            {{ t("projects.deleteWarning") }}
                        </p>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button
                        @click="showDeleteModal = false"
                        :disabled="deleting"
                        class="flex-1 py-2.5 rounded-lg border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer disabled:opacity-50"
                    >
                        {{ t("common.cancel") }}
                    </button>
                    <button
                        @click="executeDelete"
                        :disabled="deleting"
                        class="flex-1 py-2.5 rounded-lg bg-red-500 text-white text-sm font-semibold hover:bg-red-600 transition disabled:opacity-60 cursor-pointer flex items-center justify-center gap-2"
                    >
                        <Trash2 class="w-4 h-4" v-if="!deleting" />
                        {{
                            deleting
                                ? t("common.deleting")
                                : t("projects.deleteTitle")
                        }}
                    </button>
                </div>
            </div>
        </AppModal>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import {
    Plus,
    FolderOpen,
    Search,
    Eye,
    Edit2,
    Trash2,
    AlertTriangle,
} from "lucide-vue-next";
import AppLayout from "../components/layout/AppLayout.vue";
import AppModal from "../components/ui/AppModal.vue";
import { useProjects } from "../composables/useProjects.js";
import type { Project } from "../types/index.js";

const { t, locale } = useI18n();

const {
    projects,
    loading,
    error,
    fetchProjects,
    createProject,
    updateProject,
    deleteProject,
} = useProjects();

const searchQuery = ref("");
const showModal = ref(false);
const showViewModal = ref(false);
const showDeleteModal = ref(false);
const editTarget = ref<Project | null>(null);
const viewTarget = ref<Project | null>(null);
const deleteTargetId = ref<number | null>(null);
const submitting = ref(false);
const deleting = ref(false);
const formError = ref("");

// Format dates in the active app language
const fmtDate = (d: string) => new Date(d).toLocaleDateString(locale.value);

const filteredProjects = computed(() => {
    if (!searchQuery.value) return projects.value;
    const lower = searchQuery.value.toLowerCase();
    return projects.value.filter(
        (p) =>
            p.name.toLowerCase().includes(lower) ||
            (p.description && p.description.toLowerCase().includes(lower)),
    );
});

const defaultForm = () => ({
    name: "",
    description: "",
    status: "pending" as Project["status"],
});
const form = ref(defaultForm());

const statusClass = (s: string) => ({
    "bg-emerald-50 text-emerald-600": s === "completed",
    "bg-sky-50 text-sky-600": s === "in_progress",
    "bg-amber-50 text-amber-600": s === "pending",
});

const progress = (p: Project) =>
    p.tasks_count
        ? Math.round(((p.completed_tasks_count ?? 0) / p.tasks_count) * 100)
        : 0;

const openCreate = () => {
    editTarget.value = null;
    form.value = defaultForm();
    formError.value = "";
    showModal.value = true;
};

const openView = (project: Project) => {
    viewTarget.value = project;
    showViewModal.value = true;
};

const openEdit = (project: Project) => {
    editTarget.value = project;
    form.value = {
        name: project.name,
        description: project.description ?? "",
        status: project.status,
    };
    formError.value = "";
    showModal.value = true;
};

const handleSubmit = async () => {
    submitting.value = true;
    formError.value = "";
    try {
        if (editTarget.value) {
            await updateProject(editTarget.value.id, form.value);
        } else {
            await createProject(form.value);
        }
        showModal.value = false;
    } catch (e: any) {
        formError.value =
            e.response?.data?.message ?? t("common.somethingWentWrong");
    } finally {
        submitting.value = false;
    }
};

const confirmDelete = (id: number) => {
    deleteTargetId.value = id;
    showDeleteModal.value = true;
};

const executeDelete = async () => {
    if (!deleteTargetId.value) return;
    deleting.value = true;
    try {
        await deleteProject(deleteTargetId.value);
        showDeleteModal.value = false;
    } catch {
        /* ignore */
    } finally {
        deleting.value = false;
    }
};

onMounted(fetchProjects);
</script>

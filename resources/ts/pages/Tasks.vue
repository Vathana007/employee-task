<template>
    <AppLayout :title="t('tasks.title')" :subtitle="t('tasks.subtitle')">
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
                    :placeholder="t('tasks.searchPlaceholder')"
                    class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 transition-shadow"
                />
            </div>
            <button
                @click="openCreate"
                class="inline-flex items-center justify-center gap-2 bg-sky-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm cursor-pointer whitespace-nowrap"
            >
                <Plus class="w-4 h-4" />
                {{ t("tasks.newTask") }}
            </button>
        </div>

        <div
            v-if="error"
            class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg text-sm text-red-700"
        >
            {{ error }}
        </div>

        <div
            class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden"
        >
            <div v-if="loading" class="p-5 space-y-3">
                <div
                    v-for="i in 4"
                    :key="i"
                    class="h-14 bg-slate-50 rounded-lg animate-pulse"
                ></div>
            </div>
            <div
                v-else-if="filteredTasks.length === 0"
                class="text-center py-16"
            >
                <CheckCircle2 class="w-12 h-12 text-slate-200 mx-auto mb-3" />
                <p class="text-slate-500 font-medium">
                    {{ t("tasks.noTasksFound") }}
                </p>
                <p class="text-slate-400 text-sm mt-1" v-if="searchQuery">
                    {{ t("common.tryAdjustSearch") }}
                </p>
                <p class="text-slate-400 text-sm mt-1" v-else>
                    {{ t("tasks.clickNewToStart") }}
                </p>
            </div>
            <table v-else class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th
                            class="w-full text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider"
                        >
                            {{ t("tasks.task") }}
                        </th>
                        <th
                            class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider hidden md:table-cell"
                        >
                            {{ t("tasks.priority") }}
                        </th>
                        <th
                            class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider"
                        >
                            {{ t("common.status") }}
                        </th>
                        <th
                            class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider hidden lg:table-cell"
                        >
                            {{ t("tasks.startDate") }}
                        </th>
                        <th
                            class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider hidden lg:table-cell"
                        >
                            {{ t("tasks.endDate") }}
                        </th>
                        <th
                            class="text-right px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider"
                        >
                            {{ t("common.actions") }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr
                        v-for="task in filteredTasks"
                        :key="task.id"
                        @click="openView(task)"
                        class="hover:bg-slate-50 transition cursor-pointer"
                    >
                        <td class="px-5 py-4">
                            <p
                                class="font-semibold text-slate-800 hover:text-sky-600 transition"
                            >
                                {{ task.title }}
                            </p>
                            <p
                                class="text-xs text-slate-400 mt-0.5 truncate max-w-xs"
                            >
                                {{ task.description || "—" }}
                            </p>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span
                                :class="priorityClass(task.priority)"
                                class="text-xs font-semibold px-2 py-0.5 rounded-full capitalize"
                            >
                                {{ t(`priority.${task.priority}`) }}
                            </span>
                        </td>
                        <td class="px-5 py-4" @click.stop>
                            <StatusSelect
                                :model-value="task.status"
                                :disabled="updatingId === task.id"
                                @change="changeStatus(task, $event)"
                            />
                        </td>
                        <td
                            class="px-5 py-4 text-slate-500 hidden lg:table-cell"
                        >
                            {{
                                task.start_date ? fmtDate(task.start_date) : "—"
                            }}
                        </td>
                        <td
                            class="px-5 py-4 text-slate-500 hidden lg:table-cell"
                        >
                            {{ task.end_date ? fmtDate(task.end_date) : "—" }}
                        </td>
                        <td class="px-5 py-4" @click.stop>
                            <div class="flex items-center gap-1 justify-end">
                                <button
                                    @click="openView(task)"
                                    :title="t('common.view')"
                                    class="text-slate-400 hover:text-slate-500 hover:bg-slate-100 rounded-lg p-2 transition cursor-pointer"
                                >
                                    <Eye class="w-4 h-4" />
                                </button>
                                <button
                                    @click="openEdit(task)"
                                    :title="t('common.edit')"
                                    class="text-sky-400 hover:text-sky-500 hover:bg-sky-100 rounded-lg p-2 transition cursor-pointer"
                                >
                                    <Edit2 class="w-4 h-4" />
                                </button>
                                <button
                                    @click="confirmDelete(task.id)"
                                    :title="t('common.delete')"
                                    class="text-red-500 hover:text-red-600 hover:bg-red-100 rounded-lg p-2 transition cursor-pointer"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- View Modal -->
        <AppModal v-model="showViewModal" :title="t('tasks.taskDetails')">
            <div v-if="viewTarget" class="space-y-5">
                <div>
                    <p
                        class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                    >
                        {{ t("tasks.taskTitle") }}
                    </p>
                    <p class="text-sm font-medium text-slate-800">
                        {{ viewTarget.title }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p
                            class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                        >
                            {{ t("common.status") }}
                        </p>
                        <div class="mt-1">
                            <StatusSelect
                                :model-value="viewTarget.status"
                                :disabled="updatingId === viewTarget.id"
                                @change="changeStatus(viewTarget, $event)"
                            />
                        </div>
                    </div>
                    <div>
                        <p
                            class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                        >
                            {{ t("tasks.priority") }}
                        </p>
                        <span
                            :class="priorityClass(viewTarget.priority)"
                            class="text-xs font-semibold px-2.5 py-1 rounded-full capitalize inline-block mt-1"
                        >
                            {{ t(`priority.${viewTarget.priority}`) }}
                        </span>
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
                            {{ t("tasks.project") }}
                        </p>
                        <p
                            class="text-sm text-slate-800 flex items-center gap-1.5"
                        >
                            <FolderOpen class="w-3.5 h-3.5 text-slate-400" />
                            {{ getProjectName(viewTarget.project_id) }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                        >
                            {{ t("tasks.assignedTo") }}
                        </p>
                        <p
                            class="text-sm text-slate-800 flex items-center gap-1.5"
                        >
                            <Users class="w-3.5 h-3.5 text-slate-400" />
                            {{ getUserName(viewTarget.user_id) }}
                        </p>
                    </div>
                </div>

                <div
                    class="grid grid-cols-3 gap-4 pt-4 border-t border-slate-100"
                >
                    <div>
                        <p
                            class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                        >
                            {{ t("tasks.startDate") }}
                        </p>
                        <p class="text-sm text-slate-800">
                            {{
                                viewTarget.start_date
                                    ? fmtDate(viewTarget.start_date)
                                    : t("common.none")
                            }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                        >
                            {{ t("tasks.endDate") }}
                        </p>
                        <p class="text-sm text-slate-800">
                            {{
                                viewTarget.end_date
                                    ? fmtDate(viewTarget.end_date)
                                    : t("common.none")
                            }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                        >
                            {{ t("tasks.createdAt") }}
                        </p>
                        <p class="text-sm text-slate-800">
                            {{ fmtDate(viewTarget.created_at) }}
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <p
                        class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-3"
                    >
                        {{ t("tasks.comments") }}
                    </p>
                    <div class="space-y-4 max-h-60 overflow-y-auto mb-4 p-1">
                        <div v-if="commentsLoading" class="text-center py-4">
                            <span class="text-xs text-slate-400">{{
                                t("tasks.loadingComments")
                            }}</span>
                        </div>
                        <div
                            v-else-if="comments.length === 0"
                            class="text-center py-4 bg-slate-50 rounded-lg"
                        >
                            <span class="text-xs text-slate-400">{{
                                t("tasks.noComments")
                            }}</span>
                        </div>
                        <template v-else>
                            <div
                                v-for="c in comments"
                                :key="c.id"
                                class="bg-slate-50 p-3 rounded-lg border border-slate-100 relative group"
                            >
                                <div class="flex items-center gap-2 mb-1">
                                    <div
                                        class="w-6 h-6 rounded-full bg-sky-500 text-white flex items-center justify-center text-[10px] font-bold"
                                    >
                                        {{ getUserName(c.user_id).charAt(0) }}
                                    </div>
                                    <span
                                        class="text-xs font-semibold text-slate-700"
                                        >{{ getUserName(c.user_id) }}</span
                                    >
                                    <span class="text-[10px] text-slate-400">{{
                                        fmtDate(c.created_at)
                                    }}</span>
                                </div>
                                <p class="text-sm text-slate-600 pl-8">
                                    {{ c.comment }}
                                </p>
                                <button
                                    @click="handleDeleteComment(c.id)"
                                    :title="t('tasks.deleteComment')"
                                    class="absolute top-2 right-2 text-slate-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition cursor-pointer"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </template>
                    </div>

                    <form @submit.prevent="handleAddComment" class="flex gap-2">
                        <input
                            v-model="newComment"
                            type="text"
                            :placeholder="t('tasks.addComment')"
                            required
                            class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                        />
                        <button
                            type="submit"
                            :disabled="submittingComment"
                            class="bg-sky-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-sky-600 transition disabled:opacity-60 cursor-pointer whitespace-nowrap"
                        >
                            {{
                                submittingComment
                                    ? t("tasks.posting")
                                    : t("tasks.postComment")
                            }}
                        </button>
                    </form>
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
            :title="editTarget ? t('tasks.editTask') : t('tasks.newTask')"
            size="lg"
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
                        >{{ t("common.title") }}</label
                    >
                    <input
                        v-model="form.title"
                        type="text"
                        required
                        :placeholder="t('tasks.titlePlaceholder')"
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
                        rows="2"
                        :placeholder="t('common.optionalDescription')"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 resize-none"
                    ></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label
                            class="block text-sm font-semibold text-slate-700 mb-1"
                            >{{ t("tasks.project") }}</label
                        >
                        <select
                            v-model="form.project_id"
                            required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 truncate"
                        >
                            <option value="" disabled>
                                {{ t("tasks.selectProject") }}
                            </option>
                            <option
                                v-for="p in projects"
                                :key="p.id"
                                :value="p.id"
                            >
                                {{ p.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-semibold text-slate-700 mb-1"
                            >{{ t("tasks.assignTo") }}</label
                        >
                        <select
                            v-model="form.user_id"
                            required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 truncate"
                        >
                            <option value="" disabled>
                                {{ t("tasks.selectUser") }}
                            </option>
                            <option
                                v-for="u in users"
                                :key="u.id"
                                :value="u.id"
                            >
                                {{ u.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label
                            class="block text-sm font-semibold text-slate-700 mb-1"
                            >{{ t("tasks.priority") }}</label
                        >
                        <select
                            v-model="form.priority"
                            required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                        >
                            <option value="low">{{ t("priority.low") }}</option>
                            <option value="medium">
                                {{ t("priority.medium") }}
                            </option>
                            <option value="high">
                                {{ t("priority.high") }}
                            </option>
                        </select>
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
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label
                            class="block text-sm font-semibold text-slate-700 mb-1"
                            >{{ t("tasks.startDate") }}</label
                        >
                        <input
                            v-model="form.start_date"
                            type="date"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-semibold text-slate-700 mb-1"
                            >{{ t("tasks.endDate") }}</label
                        >
                        <input
                            v-model="form.end_date"
                            type="date"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                        />
                    </div>
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
                            {{ t("tasks.deleteWarning") }}
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
                                : t("tasks.deleteTitle")
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
    CheckCircle2,
    Search,
    Eye,
    Edit2,
    Trash2,
    FolderOpen,
    Users,
    AlertTriangle,
} from "lucide-vue-next";
import AppLayout from "../components/layout/AppLayout.vue";
import AppModal from "../components/ui/AppModal.vue";
import StatusSelect from "../components/ui/StatusSelect.vue";
import { useTasks } from "../composables/useTasks.js";
import { useProjects } from "../composables/useProjects.js";
import { useUsers } from "../composables/useUsers.js";
import { useComments } from "../composables/useComments.js";
import { useLayout } from "../composables/useLayout.js";
import type { Task } from "../types/index.js";

const { t, locale } = useI18n();

const {
    tasks,
    loading,
    error,
    fetchTasks,
    createTask,
    updateTask,
    deleteTask,
} = useTasks();
const { projects, fetchProjects } = useProjects();
const { users, fetchUsers } = useUsers();
const {
    comments,
    loading: commentsLoading,
    fetchComments,
    createComment,
    deleteComment,
} = useComments();
const { user } = useLayout();

const searchQuery = ref("");
const showModal = ref(false);
const showViewModal = ref(false);
const showDeleteModal = ref(false);
const editTarget = ref<Task | null>(null);
const viewTarget = ref<Task | null>(null);
const deleteTargetId = ref<number | null>(null);
const submitting = ref(false);
const deleting = ref(false);
const formError = ref("");
const updatingId = ref<number | null>(null);

const newComment = ref("");
const submittingComment = ref(false);

// Format dates in the active app language
const fmtDate = (d: string) => new Date(d).toLocaleDateString(locale.value);

const filteredTasks = computed(() => {
    if (!searchQuery.value) return tasks.value;
    const lower = searchQuery.value.toLowerCase();
    return tasks.value.filter(
        (task) =>
            task.title.toLowerCase().includes(lower) ||
            (task.description &&
                task.description.toLowerCase().includes(lower)),
    );
});

const getProjectName = (id: number) =>
    projects.value.find((p) => p.id === id)?.name || t("tasks.unknownProject");
const getUserName = (id: number) =>
    users.value.find((u) => u.id === id)?.name || t("tasks.unknownUser");

const defaultForm = () => ({
    title: "",
    description: "",
    project_id: 0,
    user_id: 0,
    priority: "medium" as Task["priority"],
    status: "pending" as Task["status"],
    start_date: "",
    end_date: "",
});
const form = ref(defaultForm());

const priorityClass = (p: string) => ({
    "bg-red-50 text-red-600": p === "high",
    "bg-amber-50 text-amber-600": p === "medium",
    "bg-slate-100 text-slate-500": p === "low",
});

const changeStatus = async (task: Task, status: Task["status"]) => {
    if (task.status === status) return;
    updatingId.value = task.id;
    error.value = "";
    try {
        const updated = await updateTask(task.id, { status });
        // keep the open View modal in sync
        if (viewTarget.value?.id === updated.id) viewTarget.value = updated;
    } catch (e: any) {
        error.value =
            e.response?.data?.message ?? t("common.failedToUpdateStatus");
        await fetchTasks(); // resync dropdowns with the real value
    } finally {
        updatingId.value = null;
    }
};

const openCreate = () => {
    editTarget.value = null;
    form.value = defaultForm();
    formError.value = "";
    showModal.value = true;
};

const openView = async (task: Task) => {
    viewTarget.value = task;
    showViewModal.value = true;
    await fetchComments(task.id);
};

const openEdit = (task: Task) => {
    editTarget.value = task;
    form.value = {
        title: task.title,
        description: task.description ?? "",
        project_id: task.project_id,
        user_id: task.user_id,
        priority: task.priority,
        status: task.status,
        start_date: task.start_date ?? "",
        end_date: task.end_date ?? "",
    };
    formError.value = "";
    showModal.value = true;
};

const handleSubmit = async () => {
    formError.value = "";

    if (
        form.value.start_date &&
        form.value.end_date &&
        form.value.end_date < form.value.start_date
    ) {
        formError.value = t("tasks.endBeforeStart");
        return;
    }

    submitting.value = true;
    try {
        if (editTarget.value) {
            await updateTask(editTarget.value.id, form.value);
        } else {
            await createTask(form.value);
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
        await deleteTask(deleteTargetId.value);
        showDeleteModal.value = false;
    } catch {
        /* ignore */
    } finally {
        deleting.value = false;
    }
};

const handleAddComment = async () => {
    if (!viewTarget.value || !user.value || !newComment.value.trim()) return;
    submittingComment.value = true;
    try {
        await createComment({
            task_id: viewTarget.value.id,
            user_id: user.value.id,
            comment: newComment.value.trim(),
        });
        newComment.value = "";
    } catch (e) {
        console.error(e);
    } finally {
        submittingComment.value = false;
    }
};

const handleDeleteComment = async (id: number) => {
    if (confirm(t("tasks.deleteCommentConfirm"))) {
        await deleteComment(id);
    }
};

onMounted(async () => {
    await Promise.all([fetchTasks(), fetchProjects(), fetchUsers()]);
});
</script>

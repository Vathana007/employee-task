<template>
    <AppLayout title="Tasks" subtitle="Track and manage all tasks">
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
                    placeholder="Search tasks..."
                    class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 transition-shadow"
                />
            </div>
            <button
                @click="openCreate"
                class="inline-flex items-center justify-center gap-2 bg-sky-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm cursor-pointer whitespace-nowrap"
            >
                <Plus class="w-4 h-4" />
                New Task
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
                <p class="text-slate-500 font-medium">No tasks found</p>
                <p class="text-slate-400 text-sm mt-1" v-if="searchQuery">
                    Try adjusting your search
                </p>
                <p class="text-slate-400 text-sm mt-1" v-else>
                    Click "New Task" to get started
                </p>
            </div>
            <table v-else class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th
                            class="w-full text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider"
                        >
                            Task
                        </th>
                        <th
                            class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider hidden md:table-cell"
                        >
                            Priority
                        </th>
                        <th
                            class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider"
                        >
                            Status
                        </th>
                        <th
                            class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider hidden lg:table-cell"
                        >
                            Deadline
                        </th>
                        <th
                            class="text-right px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr
                        v-for="task in filteredTasks"
                        :key="task.id"
                        class="hover:bg-slate-50 transition"
                    >
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-800">
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
                                {{ task.priority }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
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
                                task.deadline
                                    ? new Date(
                                          task.deadline,
                                      ).toLocaleDateString()
                                    : "—"
                            }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-1 justify-end">
                                <div class="w-24 flex justify-end mr-2">
                                    <button
                                        v-if="taskAction(task.status).next"
                                        @click="onAction(task)"
                                        :disabled="updatingId === task.id"
                                        :class="[
                                            taskAction(task.status).btn,
                                            updatingId === task.id &&
                                                'opacity-50',
                                        ]"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1.5 rounded-lg transition cursor-pointer"
                                    >
                                        <component
                                            :is="taskAction(task.status).icon"
                                            class="w-3.5 h-3.5"
                                        />
                                        {{ taskAction(task.status).label }}
                                    </button>
                                </div>
                                <button
                                    @click="openView(task)"
                                    title="View"
                                    class="text-slate-400 hover:text-slate-500 hover:bg-slate-100 rounded-lg p-2 transition cursor-pointer"
                                >
                                    <Eye class="w-4 h-4" />
                                </button>
                                <button
                                    @click="openEdit(task)"
                                    title="Edit"
                                    class="text-sky-400 hover:text-sky-500 hover:bg-sky-100 rounded-lg p-2 transition cursor-pointer"
                                >
                                    <Edit2 class="w-4 h-4" />
                                </button>
                                <button
                                    @click="confirmDelete(task.id)"
                                    title="Delete"
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
        <AppModal v-model="showViewModal" title="Task Details">
            <div v-if="viewTarget" class="space-y-5">
                <div>
                    <p
                        class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                    >
                        Task Title
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
                            Status
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
                            Priority
                        </p>
                        <span
                            :class="priorityClass(viewTarget.priority)"
                            class="text-xs font-semibold px-2.5 py-1 rounded-full capitalize inline-block mt-1"
                        >
                            {{ viewTarget.priority }}
                        </span>
                    </div>
                </div>

                <div>
                    <p
                        class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                    >
                        Description
                    </p>
                    <p
                        class="text-sm text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-lg border border-slate-100 mt-1"
                    >
                        {{
                            viewTarget.description || "No description provided."
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
                            Project
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
                            Assigned To
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
                    class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100"
                >
                    <div>
                        <p
                            class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                        >
                            Deadline
                        </p>
                        <p class="text-sm text-slate-800">
                            {{
                                viewTarget.deadline
                                    ? new Date(
                                          viewTarget.deadline,
                                      ).toLocaleDateString()
                                    : "None"
                            }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                        >
                            Created At
                        </p>
                        <p class="text-sm text-slate-800">
                            {{
                                new Date(
                                    viewTarget.created_at,
                                ).toLocaleDateString()
                            }}
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <p
                        class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-3"
                    >
                        Comments
                    </p>
                    <div class="space-y-4 max-h-60 overflow-y-auto mb-4 p-1">
                        <div v-if="commentsLoading" class="text-center py-4">
                            <span class="text-xs text-slate-400"
                                >Loading comments...</span
                            >
                        </div>
                        <div
                            v-else-if="comments.length === 0"
                            class="text-center py-4 bg-slate-50 rounded-lg"
                        >
                            <span class="text-xs text-slate-400"
                                >No comments yet.</span
                            >
                        </div>
                        <div
                            v-else
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
                                    new Date(c.created_at).toLocaleDateString()
                                }}</span>
                            </div>
                            <p class="text-sm text-slate-600 pl-8">
                                {{ c.comment }}
                            </p>
                            <button
                                @click="handleDeleteComment(c.id)"
                                title="Delete comment"
                                class="absolute top-2 right-2 text-slate-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition cursor-pointer"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <form @submit.prevent="handleAddComment" class="flex gap-2">
                        <input
                            v-model="newComment"
                            type="text"
                            placeholder="Add a comment..."
                            required
                            class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                        />
                        <button
                            type="submit"
                            :disabled="submittingComment"
                            class="bg-sky-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-sky-600 transition disabled:opacity-60 cursor-pointer whitespace-nowrap"
                        >
                            {{ submittingComment ? "Posting..." : "Post" }}
                        </button>
                    </form>
                </div>

                <div class="pt-2 flex justify-end">
                    <button
                        @click="showViewModal = false"
                        class="px-5 py-2.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition cursor-pointer"
                    >
                        Close
                    </button>
                </div>
            </div>
        </AppModal>

        <!-- Create/Edit Modal -->
        <AppModal
            v-model="showModal"
            :title="editTarget ? 'Edit Task' : 'New Task'"
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
                        >Title</label
                    >
                    <input
                        v-model="form.title"
                        type="text"
                        required
                        placeholder="Task title"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                </div>

                <div>
                    <label
                        class="block text-sm font-semibold text-slate-700 mb-1"
                        >Description</label
                    >
                    <textarea
                        v-model="form.description"
                        rows="2"
                        placeholder="Optional description..."
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 resize-none"
                    ></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label
                            class="block text-sm font-semibold text-slate-700 mb-1"
                            >Project</label
                        >
                        <select
                            v-model="form.project_id"
                            required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 truncate"
                        >
                            <option value="" disabled>Select project</option>
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
                            >Assign To</label
                        >
                        <select
                            v-model="form.user_id"
                            required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 truncate"
                        >
                            <option value="" disabled>Select user</option>
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
                            >Priority</label
                        >
                        <select
                            v-model="form.priority"
                            required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                        >
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-semibold text-slate-700 mb-1"
                            >Status</label
                        >
                        <select
                            v-model="form.status"
                            required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                        >
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label
                        class="block text-sm font-semibold text-slate-700 mb-1"
                        >Deadline</label
                    >
                    <input
                        v-model="form.deadline"
                        type="date"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                </div>

                <div class="flex gap-3 pt-2">
                    <button
                        type="button"
                        @click="showModal = false"
                        class="flex-1 py-2.5 rounded-lg border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="flex-1 py-2.5 rounded-lg bg-sky-500 text-white text-sm font-semibold hover:bg-sky-600 transition disabled:opacity-60 cursor-pointer"
                    >
                        {{
                            submitting
                                ? "Saving..."
                                : editTarget
                                  ? "Update"
                                  : "Create"
                        }}
                    </button>
                </div>
            </form>
        </AppModal>

        <!-- Delete Confirmation Modal -->
        <AppModal v-model="showDeleteModal" title="Confirm Deletion">
            <div class="space-y-4">
                <div
                    class="flex items-start gap-4 p-4 bg-red-50 rounded-lg border border-red-100 text-red-800"
                >
                    <AlertTriangle
                        class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5"
                    />
                    <div>
                        <h4 class="text-sm font-semibold mb-1">
                            Are you sure?
                        </h4>
                        <p class="text-sm text-red-600">
                            This action cannot be undone. This will permanently
                            delete the task.
                        </p>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button
                        @click="showDeleteModal = false"
                        :disabled="deleting"
                        class="flex-1 py-2.5 rounded-lg border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer disabled:opacity-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="executeDelete"
                        :disabled="deleting"
                        class="flex-1 py-2.5 rounded-lg bg-red-500 text-white text-sm font-semibold hover:bg-red-600 transition disabled:opacity-60 cursor-pointer flex items-center justify-center gap-2"
                    >
                        <Trash2 class="w-4 h-4" v-if="!deleting" />
                        {{ deleting ? "Deleting..." : "Delete Task" }}
                    </button>
                </div>
            </div>
        </AppModal>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
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
import { taskAction } from "../utils/status.js";
import type { Task } from "../types/index.js";

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

const filteredTasks = computed(() => {
    if (!searchQuery.value) return tasks.value;
    const lower = searchQuery.value.toLowerCase();
    return tasks.value.filter(
        (t) =>
            t.title.toLowerCase().includes(lower) ||
            (t.description && t.description.toLowerCase().includes(lower)),
    );
});

const getProjectName = (id: number) =>
    projects.value.find((p) => p.id === id)?.name || "Unknown Project";
const getUserName = (id: number) =>
    users.value.find((u) => u.id === id)?.name || "Unknown User";

const defaultForm = () => ({
    title: "",
    description: "",
    project_id: 0,
    user_id: 0,
    priority: "medium" as Task["priority"],
    status: "pending" as Task["status"],
    deadline: "",
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
        error.value = e.response?.data?.message ?? "Failed to update status.";
        await fetchTasks(); // resync dropdowns with the real value
    } finally {
        updatingId.value = null;
    }
};

const onAction = (t: Task) => {
    const next = taskAction(t.status).next;
    if (next) changeStatus(t, next);
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
        deadline: task.deadline ?? "",
    };
    formError.value = "";
    showModal.value = true;
};

const handleSubmit = async () => {
    submitting.value = true;
    formError.value = "";
    try {
        if (editTarget.value) {
            await updateTask(editTarget.value.id, form.value);
        } else {
            await createTask(form.value);
        }
        showModal.value = false;
    } catch (e: any) {
        formError.value = e.response?.data?.message ?? "Something went wrong.";
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
    if (confirm("Delete this comment?")) {
        await deleteComment(id);
    }
};

onMounted(async () => {
    await Promise.all([fetchTasks(), fetchProjects(), fetchUsers()]);
});
</script>

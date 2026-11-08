<template>
    <AppLayout :title="t('roles.title')" :subtitle="t('roles.subtitle')">
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
                    :placeholder="t('roles.searchPlaceholder')"
                    class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 transition-shadow"
                />
            </div>
            <button
                @click="openCreate"
                class="inline-flex items-center justify-center gap-2 bg-sky-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm cursor-pointer whitespace-nowrap"
            >
                <Shield class="w-4 h-4" />
                {{ t("roles.newRole") }}
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
                    v-for="i in 3"
                    :key="i"
                    class="h-14 bg-slate-50 rounded-lg animate-pulse"
                ></div>
            </div>
            <div
                v-else-if="filteredRoles.length === 0"
                class="text-center py-16"
            >
                <Shield class="w-12 h-12 text-slate-200 mx-auto mb-3" />
                <p class="text-slate-500 font-medium">
                    {{ t("roles.noRolesFound") }}
                </p>
                <p class="text-slate-400 text-sm mt-1" v-if="searchQuery">
                    {{ t("common.tryAdjustSearch") }}
                </p>
            </div>
            <table v-else class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th
                            class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider"
                        >
                            {{ t("roles.roleName") }}
                        </th>
                        <th
                            class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider"
                        >
                            {{ t("common.description") }}
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
                        v-for="role in filteredRoles"
                        :key="role.id"
                        @click="openView(role)"
                        class="hover:bg-slate-50 transition cursor-pointer"
                    >
                        <td class="px-5 py-4">
                            <span
                                class="font-semibold text-slate-800 capitalize hover:text-sky-600 transition"
                                >{{ role.name }}</span
                            >
                        </td>
                        <td class="px-5 py-4 text-slate-500">
                            {{ role.description || t("common.noDescription") }}
                        </td>
                        <td class="px-5 py-4" @click.stop>
                            <div
                                class="flex items-center gap-1 justify-end"
                                v-if="!isSystemRole(role.name)"
                            >
                                <button
                                    @click="openView(role)"
                                    :title="t('common.view')"
                                    class="text-slate-400 hover:text-slate-500 hover:bg-slate-100 rounded-lg p-2 transition cursor-pointer"
                                >
                                    <Eye class="w-4 h-4" />
                                </button>
                                <button
                                    @click="openEdit(role)"
                                    :title="t('common.edit')"
                                    class="text-sky-400 hover:text-sky-500 hover:bg-sky-100 rounded-lg p-2 transition cursor-pointer"
                                >
                                    <Edit2 class="w-4 h-4" />
                                </button>
                                <button
                                    @click="confirmDelete(role.id)"
                                    :title="t('common.delete')"
                                    class="text-red-500 hover:text-red-600 hover:bg-red-100 rounded-lg p-2 transition cursor-pointer"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                            <div
                                v-else
                                class="flex items-center gap-1 justify-end"
                            >
                                <button
                                    @click="openView(role)"
                                    :title="t('common.view')"
                                    class="text-slate-400 hover:text-slate-500 hover:bg-slate-100 rounded-lg p-2 transition cursor-pointer"
                                >
                                    <Eye class="w-4 h-4" />
                                </button>
                                <span
                                    class="px-2 py-1 bg-slate-100 text-slate-500 text-[10px] font-semibold rounded border border-slate-200"
                                    >{{ t("roles.systemRole") }}</span
                                >
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- View Modal -->
        <AppModal v-model="showViewModal" :title="t('roles.roleDetails')">
            <div v-if="viewTarget" class="space-y-5">
                <div>
                    <p
                        class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                    >
                        {{ t("roles.roleName") }}
                    </p>
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-bold text-slate-800 capitalize">
                            {{ viewTarget.name }}
                        </p>
                        <span
                            v-if="isSystemRole(viewTarget.name)"
                            class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-semibold rounded border border-slate-200"
                            >{{ t("roles.systemRole") }}</span
                        >
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
                            t("common.noDescription")
                        }}
                    </p>
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
            :title="editTarget ? t('roles.editRole') : t('roles.newRole')"
            size="xl"
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
                        >{{ t("roles.roleName") }}</label
                    >
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        :placeholder="t('roles.namePlaceholder')"
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
                        class="block text-sm font-semibold text-slate-700 mb-2"
                        >{{ t("roles.permissions") }}</label
                    >
                    <div class="space-y-2.5">
                        <div class="flex flex-col gap-2.5 xl:flex-row xl:items-center xl:justify-between">
                            <div class="relative flex-1 max-w-xl">
                                <Search
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                                />
                                <input
                                    v-model="permissionSearchQuery"
                                    type="text"
                                    :placeholder="t('roles.filterPermissionsPlaceholder')"
                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 pl-9 pr-3 py-2 text-sm text-slate-700 shadow-sm transition focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-3 focus:ring-sky-100"
                                />
                            </div>

                            <div class="flex flex-wrap gap-1.5">
                                <button
                                    v-for="filter in permissionFilters"
                                    :key="filter.value"
                                    type="button"
                                    @click="permissionFilter = filter.value"
                                    :class="[
                                        'rounded-full border px-2.5 py-1 text-[11px] font-semibold transition cursor-pointer',
                                        permissionFilter === filter.value
                                            ? 'border-sky-500 bg-sky-50 text-sky-700 shadow-sm'
                                            : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50',
                                    ]"
                                >
                                    {{ filter.label }}
                                </button>
                            </div>
                        </div>

                        <div class="text-[11px] font-medium text-slate-500">
                            {{ filteredPermissions.length }} / {{ availablePermissions.length }}
                            {{ t("roles.permissions") }}
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-1 gap-2.5 md:grid-cols-2 xl:grid-cols-4">
                        <label
                            v-for="permission in filteredPermissions"
                            :key="permission.value"
                            class="group flex h-full min-h-24 items-start gap-2.5 rounded-lg border border-slate-200 bg-slate-50 p-3 shadow-sm transition-all duration-200 hover:border-sky-200 hover:bg-white hover:shadow-md cursor-pointer"
                        >
                            <input
                                v-model="form.permissions"
                                :value="permission.value"
                                type="checkbox"
                                class="mt-0.5 h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                            />
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold text-slate-700 leading-5">
                                    {{ permission.label }}
                                </span>
                                <span class="mt-1 block text-[11px] text-slate-500 leading-4">
                                    {{ permission.help }}
                                </span>
                            </span>
                        </label>
                    </div>

                    <div
                        v-if="filteredPermissions.length === 0"
                        class="mt-3 rounded-lg border border-dashed border-slate-200 bg-slate-50 p-3 text-sm text-slate-500"
                    >
                        {{ t("roles.noPermissionMatch") }}
                    </div>
                </div>

                <div class="flex gap-3 pt-3">
                    <button
                        type="button"
                        @click="showModal = false"
                        class="flex-1 rounded-lg border border-slate-200 bg-white py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 cursor-pointer"
                    >
                        {{ t("common.cancel") }}
                    </button>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="flex-1 rounded-lg bg-sky-500 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-600 disabled:opacity-60 cursor-pointer"
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
                        class="w-6 h-6 text-red-500 shrink-0 mt-0.5"
                    />
                    <div>
                        <h4 class="text-sm font-semibold mb-1">
                            {{ t("common.areYouSure") }}
                        </h4>
                        <p class="text-sm text-red-600">
                            {{ t("roles.deleteWarning") }}
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
                                : t("roles.deleteTitle")
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
    Shield,
    Search,
    Edit2,
    Trash2,
    AlertTriangle,
    Eye,
} from "lucide-vue-next";
import AppLayout from "../components/layout/AppLayout.vue";
import AppModal from "../components/ui/AppModal.vue";
import { useRoles } from "../composables/useRoles.js";
import type { Role } from "../composables/useRoles.js";

const { t } = useI18n();

const {
    roles,
    loading,
    error,
    fetchRoles,
    createRole,
    updateRole,
    deleteRole,
} = useRoles();

const searchQuery = ref("");
const permissionSearchQuery = ref("");
const permissionFilter = ref("all");
const showModal = ref(false);
const showViewModal = ref(false);
const showDeleteModal = ref(false);
const editTarget = ref<Role | null>(null);
const viewTarget = ref<Role | null>(null);
const deleteTargetId = ref<number | null>(null);
const submitting = ref(false);
const deleting = ref(false);
const formError = ref("");

const filteredRoles = computed(() => {
    if (!searchQuery.value) return roles.value;
    const lower = searchQuery.value.toLowerCase();
    return roles.value.filter(
        (r) =>
            r.name.toLowerCase().includes(lower) ||
            (r.description && r.description.toLowerCase().includes(lower)),
    );
});

const permissionFilters = computed(() => [
    { value: "all", label: t("roles.filterAll") },
    { value: "manage", label: t("roles.filterManage") },
    { value: "view", label: t("roles.filterView") },
    { value: "create", label: t("roles.filterCreate") },
    { value: "update", label: t("roles.filterUpdate") },
    { value: "delete", label: t("roles.filterDelete") },
]);

const filteredPermissions = computed(() => {
    const query = permissionSearchQuery.value.trim().toLowerCase();

    return availablePermissions.filter((permission) => {
        const label = permission.label.toLowerCase();
        const help = permission.help.toLowerCase();
        const value = permission.value.toLowerCase();

        const matchesSearch =
            !query ||
            label.includes(query) ||
            help.includes(query) ||
            value.includes(query);

        const category = permission.value.includes("manage")
            ? "manage"
            : permission.value.includes("view")
              ? "view"
              : permission.value.includes("delete")
                ? "delete"
                : permission.value.includes("update") || permission.value.includes("status")
                  ? "update"
                  : permission.value.includes("create")
                    ? "create"
                    : "other";

        const matchesCategory =
            permissionFilter.value === "all" || category === permissionFilter.value;

        return matchesSearch && matchesCategory;
    });
});

const availablePermissions = [
    {
        value: "manage_users",
        label: t("roles.permissionManageUsers"),
        help: t("roles.permissionManageUsersHelp"),
    },
    {
        value: "manage_roles",
        label: t("roles.permissionManageRoles"),
        help: t("roles.permissionManageRolesHelp"),
    },
    {
        value: "manage_projects",
        label: t("roles.permissionManageProjects"),
        help: t("roles.permissionManageProjectsHelp"),
    },
    {
        value: "manage_tasks",
        label: t("roles.permissionManageTasks"),
        help: t("roles.permissionManageTasksHelp"),
    },
    {
        value: "view_all_projects",
        label: t("roles.permissionViewAllProjects"),
        help: t("roles.permissionViewAllProjectsHelp"),
    },
    {
        value: "delete_any_project",
        label: t("roles.permissionDeleteAnyProject"),
        help: t("roles.permissionDeleteAnyProjectHelp"),
    },
    {
        value: "delete_any_task",
        label: t("roles.permissionDeleteAnyTask"),
        help: t("roles.permissionDeleteAnyTaskHelp"),
    },
    {
        value: "view_admin_logs",
        label: t("roles.permissionViewAdminLogs"),
        help: t("roles.permissionViewAdminLogsHelp"),
    },
    {
        value: "approve_work",
        label: t("roles.permissionApproveWork"),
        help: t("roles.permissionApproveWorkHelp"),
    },
    {
        value: "update_task_status",
        label: t("roles.permissionUpdateTaskStatus"),
        help: t("roles.permissionUpdateTaskStatusHelp"),
    },
    {
        value: "create_projects",
        label: t("roles.permissionCreateProjects"),
        help: t("roles.permissionCreateProjectsHelp"),
    },
    {
        value: "edit_own_projects",
        label: t("roles.permissionEditOwnProjects"),
        help: t("roles.permissionEditOwnProjectsHelp"),
    },
    {
        value: "delete_own_projects",
        label: t("roles.permissionDeleteOwnProjects"),
        help: t("roles.permissionDeleteOwnProjectsHelp"),
    },
    {
        value: "view_assigned_projects",
        label: t("roles.permissionViewAssignedProjects"),
        help: t("roles.permissionViewAssignedProjectsHelp"),
    },
    {
        value: "view_assigned_tasks",
        label: t("roles.permissionViewAssignedTasks"),
        help: t("roles.permissionViewAssignedTasksHelp"),
    },
    {
        value: "update_own_task_status",
        label: t("roles.permissionUpdateOwnTaskStatus"),
        help: t("roles.permissionUpdateOwnTaskStatusHelp"),
    },
];

const defaultForm = () => ({
    name: "",
    description: "",
    permissions: [] as string[],
});

const form = ref(defaultForm());

const isSystemRole = (name: string) =>
    ["admin", "editor", "user"].includes(name.toLowerCase());

const openCreate = () => {
    editTarget.value = null;
    form.value = defaultForm();
    formError.value = "";
    permissionFilter.value = "all";
    permissionSearchQuery.value = "";
    showModal.value = true;
};

const openView = (r: Role) => {
    viewTarget.value = r;
    showViewModal.value = true;
};

const openEdit = (r: Role) => {
    editTarget.value = r;
    form.value = {
        name: r.name,
        description: r.description || "",
        permissions: Array.isArray(r.permissions) ? r.permissions : [],
    };
    formError.value = "";
    permissionFilter.value = "all";
    permissionSearchQuery.value = "";
    showModal.value = true;
};

const handleSubmit = async () => {
    submitting.value = true;
    formError.value = "";
    try {
        if (editTarget.value) {
            await updateRole(editTarget.value.id, form.value);
        } else {
            await createRole(form.value);
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
        await deleteRole(deleteTargetId.value);
        showDeleteModal.value = false;
    } catch (e: any) {
        alert(e.response?.data?.message || t("common.failedToDelete"));
    } finally {
        deleting.value = false;
    }
};

onMounted(fetchRoles);
</script>

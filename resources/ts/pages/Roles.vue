<template>
    <AppLayout title="Roles" subtitle="Manage system roles and permissions">
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
                    placeholder="Search roles..."
                    class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 transition-shadow"
                />
            </div>
            <button
                @click="openCreate"
                class="inline-flex items-center justify-center gap-2 bg-sky-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm cursor-pointer whitespace-nowrap"
            >
                <Shield class="w-4 h-4" />
                New Role
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
                <p class="text-slate-500 font-medium">No roles found</p>
                <p class="text-slate-400 text-sm mt-1" v-if="searchQuery">
                    Try adjusting your search
                </p>
            </div>
            <table v-else class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th
                            class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider"
                        >
                            Role Name
                        </th>
                        <th
                            class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider"
                        >
                            Description
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
                        v-for="role in filteredRoles"
                        :key="role.id"
                        class="hover:bg-slate-50 transition"
                    >
                        <td class="px-5 py-4">
                            <span
                                class="font-semibold text-slate-800 capitalize"
                                >{{ role.name }}</span
                            >
                        </td>
                        <td class="px-5 py-4 text-slate-500">
                            {{ role.description || "—" }}
                        </td>
                        <td class="px-5 py-4">
                            <div
                                class="flex items-center gap-1 justify-end"
                                v-if="!isSystemRole(role.name)"
                            >
                                <button
                                    @click="openEdit(role)"
                                    title="Edit"
                                    class="text-sky-400 hover:text-sky-500 hover:bg-sky-100 rounded-lg p-2 transition cursor-pointer"
                                >
                                    <Edit2 class="w-4 h-4" />
                                </button>
                                <button
                                    @click="confirmDelete(role.id)"
                                    title="Delete"
                                    class="text-red-500 hover:text-red-600 hover:bg-red-100 rounded-lg p-2 transition cursor-pointer"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                            <div v-else class="flex justify-end">
                                <span
                                    class="px-2 py-1 bg-slate-100 text-slate-500 text-[10px] font-semibold rounded border border-slate-200"
                                    >System Role</span
                                >
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Create/Edit Modal -->
        <AppModal
            v-model="showModal"
            :title="editTarget ? 'Edit Role' : 'New Role'"
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
                        >Role Name</label
                    >
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        placeholder="e.g. manager"
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
                        rows="3"
                        placeholder="Optional description..."
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 resize-none"
                    ></textarea>
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
                            delete the role.
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
                        {{ deleting ? "Deleting..." : "Delete Role" }}
                    </button>
                </div>
            </div>
        </AppModal>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { Shield, Search, Edit2, Trash2, AlertTriangle } from "lucide-vue-next";
import AppLayout from "../components/layout/AppLayout.vue";
import AppModal from "../components/ui/AppModal.vue";
import { useRoles } from "../composables/useRoles.js";
import type { Role } from "../composables/useRoles.js";

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
const showModal = ref(false);
const showDeleteModal = ref(false);
const editTarget = ref<Role | null>(null);
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

const defaultForm = () => ({ name: "", description: "" });
const form = ref(defaultForm());

const isSystemRole = (name: string) =>
    ["admin", "editor", "user"].includes(name.toLowerCase());

const openCreate = () => {
    editTarget.value = null;
    form.value = defaultForm();
    formError.value = "";
    showModal.value = true;
};

const openEdit = (r: Role) => {
    editTarget.value = r;
    form.value = { name: r.name, description: r.description || "" };
    formError.value = "";
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
        await deleteRole(deleteTargetId.value);
        showDeleteModal.value = false;
    } catch (e: any) {
        alert(e.response?.data?.message || "Failed to delete");
    } finally {
        deleting.value = false;
    }
};

onMounted(fetchRoles);
</script>

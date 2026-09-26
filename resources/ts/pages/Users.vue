<template>
    <AppLayout title="Users" subtitle="Manage team members">
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
                    placeholder="Search users..."
                    class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 transition-shadow"
                />
            </div>
            <button
                @click="openCreate"
                class="inline-flex items-center justify-center gap-2 bg-sky-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-sky-600 transition shadow-sm cursor-pointer whitespace-nowrap"
            >
                <UserPlus class="w-4 h-4" />
                New User
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
                v-else-if="filteredUsers.length === 0"
                class="text-center py-16"
            >
                <Users class="w-12 h-12 text-slate-200 mx-auto mb-3" />
                <p class="text-slate-500 font-medium">No users found</p>
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
                            User
                        </th>
                        <th
                            class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider hidden md:table-cell"
                        >
                            Email
                        </th>
                        <th
                            class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider hidden lg:table-cell"
                        >
                            Joined
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
                        v-for="u in filteredUsers"
                        :key="u.id"
                        class="hover:bg-slate-50 transition"
                    >
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    v-if="!u.avatar_url"
                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                >
                                    {{ u.name.charAt(0).toUpperCase() }}
                                </div>
                                <img
                                    v-else
                                    :src="u.avatar_url"
                                    alt="Avatar"
                                    class="w-8 h-8 rounded-full object-cover flex-shrink-0"
                                />
                                <span class="font-semibold text-slate-800">{{
                                    u.name
                                }}</span>
                            </div>
                        </td>
                        <td
                            class="px-5 py-4 text-slate-500 hidden md:table-cell"
                        >
                            {{ u.email }}
                        </td>
                        <td
                            class="px-5 py-4 text-slate-400 hidden lg:table-cell"
                        >
                            {{ new Date(u.created_at).toLocaleDateString() }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-1 justify-end">
                                <button
                                    @click="openView(u)"
                                    title="View"
                                    class="text-slate-400 hover:text-slate-500 hover:bg-slate-100 rounded-lg p-2 transition cursor-pointer"
                                >
                                    <Eye class="w-4 h-4" />
                                </button>
                                <button
                                    @click="openEdit(u)"
                                    title="Edit"
                                    class="text-sky-400 hover:text-sky-500 hover:bg-sky-100 rounded-lg p-2 transition cursor-pointer"
                                >
                                    <Edit2 class="w-4 h-4" />
                                </button>
                                <button
                                    @click="confirmDelete(u.id)"
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
        <AppModal v-model="showViewModal" title="User Details">
            <div v-if="viewTarget" class="space-y-5">
                <div class="flex items-center gap-4">
                    <div
                        v-if="!viewTarget.avatar_url"
                        class="w-14 h-14 rounded-full bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center text-white text-xl font-bold flex-shrink-0 shadow-sm"
                    >
                        {{ viewTarget.name.charAt(0).toUpperCase() }}
                    </div>
                    <img
                        v-else
                        :src="viewTarget.avatar_url"
                        alt="Avatar"
                        class="w-14 h-14 rounded-full object-cover flex-shrink-0 shadow-sm"
                    />
                    <div>
                        <p class="text-sm font-bold text-slate-800">
                            {{ viewTarget.name }}
                        </p>
                        <p class="text-sm text-slate-500">
                            {{ viewTarget.email }}
                        </p>
                        <span
                            class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 capitalize border border-slate-200"
                            >{{ viewTarget.role || "User" }}</span
                        >
                    </div>
                </div>

                <div
                    class="grid grid-cols-2 gap-4 pt-5 border-t border-slate-100"
                >
                    <div>
                        <p
                            class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                        >
                            Joined Date
                        </p>
                        <p class="text-sm text-slate-800">
                            {{
                                new Date(
                                    viewTarget.created_at,
                                ).toLocaleDateString()
                            }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1"
                        >
                            Last Updated
                        </p>
                        <p class="text-sm text-slate-800">
                            {{
                                new Date(
                                    viewTarget.updated_at,
                                ).toLocaleDateString()
                            }}
                        </p>
                    </div>
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
            :title="editTarget ? 'Edit User' : 'New User'"
        >
            <form
                @submit.prevent="handleSubmit"
                class="space-y-4"
                autocomplete="off"
            >
                <div
                    v-if="formError"
                    class="bg-red-50 text-red-700 text-sm p-3 rounded-lg border-l-4 border-red-500"
                >
                    {{ formError }}
                </div>

                <div>
                    <label
                        class="block text-sm font-semibold text-slate-700 mb-1"
                        >Full Name</label
                    >
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        placeholder="John Doe"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                </div>

                <div>
                    <label
                        class="block text-sm font-semibold text-slate-700 mb-1"
                        >Email</label
                    >
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        placeholder="john@example.com"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                </div>

                <div>
                    <label
                        class="block text-sm font-semibold text-slate-700 mb-1"
                        >Role</label
                    >
                    <select
                        v-model="form.role"
                        required
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 capitalize"
                    >
                        <option
                            v-for="role in roles"
                            :key="role.id"
                            :value="role.name"
                        >
                            {{ role.name }}
                        </option>
                    </select>
                </div>

                <div v-if="!editTarget">
                    <label
                        class="block text-sm font-semibold text-slate-700 mb-1"
                        >Password</label
                    >
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        placeholder="Min. 8 characters"
                        autocomplete="new-password"
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
                            delete the user account.
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
                        {{ deleting ? "Deleting..." : "Delete User" }}
                    </button>
                </div>
            </div>
        </AppModal>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import {
    UserPlus,
    Users,
    Search,
    Eye,
    Edit2,
    Trash2,
    AlertTriangle,
} from "lucide-vue-next";
import AppLayout from "../components/layout/AppLayout.vue";
import AppModal from "../components/ui/AppModal.vue";
import { useUsers } from "../composables/useUsers.js";
import { useRoles } from "../composables/useRoles.js";
import type { User } from "../types/index.js";

const {
    users,
    loading,
    error,
    fetchUsers,
    createUser,
    updateUser,
    deleteUser,
} = useUsers();
const { roles, fetchRoles } = useRoles();

const searchQuery = ref("");
const showModal = ref(false);
const showViewModal = ref(false);
const showDeleteModal = ref(false);
const editTarget = ref<User | null>(null);
const viewTarget = ref<User | null>(null);
const deleteTargetId = ref<number | null>(null);
const submitting = ref(false);
const deleting = ref(false);
const formError = ref("");

const filteredUsers = computed(() => {
    if (!searchQuery.value) return users.value;
    const lower = searchQuery.value.toLowerCase();
    return users.value.filter(
        (u) =>
            u.name.toLowerCase().includes(lower) ||
            u.email.toLowerCase().includes(lower),
    );
});

const defaultForm = () => ({ name: "", email: "", password: "", role: "user" });
const form = ref(defaultForm());

const openCreate = () => {
    editTarget.value = null;
    form.value = defaultForm();
    formError.value = "";
    showModal.value = true;
};

const openView = (u: User) => {
    viewTarget.value = u;
    showViewModal.value = true;
};

const openEdit = (u: User) => {
    editTarget.value = u;
    form.value = {
        name: u.name,
        email: u.email,
        password: "",
        role: u.role || "user",
    };
    formError.value = "";
    showModal.value = true;
};

const handleSubmit = async () => {
    submitting.value = true;
    formError.value = "";
    try {
        if (editTarget.value) {
            const payload: any = {
                name: form.value.name,
                email: form.value.email,
                role: form.value.role,
            };
            if (form.value.password) payload.password = form.value.password;
            await updateUser(editTarget.value.id, payload);
        } else {
            await createUser(form.value);
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
        await deleteUser(deleteTargetId.value);
        showDeleteModal.value = false;
    } catch {
        /* ignore */
    } finally {
        deleting.value = false;
    }
};

onMounted(() => {
    fetchUsers();
    fetchRoles();
});
</script>

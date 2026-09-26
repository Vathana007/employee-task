import { ref } from "vue";
import api from "../services/api";
import type { User } from "../types";

export function useUsers() {
    const users = ref<User[]>([]);
    const loading = ref(false);
    const error = ref("");

    const fetchUsers = async () => {
        loading.value = true;
        error.value = "";
        try {
            const res = await api.get("/users");
            users.value = res.data.data ?? [];
        } catch (e: any) {
            error.value = e.response?.data?.message ?? "Failed to load users.";
        } finally {
            loading.value = false;
        }
    };

    const createUser = async (payload: {
        name: string;
        email: string;
        password: string;
    }) => {
        const res = await api.post("/users", payload);
        users.value.unshift(res.data.data);
        return res.data.data as User;
    };

    const updateUser = async (
        id: number,
        payload: Partial<{ name: string; email: string }>,
    ) => {
        const res = await api.put(`/users/${id}`, payload);
        const idx = users.value.findIndex((u) => u.id === id);
        if (idx !== -1) users.value[idx] = res.data.data;
        return res.data.data as User;
    };

    const deleteUser = async (id: number) => {
        await api.delete(`/users/${id}`);
        users.value = users.value.filter((u) => u.id !== id);
    };

    return {
        users,
        loading,
        error,
        fetchUsers,
        createUser,
        updateUser,
        deleteUser,
    };
}

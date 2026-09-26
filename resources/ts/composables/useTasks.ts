import { ref } from "vue";
import api from "../services/api";
import type { Task } from "../types";

export interface TaskPayload {
    project_id: number;
    user_id: number;
    title: string;
    description?: string;
    priority: Task["priority"];
    status: Task["status"];
    deadline?: string;
}

export function useTasks() {
    const tasks = ref<Task[]>([]);
    const loading = ref(false);
    const error = ref("");

    const fetchTasks = async () => {
        loading.value = true;
        error.value = "";
        try {
            const res = await api.get("/tasks");
            tasks.value = res.data.data ?? [];
        } catch (e: any) {
            error.value = e.response?.data?.message ?? "Failed to load tasks.";
        } finally {
            loading.value = false;
        }
    };

    const createTask = async (payload: TaskPayload) => {
        const res = await api.post("/tasks", payload);
        tasks.value.unshift(res.data.data);
        return res.data.data as Task;
    };

    const updateTask = async (id: number, payload: Partial<TaskPayload>) => {
        const res = await api.put(`/tasks/${id}`, payload);
        const idx = tasks.value.findIndex((t) => t.id === id);
        if (idx !== -1) tasks.value[idx] = res.data.data;
        return res.data.data as Task;
    };

    const deleteTask = async (id: number) => {
        await api.delete(`/tasks/${id}`);
        tasks.value = tasks.value.filter((t) => t.id !== id);
    };

    return {
        tasks,
        loading,
        error,
        fetchTasks,
        createTask,
        updateTask,
        deleteTask,
    };
}

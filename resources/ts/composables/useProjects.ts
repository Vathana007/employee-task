import { ref } from "vue";
import api from "../services/api";
import type { Project } from "../types";

export function useProjects() {
    const projects = ref<Project[]>([]);
    const loading = ref(false);
    const error = ref("");

    const fetchProjects = async () => {
        loading.value = true;
        error.value = "";
        try {
            const res = await api.get("/projects");
            projects.value = res.data.data ?? [];
        } catch (e: any) {
            error.value =
                e.response?.data?.message ?? "Failed to load projects.";
        } finally {
            loading.value = false;
        }
    };

    const createProject = async (
        payload: Pick<Project, "name" | "description" | "status">,
    ) => {
        const res = await api.post("/projects", payload);
        projects.value.unshift(res.data.data);
        return res.data.data as Project;
    };

    const updateProject = async (
        id: number,
        payload: Partial<Pick<Project, "name" | "description" | "status">>,
    ) => {
        const res = await api.put(`/projects/${id}`, payload);
        const idx = projects.value.findIndex((p) => p.id === id);
        if (idx !== -1) projects.value[idx] = res.data.data;
        return res.data.data as Project;
    };

    const deleteProject = async (id: number) => {
        await api.delete(`/projects/${id}`);
        projects.value = projects.value.filter((p) => p.id !== id);
    };

    return {
        projects,
        loading,
        error,
        fetchProjects,
        createProject,
        updateProject,
        deleteProject,
    };
}

import { ref } from "vue";
import api from "../services/api";
import type { User, Project, Task } from "../types";

export interface DashboardStats {
    users: number;
    projects: number;
    tasks: number;
    comments: number;
}

export function useDashboard() {
    const user = ref<User | null>(null);
    const stats = ref<DashboardStats>({
        users: 0,
        projects: 0,
        tasks: 0,
        comments: 0,
    });
    const recentTasks = ref<Task[]>([]);
    const loading = ref(false);
    const error = ref("");

    const fetchDashboard = async () => {
        loading.value = true;
        error.value = "";

        try {
            const [profileRes, usersRes, projectsRes, tasksRes, commentsRes] =
                await Promise.all([
                    api.get("/profile"),
                    api.get("/users"),
                    api.get("/projects"),
                    api.get("/tasks"),
                    api.get("/comments"),
                ]);

            user.value = profileRes.data.user;

            const users: User[] = usersRes.data.data ?? [];
            const projects: Project[] = projectsRes.data.data ?? [];
            const tasks: Task[] = tasksRes.data.data ?? [];
            const comments: any[] = commentsRes.data.data ?? [];

            stats.value = {
                users: users.length,
                projects: projects.length,
                tasks: tasks.length,
                comments: comments.length,
            };

            recentTasks.value = [...tasks]
                .sort(
                    (a, b) =>
                        new Date(b.created_at).getTime() -
                        new Date(a.created_at).getTime(),
                )
                .slice(0, 5);
        } catch (e: any) {
            error.value =
                e.response?.data?.message ?? "Failed to load dashboard data.";
        } finally {
            loading.value = false;
        }
    };

    return { user, stats, recentTasks, loading, error, fetchDashboard };
}

export interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    avatar_url: string;
    created_at: string;
    updated_at: string;
}

export interface Project {
    id: number;
    user_id: number;
    name: string;
    description: string | null;
    status: "pending" | "in_progress" | "completed";
    tasks_count?: number;
    completed_tasks_count?: number;
    created_at: string;
    updated_at: string;
}

export interface Task {
    id: number;
    project_id: number;
    user_id: number;
    title: string;
    description: string | null;
    priority: "low" | "medium" | "high";
    status: "pending" | "in_progress" | "completed";
    deadline: string | null;
    created_at: string;
    updated_at: string;
}

export interface Comment {
    id: number;
    task_id: number;
    user_id: number;
    comment: string;
    created_at: string;
    updated_at: string;
}

export type ProjectStatus = Project["status"];
export type TaskStatus = Task["status"];
export type TaskPriority = Task["priority"];

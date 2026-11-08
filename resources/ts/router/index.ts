import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/",
        name: "Dashboard",
        component: () => import("../pages/Home.vue"),
        meta: { requiresAuth: true },
    },
    {
        path: "/projects",
        name: "Projects",
        component: () => import("../pages/Projects.vue"),
        meta: { requiresAuth: true },
    },
    {
        path: "/tasks",
        name: "Tasks",
        component: () => import("../pages/Tasks.vue"),
        meta: { requiresAuth: true },
    },
    {
        path: "/users",
        name: "Users",
        component: () => import("../pages/Users.vue"),
        meta: { requiresAuth: true },
    },
    {
        path: "/roles",
        name: "Roles",
        component: () => import("../pages/Roles.vue"),
        meta: { requiresAuth: true },
    },
    {
        path: "/settings",
        name: "Settings",
        component: () => import("../pages/Settings.vue"),
        meta: { requiresAuth: true },
    },
    {
        path: "/audit-log",
        name: "AuditLog",
        component: () => import("../pages/AuditLog.vue"),
        meta: { requiresAuth: true },
    },
    {
        path: "/login",
        name: "Login",
        component: () => import("../pages/Login.vue"),
        meta: { guest: true },
    },
    {
        path: "/register",
        name: "Register",
        component: () => import("../pages/Register.vue"),
        meta: { guest: true },
    },
    {
        path: "/forgot-password",
        name: "ForgotPassword",
        component: () => import("../pages/ForgotPassword.vue"),
        meta: { guest: true },
    },
    {
        path: "/reset-password/:token",
        name: "ResetPassword",
        component: () => import("../pages/ResetPassword.vue"),
        meta: { guest: true },
    },
    {
        path: "/verify-email",
        name: "VerifyEmail",
        component: () => import("../pages/VerifyEmail.vue"),
        meta: { guest: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    const token = localStorage.getItem("access_token");

    if (to.meta.requiresAuth && !token) {
        return { name: "Login" };
    }

    if (to.meta.guest && token) {
        return { name: "Dashboard" };
    }

    return true;
});

export default router;

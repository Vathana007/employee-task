<template>
  <nav class="px-4 py-5 space-y-1 flex-1 overflow-y-auto">
    <p class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Main Menu</p>

    <router-link v-for="item in mainNav" :key="item.to" :to="item.to"
      class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors" :class="$route.path === item.to
        ? 'bg-sky-500 text-white shadow-sm'
        : 'text-slate-500 hover:bg-sky-50 hover:text-sky-600'">
      <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
      <span>{{ item.label }}</span>
    </router-link>

    <p class="px-3 pt-5 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">System</p>

    <router-link v-for="item in systemNav" :key="item.to" :to="item.to"
      class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors" :class="$route.path === item.to
        ? 'bg-sky-500 text-white shadow-sm'
        : 'text-slate-500 hover:bg-sky-50 hover:text-sky-600'">
      <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
      <span>{{ item.label }}</span>
    </router-link>
  </nav>
</template>

<script setup lang="ts">
import { LayoutDashboard, FolderOpen, CheckCircle2, Users, Settings, Shield } from 'lucide-vue-next';

const mainNav = [
  { to: '/', label: 'Dashboard', icon: LayoutDashboard },
  { to: '/projects', label: 'Projects', icon: FolderOpen },
  { to: '/tasks', label: 'Tasks', icon: CheckCircle2 },
  { to: '/users', label: 'Users', icon: Users },
];

const systemNav = [
  { to: '/roles', label: 'Roles', icon: Shield },
  { to: '/settings', label: 'Settings', icon: Settings },
];
</script>

<template>
  <AppLayout title="Dashboard" subtitle="Overview of your workspace">
    <div v-if="error" class="mb-5 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg text-sm text-red-700">{{ error }}
    </div>

    <section
      class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-sky-500 to-sky-600 p-6 sm:p-8 text-white shadow-lg mb-6">
      <div class="absolute -right-10 -top-10 w-44 h-44 rounded-full bg-white/10"></div>
      <div class="absolute right-20 -bottom-16 w-56 h-56 rounded-full bg-white/5"></div>
      <div class="relative z-10">
        <p class="text-sky-200 text-sm font-medium mb-1">Good day 👋</p>
        <h1 class="text-2xl sm:text-3xl font-bold mb-2">Welcome back, {{ user?.name || '...' }}!</h1>
        <p class="text-sky-100 text-sm leading-relaxed max-w-lg">Manage your tasks, projects, and team from one simple
          dashboard.</p>
      </div>
    </section>

    <section class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
      <div v-for="card in statCards" :key="card.label"
        class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition">
        <div :class="`w-10 h-10 rounded-lg ${card.bg} ${card.color} flex items-center justify-center mb-3`">
          <component :is="card.icon" class="w-5 h-5" />
        </div>
        <p class="text-xs text-slate-400">{{ card.label }}</p>
        <p v-if="loading" class="text-2xl font-bold text-slate-200 mt-1 animate-pulse">—</p>
        <p v-else class="text-2xl font-bold text-slate-800 mt-1">{{ card.value }}</p>
      </div>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-3 gap-5">
      <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
          <div>
            <h2 class="text-sm font-bold text-slate-800">Recent Tasks</h2>
            <p class="text-xs text-slate-400 mt-0.5">Latest added tasks</p>
          </div>
          <router-link to="/tasks" class="text-xs font-semibold text-sky-500 hover:text-sky-600">View all
            →</router-link>
        </div>
        <div class="divide-y divide-slate-100">
          <div v-if="loading" class="p-5 text-center text-slate-400 text-sm animate-pulse">Loading...</div>
          <div v-else-if="recentTasks.length === 0" class="p-5 text-center text-slate-400 text-sm">No tasks yet.</div>
          <div v-for="task in recentTasks" :key="task.id"
            class="p-4 flex items-center gap-3 hover:bg-slate-50 transition">
            <div class="w-8 h-8 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center flex-shrink-0">
              <CheckCircle2 class="w-4 h-4" />
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-slate-800 truncate">{{ task.title }}</p>
              <p class="text-xs text-slate-400 mt-0.5 truncate">{{ task.description || 'No description' }}</p>
            </div>
            <span :class="statusClass(task.status)"
              class="text-xs font-medium px-2 py-0.5 rounded-full capitalize whitespace-nowrap">
              {{ task.status.replace('_', ' ') }}
            </span>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <h2 class="text-sm font-bold text-slate-800 mb-4">Account Overview</h2>
        <div class="flex items-center gap-3 mb-5">
          <div
            class="w-12 h-12 rounded-full bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center text-white text-lg font-bold shadow-sm">
            {{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}
          </div>
          <div>
            <p class="font-semibold text-slate-800 text-sm">{{ user?.name || '...' }}</p>
            <p class="text-xs text-slate-400">{{ user?.email || '...' }}</p>
          </div>
        </div>
        <div class="space-y-2">
          <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
            <span class="text-xs text-slate-500">Status</span>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full">Active</span>
          </div>
          <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
            <span class="text-xs text-slate-500">Projects</span>
            <span class="text-sm font-bold text-slate-800">{{ stats.projects }}</span>
          </div>
          <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
            <span class="text-xs text-slate-500">Tasks</span>
            <span class="text-sm font-bold text-slate-800">{{ stats.tasks }}</span>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { CheckCircle2, FolderOpen, LayoutDashboard, Users } from 'lucide-vue-next';
import AppLayout from '../components/layout/AppLayout.vue';
import { useDashboard } from '../composables/useDashboard.js';

const { stats, recentTasks, loading, error, fetchDashboard, user } = useDashboard();

const statCards = computed(() => [
  { label: 'Total Users', value: stats.value.users, icon: Users, bg: 'bg-sky-50', color: 'text-sky-500' },
  { label: 'Total Projects', value: stats.value.projects, icon: FolderOpen, bg: 'bg-emerald-50', color: 'text-emerald-500' },
  { label: 'Total Tasks', value: stats.value.tasks, icon: CheckCircle2, bg: 'bg-purple-50', color: 'text-purple-500' },
  { label: 'Comments', value: stats.value.comments, icon: LayoutDashboard, bg: 'bg-orange-50', color: 'text-orange-500' },
]);

const statusClass = (status: string) => ({
  'bg-emerald-50 text-emerald-600': status === 'completed',
  'bg-sky-50 text-sky-600': status === 'in_progress',
  'bg-slate-100 text-slate-500': status === 'pending',
});

onMounted(fetchDashboard);
</script>
<template>
  <nav class="px-4 py-5 space-y-1 flex-1 overflow-y-auto">
    <p class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">{{ t('nav.mainMenu') }}</p>

    <router-link v-for="item in mainNav" :key="item.to" :to="item.to"
      class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors" :class="$route.path === item.to
        ? 'bg-sky-500 text-white shadow-sm'
        : 'text-slate-500 hover:bg-sky-50 hover:text-sky-600'">
      <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
      <span>{{ t(item.labelKey) }}</span>
    </router-link>

    <p class="px-3 pt-5 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">{{ t('nav.system') }}</p>

    <router-link v-for="item in systemNav" :key="item.to" :to="item.to"
      class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors" :class="$route.path === item.to
        ? 'bg-sky-500 text-white shadow-sm'
        : 'text-slate-500 hover:bg-sky-50 hover:text-sky-600'">
      <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
      <span>{{ t(item.labelKey) }}</span>
    </router-link>

    <template v-if="isAdmin">
      <p class="px-3 pt-5 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">{{ t('nav.admin') }}</p>
      <router-link to="/error-log" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors"
        :class="$route.path === '/error-log'
          ? 'bg-sky-500 text-white shadow-sm'
          : 'text-slate-500 hover:bg-sky-50 hover:text-sky-600'">
        <AlertCircle class="w-5 h-5 flex-shrink-0" />
        <span>{{ t('nav.errorLogs') }}</span>
      </router-link>
    </template>
  </nav>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { LayoutDashboard, FolderOpen, CheckCircle2, Users, Settings, Shield, AlertCircle } from 'lucide-vue-next';
import { useLayout } from '../../composables/useLayout';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const { user } = useLayout();

const mainNav = [
  { to: '/', labelKey: 'nav.dashboard', icon: LayoutDashboard },
  { to: '/projects', labelKey: 'nav.projects', icon: FolderOpen },
  { to: '/tasks', labelKey: 'nav.tasks', icon: CheckCircle2 },
  { to: '/users', labelKey: 'nav.users', icon: Users },
];

const systemNav = [
  { to: '/roles', labelKey: 'nav.roles', icon: Shield },
  { to: '/settings', labelKey: 'nav.settings', icon: Settings },
];

const isAdmin = computed(() => user.value?.role === 'Super Admin');
</script>

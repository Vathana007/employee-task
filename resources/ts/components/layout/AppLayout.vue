<template>
  <div class="min-h-screen bg-slate-50 flex">

    <div v-if="sidebarOpen" @click="sidebarOpen = false"
      class="fixed inset-0 z-40 bg-black/30 lg:hidden"></div>

    <aside :class="[
      'fixed top-0 left-0 z-50 h-screen w-64 bg-white border-r border-slate-200 flex flex-col transition-transform duration-300',
      sidebarOpen ? 'translate-x-0' : '-translate-x-full'
    ]">
      <div class="h-16 flex items-center px-5 border-b border-slate-200 flex-shrink-0">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-sky-500 flex items-center justify-center shadow shadow-sky-500/30">
            <ShieldCheck class="w-4 h-4 text-white" />
          </div>
          <div>
            <p class="font-bold text-sm text-slate-800 leading-none">Task Manager</p>
            <p class="text-xs text-slate-400 mt-0.5">Management Panel</p>
          </div>
        </div>
      </div>

      <SidebarNav class="flex-1" />

      <div class="p-4 border-t border-slate-200 flex-shrink-0">
        <button @click="confirmLogout"
          class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-red-500 hover:bg-red-50 transition text-sm cursor-pointer">
          <LogOut class="w-5 h-5" />
          <span class="font-medium">Logout</span>
        </button>
      </div>
    </aside>

    <div :class="['flex-1 flex flex-col transition-all duration-300 min-w-0', sidebarOpen ? 'lg:ml-64' : 'ml-0']">
      <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-30 flex-shrink-0">
        <div class="h-full px-4 sm:px-6 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen"
              class="p-2 rounded-lg hover:bg-slate-100 transition cursor-pointer">
              <Menu class="w-5 h-5 text-slate-600" />
            </button>
            <div>
              <h2 class="text-base font-bold text-slate-800 leading-none">{{ title }}</h2>
              <p class="text-xs text-slate-400 mt-0.5">{{ subtitle }}</p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <button class="relative p-2 rounded-lg hover:bg-slate-100 transition cursor-pointer">
              <Bell class="w-5 h-5 text-slate-600" />
              <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
            </button>

            <div class="h-6 w-px bg-slate-200 mx-1"></div>

            <div class="flex items-center gap-2.5">
              <div v-if="!user?.avatar_url" class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center text-white font-bold text-sm shadow-sm flex-shrink-0">
                {{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}
              </div>
              <img v-else :src="user.avatar_url" alt="Profile" class="w-8 h-8 rounded-full object-cover shadow-sm border border-slate-200 flex-shrink-0" />
              <div class="hidden sm:block">
                <p class="text-sm font-semibold text-slate-800 leading-none">{{ user?.name || '...' }}</p>
                <p class="text-xs text-slate-400 mt-0.5 capitalize">{{ user?.role || 'User' }}</p>
              </div>
            </div>
          </div>
        </div>
      </header>

      <main class="flex-1 p-5 sm:p-7 overflow-auto">
        <slot />
      </main>
    </div>
  </div>

  <AppModal v-model="showLogoutModal" title="Confirm Logout">
    <div class="mb-6">
      <p class="text-sm text-slate-600 leading-relaxed">
        Are you sure you want to log out of your account? You will need your credentials to access the dashboard again.
      </p>
    </div>
    <div class="flex gap-3">
      <button @click="showLogoutModal = false"
        class="flex-1 py-2.5 rounded-lg border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
        Cancel
      </button>
      <button @click="executeLogout"
        class="flex-1 py-2.5 rounded-lg bg-red-500 text-white text-sm font-semibold hover:bg-red-600 transition cursor-pointer shadow-sm">
        Yes, Log Out
      </button>
    </div>
  </AppModal>
</template>

<script setup lang="ts">
import { ShieldCheck, LogOut, Menu, Bell } from 'lucide-vue-next';
import SidebarNav from './SidebarNav.vue';
import AppModal from '../ui/AppModal.vue';
import { useLayout } from '../../composables/useLayout';

defineProps<{
  title: string;
  subtitle?: string;
}>();

const { sidebarOpen, user, showLogoutModal, confirmLogout, executeLogout } = useLayout();
</script>

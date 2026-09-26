<template>
  <AppLayout title="Settings" subtitle="Manage your account preferences">
    <div class="max-w-2xl space-y-5">

      <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-sm font-bold text-slate-800 mb-4">Profile Information</h2>
        <form @submit.prevent="saveProfile" class="space-y-4">
          <div v-if="profileMsg"
            :class="profileMsg.type === 'success' ? 'text-emerald-700 bg-emerald-50 border-emerald-300' : 'text-red-700 bg-red-50 border-red-300'"
            class="text-sm p-3 rounded-lg border-l-4">{{ profileMsg.text }}</div>

          <div class="flex items-center gap-4 mb-5">
            <div class="relative group">
              <div v-if="!avatarPreview && !user?.avatar_url"
                class="w-16 h-16 rounded-full bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center text-white text-2xl font-bold shadow-md">
                {{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}
              </div>
              <img v-else :src="avatarPreview || user?.avatar_url" alt="Profile avatar"
                class="w-16 h-16 rounded-full object-cover shadow-md" />

              <label
                class="absolute inset-0 rounded-full bg-black/0 group-hover:bg-black/40 flex items-center justify-center cursor-pointer transition"
                title="Change photo">
                <Camera class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 transition" />
                <input type="file" accept="image/png,image/jpeg,image/webp" class="hidden" @change="onAvatarSelected" />
              </label>
            </div>

            <div>
              <p class="font-semibold text-slate-800">{{ user?.name }}</p>
              <p class="text-sm text-slate-400">{{ user?.email }}</p>

              <div class="flex items-center gap-2 mt-2" v-if="avatarFile">
                <button type="button" @click="uploadAvatar" :disabled="uploadingAvatar"
                  class="text-xs font-semibold px-3 py-1.5 rounded-md bg-sky-500 text-white hover:bg-sky-600 disabled:opacity-60 cursor-pointer">
                  {{ uploadingAvatar ? 'Uploading...' : 'Upload photo' }}
                </button>
                <button type="button" @click="cancelAvatar"
                  class="text-xs font-semibold px-3 py-1.5 rounded-md text-slate-500 hover:bg-slate-100 cursor-pointer">
                  Cancel
                </button>
              </div>
              <p v-if="avatarMsg" :class="avatarMsg.type === 'success' ? 'text-emerald-600' : 'text-red-600'"
                class="text-xs mt-1">
                {{ avatarMsg.text }}
              </p>
            </div>
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Full Name</label>
            <input v-model="profileForm.name" type="text" required
              class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Email Address</label>
            <input v-model="profileForm.email" type="email" required
              class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Role</label>
            <div class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm bg-slate-50 text-slate-600">
              {{ getRoleLabel(profileForm.role) }}
            </div>
            <p class="text-xs text-slate-400 mt-1">Role is assigned by administrator only</p>
          </div>

          <button type="submit" :disabled="savingProfile"
            class="px-5 py-2.5 rounded-lg bg-sky-500 text-white text-sm font-semibold hover:bg-sky-600 transition disabled:opacity-60 cursor-pointer">
            {{ savingProfile ? 'Saving...' : 'Save Changes' }}
          </button>
        </form>
      </div>

      <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-sm font-bold text-slate-800 mb-4">Change Password</h2>
        <form @submit.prevent="changePassword" class="space-y-4">
          <div v-if="passwordMsg"
            :class="passwordMsg.type === 'success' ? 'text-emerald-700 bg-emerald-50 border-emerald-300' : 'text-red-700 bg-red-50 border-red-300'"
            class="text-sm p-3 rounded-lg border-l-4">{{ passwordMsg.text }}</div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Current Password</label>
            <input v-model="passwordForm.current_password" type="password" required placeholder="••••••••"
              class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">New Password</label>
            <input v-model="passwordForm.password" type="password" required placeholder="Min. 8 characters"
              class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Confirm New Password</label>
            <input v-model="passwordForm.password_confirmation" type="password" required placeholder="••••••••"
              class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
          </div>

          <button type="submit" :disabled="savingPassword"
            class="px-5 py-2.5 rounded-lg bg-sky-500 text-white text-sm font-semibold hover:bg-sky-600 transition disabled:opacity-60 cursor-pointer">
            {{ savingPassword ? 'Updating...' : 'Update Password' }}
          </button>
        </form>
      </div>

      <div class="bg-white rounded-xl border border-red-100 shadow-sm p-6">
        <h2 class="text-sm font-bold text-slate-800 mb-1">Danger Zone</h2>
        <p class="text-xs text-slate-400 mb-4">Once you log out, you will need your credentials to sign back in.</p>
        <button @click="confirmLogout"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-red-50 text-red-600 text-sm font-semibold hover:bg-red-100 transition cursor-pointer">
          <LogOut class="w-4 h-4" />
          Sign Out
        </button>
      </div>

    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { LogOut, Camera } from 'lucide-vue-next';
import AppLayout from '../components/layout/AppLayout.vue';
import { useLayout } from '../composables/useLayout.js';
import api from '../services/api.js';

const { user, confirmLogout } = useLayout();

const profileForm = ref({ name: '', email: '', role: '' });
const passwordForm = ref({ current_password: '', password: '', password_confirmation: '' });
const profileMsg = ref<{ type: 'success' | 'error'; text: string } | null>(null);
const passwordMsg = ref<{ type: 'success' | 'error'; text: string } | null>(null);
const savingProfile = ref(false);
const savingPassword = ref(false);

const avatarFile = ref<File | null>(null);
const avatarPreview = ref<string | null>(null);
const uploadingAvatar = ref(false);
const avatarMsg = ref<{ type: 'success' | 'error'; text: string } | null>(null);

watch(user, (u) => {
  if (u) {
    profileForm.value.name = u.name;
    profileForm.value.email = u.email;
    profileForm.value.role = u.role ?? 'user';
  }
}, { immediate: true });

const getRoleLabel = (role: string) => {
  const roleMap: Record<string, string> = {
    admin: 'Admin',
    editor: 'Editor',
    user: 'User'
  };
  return roleMap[role] || role;
};

const saveProfile = async () => {
  savingProfile.value = true;
  profileMsg.value = null;
  try {
    await api.put(`/users/${user.value?.id}`, {
      name: profileForm.value.name,
      email: profileForm.value.email
    });
    if (user.value) {
      user.value.name = profileForm.value.name;
      user.value.email = profileForm.value.email;
    }
    profileMsg.value = { type: 'success', text: 'Profile updated successfully.' };
  } catch (e: any) {
    profileMsg.value = { type: 'error', text: e.response?.data?.message ?? 'Failed to update profile.' };
  } finally {
    savingProfile.value = false;
  }
};

const changePassword = async () => {
  if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
    passwordMsg.value = { type: 'error', text: 'Passwords do not match.' };
    return;
  }
  savingPassword.value = true;
  passwordMsg.value = null;
  try {
    await api.put(`/users/${user.value?.id}/password`, {
      current_password: passwordForm.value.current_password,
      password: passwordForm.value.password,
      password_confirmation: passwordForm.value.password_confirmation
    });
    passwordMsg.value = { type: 'success', text: 'Password updated successfully.' };
    passwordForm.value = { current_password: '', password: '', password_confirmation: '' };
  } catch (e: any) {
    passwordMsg.value = { type: 'error', text: e.response?.data?.message ?? 'Failed to update password.' };
  } finally {
    savingPassword.value = false;
  }
};

const MAX_AVATAR_SIZE = 2 * 1024 * 1024;

const onAvatarSelected = (e: Event) => {
  const input = e.target as HTMLInputElement;
  const file = input.files?.[0];
  avatarMsg.value = null;
  if (!file) return;

  if (!['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) {
    avatarMsg.value = { type: 'error', text: 'Please choose a PNG, JPG, or WEBP image.' };
    input.value = '';
    return;
  }
  if (file.size > MAX_AVATAR_SIZE) {
    avatarMsg.value = { type: 'error', text: 'Image must be under 2MB.' };
    input.value = '';
    return;
  }

  avatarFile.value = file;
  avatarPreview.value = URL.createObjectURL(file);
};

const cancelAvatar = () => {
  if (avatarPreview.value) URL.revokeObjectURL(avatarPreview.value);
  avatarFile.value = null;
  avatarPreview.value = null;
  avatarMsg.value = null;
};

const uploadAvatar = async () => {
  if (!avatarFile.value || !user.value) return;
  uploadingAvatar.value = true;
  avatarMsg.value = null;

  const formData = new FormData();
  formData.append('avatar', avatarFile.value);

  try {
    const { data } = await api.post(`/users/${user.value.id}/avatar`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    user.value.avatar_url = data.data.avatar_url;
    avatarMsg.value = { type: 'success', text: 'Photo updated.' };
    if (avatarPreview.value) URL.revokeObjectURL(avatarPreview.value);
    avatarFile.value = null;
    avatarPreview.value = null;
  } catch (e: any) {
    avatarMsg.value = { type: 'error', text: e.response?.data?.message ?? 'Failed to upload photo.' };
  } finally {
    uploadingAvatar.value = false;
  }
};
</script>

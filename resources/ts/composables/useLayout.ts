import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import type { User } from '../types';

const sidebarOpen = ref(false);
const user = ref<User | null>(null);
const showLogoutModal = ref(false);
const isFetching = ref(false);

export function useLayout() {
    const router = useRouter();

    const fetchUser = async () => {
        if (isFetching.value || user.value) return;
        isFetching.value = true;
        try {
            const res = await api.get('/profile');
            user.value = res.data.user;
        } catch {
            // token invalid
        } finally {
            isFetching.value = false;
        }
    };

    const confirmLogout = () => {
        showLogoutModal.value = true;
    };

    const executeLogout = async () => {
        try { await api.post('/logout'); } catch { /* ignore */ }
        localStorage.removeItem('access_token');
        showLogoutModal.value = false;
        user.value = null;
        router.push('/login');
    };

    onMounted(() => {
        sidebarOpen.value = window.innerWidth >= 1024;
        fetchUser();
    });

    return { sidebarOpen, user, showLogoutModal, confirmLogout, executeLogout };
}

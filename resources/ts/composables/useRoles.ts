import { ref } from 'vue';
import api from '../services/api';

export interface Role {
  id: number;
  name: string;
  description: string | null;
  created_at: string;
  updated_at: string;
}

export function useRoles() {
  const roles = ref<Role[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const fetchRoles = async () => {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.get('/roles');
      roles.value = response.data.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch roles';
    } finally {
      loading.value = false;
    }
  };

  const createRole = async (roleData: Partial<Role>) => {
    try {
      const response = await api.post('/roles', roleData);
      roles.value.unshift(response.data.data);
      return response.data.data;
    } catch (err: any) {
      throw err;
    }
  };

  const updateRole = async (id: number, roleData: Partial<Role>) => {
    try {
      const response = await api.put(`/roles/${id}`, roleData);
      const index = roles.value.findIndex((r) => r.id === id);
      if (index !== -1) {
        roles.value[index] = response.data.data;
      }
      return response.data.data;
    } catch (err: any) {
      throw err;
    }
  };

  const deleteRole = async (id: number) => {
    try {
      await api.delete(`/roles/${id}`);
      roles.value = roles.value.filter((r) => r.id !== id);
    } catch (err: any) {
      throw err;
    }
  };

  return {
    roles,
    loading,
    error,
    fetchRoles,
    createRole,
    updateRole,
    deleteRole,
  };
}

import { ref } from 'vue';
import api from '../services/api';

export interface Comment {
  id: number;
  task_id: number;
  user_id: number;
  comment: string;
  created_at: string;
  updated_at: string;
  user?: { id: number; name: string; avatar_url: string | null };
}

export function useComments() {
  const comments = ref<Comment[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const fetchComments = async (taskId?: number) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.get('/comments');
      // If backend doesn't filter by task_id yet, filter on frontend
      let allComments = response.data.data;
      if (taskId) {
        allComments = allComments.filter((c: Comment) => c.task_id === taskId);
      }
      comments.value = allComments;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch comments';
    } finally {
      loading.value = false;
    }
  };

  const createComment = async (commentData: Partial<Comment>) => {
    try {
      const response = await api.post('/comments', commentData);
      comments.value.push(response.data.data);
      return response.data.data;
    } catch (err: any) {
      throw err;
    }
  };

  const deleteComment = async (id: number) => {
    try {
      await api.delete(`/comments/${id}`);
      comments.value = comments.value.filter((c) => c.id !== id);
    } catch (err: any) {
      throw err;
    }
  };

  return { comments, loading, error, fetchComments, createComment, deleteComment };
}

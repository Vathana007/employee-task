import { Play, CheckCircle2, Clock } from 'lucide-vue-next';
import type { TaskStatus } from '../types/index.js';

export const STATUS_OPTIONS: { value: TaskStatus; labelKey: string }[] = [
  { value: 'pending', labelKey: 'status.pending' },
  { value: 'in_progress', labelKey: 'status.in_progress' },
  { value: 'completed', labelKey: 'status.completed' },
];

// Tasks: Start -> Complete -> "Completed" (no button action)
export const taskAction = (s: TaskStatus) => ({
  pending:     { labelKey: 'common.start',     next: 'in_progress' as TaskStatus | null, icon: Play,         btn: 'bg-sky-50 text-sky-600 hover:bg-sky-100' },
  in_progress: { labelKey: 'status.in_progress',  next: 'completed'   as TaskStatus | null, icon: CheckCircle2, btn: 'bg-sky-50 text-sky-600 hover:bg-sky-100' },
  completed:   { labelKey: 'status.completed', next: null as TaskStatus | null,          icon: CheckCircle2, btn: 'bg-emerald-50 text-emerald-600' },
})[s];

// Projects: Start -> "In Progress" -> "Completed" (the last two follow the tasks automatically)
export const projectAction = (s: TaskStatus) => ({
  pending:     { labelKey: 'common.start',       next: 'in_progress' as TaskStatus | null, icon: Play,         btn: 'bg-sky-50 text-sky-600 hover:bg-sky-100' },
  in_progress: { labelKey: 'status.in_progress', next: null as TaskStatus | null,          icon: Clock,        btn: 'bg-sky-50 text-sky-600' },
  completed:   { labelKey: 'status.completed',   next: null as TaskStatus | null,          icon: CheckCircle2, btn: 'bg-emerald-50 text-emerald-600' },
})[s];

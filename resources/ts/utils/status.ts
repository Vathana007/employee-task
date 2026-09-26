import { Play, CheckCircle2, Clock } from 'lucide-vue-next';
import type { TaskStatus } from '../types/index.js';

export const STATUS_OPTIONS: { value: TaskStatus; label: string }[] = [
  { value: 'pending', label: 'Pending' },
  { value: 'in_progress', label: 'In Progress' },
  { value: 'completed', label: 'Completed' },
];

// Tasks: Start -> Complete -> "Completed" (no button action)
export const taskAction = (s: TaskStatus) => ({
  pending:     { label: 'Start',     next: 'in_progress' as TaskStatus | null, icon: Play,         btn: 'bg-sky-50 text-sky-600 hover:bg-sky-100' },
  in_progress: { label: 'In Progress',  next: 'completed'   as TaskStatus | null, icon: CheckCircle2, btn: 'bg-sky-50 text-sky-600 hover:bg-sky-100' },
  completed:   { label: 'Completed', next: null as TaskStatus | null,          icon: CheckCircle2, btn: 'bg-emerald-50 text-emerald-600' },
})[s];

// Projects: Start -> "In Progress" -> "Completed" (the last two follow the tasks automatically)
export const projectAction = (s: TaskStatus) => ({
  pending:     { label: 'Start',       next: 'in_progress' as TaskStatus | null, icon: Play,         btn: 'bg-sky-50 text-sky-600 hover:bg-sky-100' },
  in_progress: { label: 'In Progress', next: null as TaskStatus | null,          icon: Clock,        btn: 'bg-sky-50 text-sky-600' },
  completed:   { label: 'Completed',   next: null as TaskStatus | null,          icon: CheckCircle2, btn: 'bg-emerald-50 text-emerald-600' },
})[s];

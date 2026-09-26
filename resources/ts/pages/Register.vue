<template>
  <div
    class="min-h-screen relative flex items-center justify-center overflow-hidden bg-gradient-to-br from-sky-50 via-white to-sky-100 py-12">
    <!-- Decorative background elements -->
    <div
      class="absolute top-[10%] left-[-5%] w-96 h-96 bg-sky-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob">
    </div>

    <div
      class="absolute top-[40%] right-[-10%] w-96 h-96 bg-cyan-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000">
    </div>

    <div
      class="absolute bottom-[-10%] left-[20%] w-96 h-96 bg-sky-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000">
    </div>

    <div class="relative w-full max-w-lg px-6 lg:px-8 z-10">
      <div
        class="backdrop-blur-xl bg-white/70 shadow-[0_8px_30px_rgb(0,0,0,0.04)] ring-1 ring-sky-900/5 rounded-3xl p-8 sm:p-10 transition-all duration-500 hover:shadow-[0_8px_40px_rgb(14,165,233,0.1)]">
        <!-- Header -->
        <div class="sm:mx-auto sm:w-full text-center">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-sky-100 mb-6 shadow-inner">
            <UserPlus class="w-8 h-8 text-sky-600" />
          </div>

          <h2 class="text-3xl font-bold tracking-tight text-slate-800">
            Join Us Today
          </h2>

          <p class="mt-2 text-sm text-slate-500">
            Create your account to get started
          </p>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full">
          <form class="space-y-5" @submit.prevent="handleRegister">
            <!-- General Error Alert -->
            <div v-if="error"
              class="bg-red-50/80 backdrop-blur-sm border-l-4 border-red-500 p-4 rounded-r-lg flex items-center shadow-sm animate-pulse">
              <CircleAlert class="w-5 h-5 text-red-500 mr-3 shrink-0" />

              <p class="text-sm text-red-700 font-medium break-words">
                {{ error }}
              </p>
            </div>

            <!-- Full Name -->
            <div class="space-y-1">
              <label for="name" class="block text-sm font-semibold leading-6 text-slate-700">
                Full Name
              </label>

              <div class="relative">
                <User class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />

                <input 
                  id="name" 
                  v-model="form.name" 
                  name="name" 
                  type="text" 
                  autocomplete="name" 
                  required
                  placeholder="John Doe"
                  class="block w-full rounded-xl border-0 py-2.5 pl-10 text-slate-900 bg-white/50 shadow-sm ring-1 ring-inset transition-all duration-300 sm:text-sm sm:leading-6 placeholder:text-slate-400"
                  :class="errors.name ? 'ring-red-500 focus:ring-red-500' : 'ring-slate-200 focus:ring-sky-500'"
                  @input="errors.name = ''"
                  @focus="errors.name = ''" />
              </div>

              <!-- Field error message -->
              <span v-if="errors.name" class="text-red-600 text-sm flex items-center mt-1">
                <span class="mr-1">❌</span> {{ errors.name }}
              </span>
            </div>

            <!-- Email -->
            <div class="space-y-1">
              <label for="email" class="block text-sm font-semibold leading-6 text-slate-700">
                Email Address
              </label>

              <div class="relative">
                <Mail class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />

                <input 
                  id="email" 
                  v-model="form.email" 
                  name="email" 
                  type="email" 
                  autocomplete="email" 
                  required
                  placeholder="you@example.com"
                  class="block w-full rounded-xl border-0 py-2.5 pl-10 text-slate-900 bg-white/50 shadow-sm ring-1 ring-inset transition-all duration-300 sm:text-sm sm:leading-6 placeholder:text-slate-400"
                  :class="errors.email ? 'ring-red-500 focus:ring-red-500' : 'ring-slate-200 focus:ring-sky-500'"
                  @input="errors.email = ''"
                  @focus="errors.email = ''" />
              </div>

              <!-- Field error message -->
              <span v-if="errors.email" class="text-red-600 text-sm flex items-center mt-1">
                <span class="mr-1">❌</span> {{ errors.email }}
              </span>
            </div>

            <!-- Password -->
            <div class="space-y-1">
              <label for="password" class="block text-sm font-semibold leading-6 text-slate-700">
                Password
              </label>

              <div class="relative">
                <Lock class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />

                <input 
                  id="password" 
                  v-model="form.password" 
                  name="password" 
                  :type="showPassword ? 'text' : 'password'"
                  autocomplete="new-password" 
                  required 
                  placeholder="••••••••"
                  class="block w-full rounded-xl border-0 py-2.5 pl-10 pr-12 text-slate-900 bg-white/50 shadow-sm ring-1 ring-inset transition-all duration-300 sm:text-sm sm:leading-6 placeholder:text-slate-400"
                  :class="errors.password ? 'ring-red-500 focus:ring-red-500' : 'ring-slate-200 focus:ring-sky-500'"
                  @input="validatePassword"
                  @focus="errors.password = ''" />

                <button 
                  type="button" 
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-sky-500 transition-colors cursor-pointer"
                  :aria-label="showPassword ? 'Hide password' : 'Show password'">
                  <EyeOff v-if="showPassword" class="h-5 w-5" />
                  <Eye v-else class="h-5 w-5" />
                </button>
              </div>

              <!-- Field error and hint -->
              <div class="flex justify-between items-center mt-1">
                <span v-if="errors.password" class="text-red-600 text-sm">
                  <span class="mr-1">❌</span>{{ errors.password }}
                </span>
                <span v-else-if="form.password && form.password.length >= 8" class="text-green-600 text-sm">
                  ✅ Password strong
                </span>
                <span v-else class="text-slate-400 text-xs">
                  Min 8 characters
                </span>
              </div>
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1">
              <label for="password_confirmation" class="block text-sm font-semibold leading-6 text-slate-700">
                Confirm Password
              </label>

              <div class="relative">
                <ShieldCheck class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />

                <input 
                  id="password_confirmation" 
                  v-model="form.password_confirmation" 
                  name="password_confirmation"
                  :type="showPasswordConfirmation ? 'text' : 'password'"
                  autocomplete="new-password" 
                  required 
                  placeholder="••••••••"
                  class="block w-full rounded-xl border-0 py-2.5 pl-10 pr-12 text-slate-900 bg-white/50 shadow-sm ring-1 ring-inset transition-all duration-300 sm:text-sm sm:leading-6 placeholder:text-slate-400"
                  :class="errors.password_confirmation ? 'ring-red-500 focus:ring-red-500' : 'ring-slate-200 focus:ring-sky-500'"
                  @input="checkPasswordMatch"
                  @focus="errors.password_confirmation = ''" />

                <button 
                  type="button" 
                  @click="showPasswordConfirmation = !showPasswordConfirmation"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-sky-500 transition-colors cursor-pointer"
                  :aria-label="showPasswordConfirmation ? 'Hide password' : 'Show password'">
                  <EyeOff v-if="showPasswordConfirmation" class="h-5 w-5" />
                  <Eye v-else class="h-5 w-5" />
                </button>
              </div>

              <!-- Field error and match status -->
              <div class="flex justify-between items-center mt-1">
                <span v-if="errors.password_confirmation" class="text-red-600 text-sm">
                  <span class="mr-1">❌</span>{{ errors.password_confirmation }}
                </span>
                <span v-else-if="form.password_confirmation && passwordsMatch" class="text-green-600 text-sm">
                  ✅ Passwords match
                </span>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
              <button 
                type="submit" 
                :disabled="!isFormValid || loading"
                class="group relative flex w-full justify-center rounded-xl bg-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 hover:bg-sky-400 hover:shadow-sky-500/50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500 transition-all duration-300 disabled:opacity-70 transform hover:disabled:translate-y-0 hover:-translate-y-0.5 cursor-pointer">
                <span v-if="!loading" class="flex items-center">
                  Create Account

                  <ArrowRight class="w-4 h-4 ml-2 transition-transform duration-300 group-hover:translate-x-1" />
                </span>

                <span v-else class="flex items-center">
                  <LoaderCircle class="animate-spin -ml-1 mr-3 h-5 w-5" />

                  Processing...
                </span>
              </button>
            </div>
          </form>

          <!-- Login Link -->
          <p class="mt-8 text-center text-sm text-slate-500">
            Already have an account?

            <router-link 
              to="/login"
              class="font-semibold text-sky-600 hover:text-sky-500 transition-colors relative after:content-[''] after:absolute after:w-full after:scale-x-0 after:h-0.5 after:bottom-0 after:left-0 after:bg-sky-500 after:origin-bottom-right after:transition-transform after:duration-300 hover:after:scale-x-100 hover:after:origin-bottom-left">
              Sign in here
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  ArrowRight,
  CircleAlert,
  Eye,
  EyeOff,
  LoaderCircle,
  Lock,
  Mail,
  ShieldCheck,
  User,
  UserPlus,
} from 'lucide-vue-next';
import api from '../services/api';

const router = useRouter();

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

// Field-level errors
const errors = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

// General error message
const error = ref('');
const loading = ref(false);

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

// Check if passwords match in real-time
const passwordsMatch = computed(() => {
  if (!form.value.password || !form.value.password_confirmation) {
    return true; // Don't show error if either is empty
  }
  return form.value.password === form.value.password_confirmation;
});

// Check if entire form is valid
const isFormValid = computed(() => {
  return form.value.name && 
         form.value.email && 
         form.value.password && 
         form.value.password_confirmation &&
         passwordsMatch.value &&
         !Object.values(errors.value).some(e => e);
});

// Validate password strength on input
const validatePassword = () => {
  if (form.value.password && form.value.password.length < 8) {
    errors.value.password = 'Password must be at least 8 characters';
  } else {
    errors.value.password = '';
  }
};

// Real-time password match check
const checkPasswordMatch = () => {
  if (form.value.password_confirmation && form.value.password !== form.value.password_confirmation) {
    errors.value.password_confirmation = 'Passwords do not match';
  } else {
    errors.value.password_confirmation = '';
  }
};

const handleRegister = async () => {
  error.value = '';
  errors.value = { name: '', email: '', password: '', password_confirmation: '' };

  // Validate all fields before submit
  let hasErrors = false;

  if (!form.value.name.trim()) {
    errors.value.name = 'Name is required';
    hasErrors = true;
  }

  if (!form.value.email.trim()) {
    errors.value.email = 'Email is required';
    hasErrors = true;
  }

  if (!form.value.password) {
    errors.value.password = 'Password is required';
    hasErrors = true;
  } else if (form.value.password.length < 8) {
    errors.value.password = 'Password must be at least 8 characters';
    hasErrors = true;
  }

  if (!form.value.password_confirmation) {
    errors.value.password_confirmation = 'Confirm password is required';
    hasErrors = true;
  } else if (form.value.password !== form.value.password_confirmation) {
    errors.value.password_confirmation = 'Passwords do not match';
    hasErrors = true;
  }

  // Don't submit if there are errors
  if (hasErrors) {
    return;
  }

  loading.value = true;

  try {
    const response = await api.post('/register', form.value);

    localStorage.setItem('access_token', response.data.access_token);
    
    // Redirect to email verification page
    router.push(`/verify-email?email=${encodeURIComponent(form.value.email)}`);

  } catch (err: any) {
    // Check if validation errors returned from backend
    if (err.response?.data?.errors) {
      const backendErrors = err.response.data.errors;
      
      // Map backend errors to form fields
      if (backendErrors.name) {
        errors.value.name = Array.isArray(backendErrors.name) 
          ? backendErrors.name[0] 
          : backendErrors.name;
      }
      if (backendErrors.email) {
        errors.value.email = Array.isArray(backendErrors.email) 
          ? backendErrors.email[0] 
          : backendErrors.email;
      }
      if (backendErrors.password) {
        errors.value.password = Array.isArray(backendErrors.password) 
          ? backendErrors.password[0] 
          : backendErrors.password;
      }
      if (backendErrors.password_confirmation) {
        errors.value.password_confirmation = Array.isArray(backendErrors.password_confirmation) 
          ? backendErrors.password_confirmation[0] 
          : backendErrors.password_confirmation;
      }
    } else {
      // Show general error if no field-specific errors
      error.value = err.response?.data?.message || 'Failed to register. Please try again.';
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style>
@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }

  33% {
    transform: translate(30px, -50px) scale(1.1);
  }

  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }

  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}
</style>
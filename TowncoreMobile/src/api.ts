import API_BASE_URL from './config';
import * as SecureStore from 'expo-secure-store';

const TOKEN_KEY = 'auth_token';

async function getToken(): Promise<string | null> {
  return await SecureStore.getItemAsync(TOKEN_KEY);
}

export async function setToken(token: string): Promise<void> {
  await SecureStore.setItemAsync(TOKEN_KEY, token);
}

export async function removeToken(): Promise<void> {
  await SecureStore.deleteItemAsync(TOKEN_KEY);
}

type RequestOptions = {
  method?: string;
  body?: any;
  isFormData?: boolean;
};

export type MobileBrandingResponse = {
  app_name: string;
  colors: {
    primary: string;
    secondary: string;
    background: string;
    primary_dark?: string;
    text_light?: string;
    text_dark?: string;
  };
  assets: {
    logo_url: string | null;
    app_icon_url: string | null;
  };
};

export async function api(endpoint: string, options: RequestOptions = {}) {
  const { method = 'GET', body, isFormData = false } = options;
  const token = await getToken();

  const headers: Record<string, string> = {
    Accept: 'application/json',
  };

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  if (!isFormData) {
    headers['Content-Type'] = 'application/json';
  }

  const config: RequestInit = {
    method,
    headers,
  };

  if (body) {
    config.body = isFormData ? body : JSON.stringify(body);
  }

  const response = await fetch(`${API_BASE_URL}${endpoint}`, config);

  if (response.status === 401) {
    await removeToken();
    throw new Error('UNAUTHORIZED');
  }

  if (!response.ok) {
    const error = await response.json().catch(() => ({ message: 'Request failed' }));
    throw new Error(error.message || `HTTP ${response.status}`);
  }

  if (response.status === 204) return null;

  return response.json();
}

// Auth API
export const authApi = {
  login: (email: string, password: string) =>
    api('/auth/login', { method: 'POST', body: { email, password } }),

  register: (data: { name: string; email: string; password: string; password_confirmation: string; role: string }) =>
    api('/auth/register', { method: 'POST', body: data }),

  logout: () => api('/auth/logout', { method: 'POST' }),

  getUser: () => api('/user'),

  updateProfile: (data: { name?: string; email?: string }) =>
    api('/user/profile', { method: 'PUT', body: data }),

  updatePassword: (data: { current_password: string; password: string; password_confirmation: string }) =>
    api('/user/password', { method: 'PUT', body: data }),

  uploadProfileImage: (formData: FormData) =>
    api('/user/profile-image', { method: 'POST', body: formData, isFormData: true }),
};

// Dashboard API
export const dashboardApi = {
  get: () => api('/dashboard'),
};

export const brandingApi = {
  get: () => api('/mobile/branding') as Promise<MobileBrandingResponse>,
};

// Projects API
export const projectsApi = {
  list: (page = 1, status?: string) =>
    api(`/projects?page=${page}${status ? `&status=${status}` : ''}`),

  get: (id: number) => api(`/projects/${id}`),

  create: (data: { title: string; description: string; categories?: number[] }) =>
    api('/projects', { method: 'POST', body: data }),

  assign: (id: number, freelancerId: number) =>
    api(`/projects/${id}/assign`, { method: 'POST', body: { freelancer_id: freelancerId } }),

  updateStatus: (id: number, status: string) =>
    api(`/projects/${id}/status`, { method: 'PUT', body: { status } }),

  uploadFile: (id: number, formData: FormData) =>
    api(`/projects/${id}/files`, { method: 'POST', body: formData, isFormData: true }),

  categories: () => api('/categories'),
};

// Invoices API
export const invoicesApi = {
  list: (page = 1) => api(`/invoices?page=${page}`),
  get: (id: number) => api(`/invoices/${id}`),
  freelancerInvoices: (page = 1) => api(`/freelancer-invoices?page=${page}`),
  submitFreelancerInvoice: (formData: FormData) =>
    api('/freelancer-invoices', { method: 'POST', body: formData, isFormData: true }),
};

// Chat API
export const chatApi = {
  conversations: (page = 1) => api(`/conversations?page=${page}`),
  messages: (conversationId: number, page = 1) =>
    api(`/conversations/${conversationId}/messages?page=${page}`),
  sendMessage: (conversationId: number, formData: FormData) =>
    api(`/conversations/${conversationId}/messages`, { method: 'POST', body: formData, isFormData: true }),
  sendTextMessage: (conversationId: number, message: string) =>
    api(`/conversations/${conversationId}/messages`, { method: 'POST', body: { message, message_type: 'text' } }),
  createConversation: (userId: number, message?: string) =>
    api('/conversations', { method: 'POST', body: { user_id: userId, ...(message ? { message } : {}) } }),
  findOrCreateByProject: (projectId: number) =>
    api('/conversations/by-project', { method: 'POST', body: { project_id: projectId } }),
  contacts: () => api('/contacts'),
};

// Portfolio API
export const portfolioApi = {
  list: (page = 1, freelancerId?: number) =>
    api(`/portfolio?page=${page}${freelancerId ? `&freelancer_id=${freelancerId}` : ''}`),
  myPortfolio: (page = 1) => api(`/my-portfolio?page=${page}`),
  create: (formData: FormData) =>
    api('/portfolio', { method: 'POST', body: formData, isFormData: true }),
  delete: (id: number) => api(`/portfolio/${id}`, { method: 'DELETE' }),
  adminList: (page = 1, status?: string) =>
    api(`/admin/portfolio?page=${page}${status ? `&status=${status}` : ''}`),
  updateStatus: (id: number, status: string) =>
    api(`/admin/portfolio/${id}/status`, { method: 'PUT', body: { status } }),
};

// Blog API
export const blogApi = {
  list: (page = 1) => api(`/blog?page=${page}`),
  get: (slug: string) => api(`/blog/${slug}`),
  adminList: (page = 1, status?: string) =>
    api(`/admin/blog?page=${page}${status ? `&status=${status}` : ''}`),
  create: (formData: FormData) =>
    api('/admin/blog', { method: 'POST', body: formData, isFormData: true }),
  update: (id: number, formData: FormData) =>
    api(`/admin/blog/${id}`, { method: 'PUT', body: formData, isFormData: true }),
  delete: (id: number) => api(`/admin/blog/${id}`, { method: 'DELETE' }),
};

// Notifications API
export const notificationsApi = {
  list: (page = 1) => api(`/notifications?page=${page}`),
  unreadCount: () => api('/notifications/unread-count'),
  markAsRead: (id: string) => api(`/notifications/${id}/read`, { method: 'POST' }),
  markAllAsRead: () => api('/notifications/read-all', { method: 'POST' }),
};

// Live Chat API (public — no auth required)
export const liveChatApi = {
  startSession: (name: string, email: string, sessionKey?: string) =>
    api('/live-chat/start', { method: 'POST', body: { name, email, session_key: sessionKey || null } }),

  sendMessage: (sessionKey: string, body: string) =>
    api('/live-chat/send', { method: 'POST', body: { session_key: sessionKey, body } }),

  getMessages: (sessionKey: string, after?: number) =>
    api(`/live-chat/messages?session_key=${encodeURIComponent(sessionKey)}${after ? `&after=${after}` : ''}`),
};

// Live Chat Agent API (auth required — admin/agent)
export const liveChatAgentApi = {
  sessions: (status?: string) =>
    api(`/live-chat/agent/sessions${status ? `?status=${status}` : ''}`),

  history: () => api('/live-chat/agent/history'),

  joinSession: (sessionId: number) =>
    api(`/live-chat/agent/${sessionId}/join`, { method: 'POST' }),

  sendMessage: (sessionId: number, body: string) =>
    api(`/live-chat/agent/${sessionId}/send`, { method: 'POST', body: { body } }),

  getMessages: (sessionId: number, after?: number) =>
    api(`/live-chat/agent/${sessionId}/messages${after ? `?after=${after}` : ''}`),

  closeSession: (sessionId: number) =>
    api(`/live-chat/agent/${sessionId}/close`, { method: 'POST' }),
};

// Subscription API
export const subscriptionApi = {
  getPlans: () => api('/subscription/plans'),
  requestSubscription: (data: {
    plan_id: number;
    billing_cycle: string;
    payment_method: string;
    payment_reference?: string;
    notes?: string;
  }) => api('/subscription/request', { method: 'POST', body: data }),
  startTrial: () => api('/subscription/trial', { method: 'POST' }),
  requestHistory: () => api('/subscription/history'),
};

// Checklist API
export const checklistApi = {
  list: () => api('/checklist'),
};

// Client Files API
export const clientFilesApi = {
  list: (folderId?: number | null) =>
    api(`/files${folderId ? `?folder_id=${folderId}` : ''}`),
  upload: (formData: FormData) =>
    api('/files', { method: 'POST', body: formData, isFormData: true }),
  download: (fileId: number) => api(`/files/${fileId}/download`),
  delete: (fileId: number) => api(`/files/${fileId}`, { method: 'DELETE' }),
  createFolder: (name: string, parentId?: number | null) =>
    api('/folders', { method: 'POST', body: { name, parent_id: parentId || null } }),
  renameFolder: (folderId: number, name: string) =>
    api(`/folders/${folderId}`, { method: 'PATCH', body: { name } }),
  deleteFolder: (folderId: number) =>
    api(`/folders/${folderId}`, { method: 'DELETE' }),
};

// Admin Users API
export const adminUsersApi = {
  list: (page = 1, role?: string, search?: string) =>
    api(`/admin/users?page=${page}${role ? `&role=${role}` : ''}${search ? `&search=${encodeURIComponent(search)}` : ''}`),
  get: (id: number) => api(`/admin/users/${id}`),
  create: (data: { name: string; email: string; password: string; password_confirmation: string; role: string }) =>
    api('/admin/users', { method: 'POST', body: data }),
  update: (id: number, data: any) =>
    api(`/admin/users/${id}`, { method: 'PUT', body: data }),
  delete: (id: number) => api(`/admin/users/${id}`, { method: 'DELETE' }),
  freelancers: () => api('/admin/users/freelancers'),
};

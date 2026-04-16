export type ThemeColors = typeof lightColors;

export const lightColors = {
  primary: '#f97316',
  primaryDark: '#ea580c',
  secondary: '#64748b',
  success: '#16a34a',
  warning: '#f59e0b',
  danger: '#dc2626',
  background: '#f8fafc',
  white: '#ffffff',
  card: '#ffffff',
  text: '#0f172a',
  textSecondary: '#64748b',
  textLight: '#94a3b8',
  border: '#e2e8f0',
  inputBg: '#f1f5f9',
  shadow: '#000000',
};

export const darkColors: ThemeColors = {
  primary: '#f97316',
  primaryDark: '#ea580c',
  secondary: '#94a3b8',
  success: '#22c55e',
  warning: '#fbbf24',
  danger: '#ef4444',
  background: '#09090b',
  white: '#141416',
  card: '#141416',
  text: '#fafafa',
  textSecondary: '#a1a1aa',
  textLight: '#71717a',
  border: 'rgba(255,255,255,0.10)',
  inputBg: '#1a1a1e',
  shadow: '#000000',
};

// Default export — screens that don't use useTheme() yet still work
export const colors = lightColors;

export const statusColors: Record<string, string> = {
  pending: '#f59e0b',
  assigned: '#3b82f6',
  in_progress: '#8b5cf6',
  completed: '#16a34a',
  cancelled: '#dc2626',
  paid: '#16a34a',
  unpaid: '#dc2626',
  partial: '#f59e0b',
  approved: '#16a34a',
  rejected: '#dc2626',
  new: '#3b82f6',
  contacted: '#f59e0b',
  converted: '#16a34a',
};

export const spacing = {
  xs: 4,
  sm: 8,
  md: 16,
  lg: 24,
  xl: 32,
};

export const fontSize = {
  xs: 12,
  sm: 14,
  md: 16,
  lg: 18,
  xl: 22,
  xxl: 28,
};

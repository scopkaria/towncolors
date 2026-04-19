export type ThemeColors = typeof lightColors;

export const lightColors = {
  primary: '#FFB162',
  primaryDark: '#A35139',
  secondary: '#A35139',
  success: '#16a34a',
  warning: '#f59e0b',
  danger: '#dc2626',
  background: '#EEE9DF',
  white: '#ffffff',
  card: '#ffffff',
  text: '#1B2632',
  textSecondary: '#2C3B4D',
  textLight: '#9E9480',
  border: '#D4CABB',
  inputBg: '#F5F1EB',
  shadow: '#000000',
};

export const darkColors: ThemeColors = {
  primary: '#FFB162',
  primaryDark: '#A35139',
  secondary: '#A35139',
  success: '#22c55e',
  warning: '#fbbf24',
  danger: '#ef4444',
  background: '#111A24',
  white: '#1B2632',
  card: '#1B2632',
  text: '#EEE9DF',
  textSecondary: '#9E9480',
  textLight: '#C9C1B1',
  border: '#2C3B4D',
  inputBg: '#0A0E14',
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

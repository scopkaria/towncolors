import React, { createContext, useContext, useEffect, useState } from 'react';
import { useColorScheme } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { lightColors, darkColors, ThemeColors } from '../theme';
import { useBranding } from './BrandingContext';

const THEME_KEY = 'app_theme_mode';

type ThemeContextType = {
  isDark: boolean;
  colors: ThemeColors;
  toggleTheme: () => void;
};

const ThemeContext = createContext<ThemeContextType>({
  isDark: false,
  colors: lightColors,
  toggleTheme: () => {},
});

export function ThemeProvider({ children }: { children: React.ReactNode }) {
  const branding = useBranding();
  const systemScheme = useColorScheme();
  const [isDark, setIsDark] = useState(systemScheme === 'dark');

  useEffect(() => {
    AsyncStorage.getItem(THEME_KEY).then((val) => {
      if (val === 'dark') setIsDark(true);
      else if (val === 'light') setIsDark(false);
      else setIsDark(systemScheme === 'dark'); // first launch — follow system
    });
  }, []);

  function toggleTheme() {
    setIsDark((prev) => {
      const next = !prev;
      AsyncStorage.setItem(THEME_KEY, next ? 'dark' : 'light');
      return next;
    });
  }

  const baseColors = isDark ? darkColors : lightColors;

  const colors: ThemeColors = {
    ...baseColors,
    primary: branding.colors?.primary || baseColors.primary,
    primaryDark: branding.colors?.primary_dark || branding.colors?.secondary || baseColors.primaryDark,
    secondary: branding.colors?.secondary || baseColors.secondary,
    ...(isDark ? {} : { background: branding.colors?.background || baseColors.background }),
  };

  return (
    <ThemeContext.Provider value={{ isDark, colors, toggleTheme }}>
      {children}
    </ThemeContext.Provider>
  );
}

export function useTheme() {
  return useContext(ThemeContext);
}

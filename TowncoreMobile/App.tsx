import React, { useEffect, useRef, useCallback } from 'react';
import { StatusBar } from 'expo-status-bar';
import { Alert } from 'react-native';
import * as Notifications from 'expo-notifications';
import * as Updates from 'expo-updates';
import { AuthProvider, useAuth } from './src/contexts/AuthContext';
import { BrandingProvider } from './src/contexts/BrandingContext';
import { ThemeProvider, useTheme } from './src/contexts/ThemeContext';
import AppNavigator, { navigationRef } from './src/navigation/AppNavigator';
import { registerForPushNotifications, getNotificationRoute } from './src/services/notifications';

function UpdateChecker() {
  const checkForUpdates = useCallback(async () => {
    if (__DEV__) return; // skip in development
    try {
      const update = await Updates.checkForUpdateAsync();
      if (update.isAvailable) {
        await Updates.fetchUpdateAsync();
        Alert.alert(
          'Update Available',
          'A new version has been downloaded. Restart now to apply?',
          [
            { text: 'Later', style: 'cancel' },
            { text: 'Restart', onPress: () => Updates.reloadAsync() },
          ]
        );
      }
    } catch (e) {
      // silently fail – user can still use current version
      console.log('Update check failed:', e);
    }
  }, []);

  useEffect(() => {
    checkForUpdates();
  }, [checkForUpdates]);

  return null;
}

function NotificationHandler() {
  const { user } = useAuth();
  const responseListener = useRef<Notifications.Subscription | null>(null);

  useEffect(() => {
    if (user) {
      registerForPushNotifications();
    }
  }, [user]);

  useEffect(() => {
    responseListener.current = Notifications.addNotificationResponseReceivedListener(response => {
      const data = response.notification.request.content.data;
      const route = getNotificationRoute(data);
      if (route && navigationRef.isReady()) {
        navigationRef.navigate(route.screen, route.params);
      }
    });

    return () => {
      if (responseListener.current) {
        responseListener.current.remove();
      }
    };
  }, []);

  return null;
}

function Main() {
  const { isDark } = useTheme();
  return (
    <AuthProvider>
      <UpdateChecker />
      <NotificationHandler />
      <StatusBar style={isDark ? 'light' : 'dark'} />
      <AppNavigator />
    </AuthProvider>
  );
}

export default function App() {
  return (
    <BrandingProvider>
      <ThemeProvider>
        <Main />
      </ThemeProvider>
    </BrandingProvider>
  );
}

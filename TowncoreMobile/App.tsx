import React, { useEffect, useRef } from 'react';
import { StatusBar } from 'expo-status-bar';
import * as Notifications from 'expo-notifications';
import { AuthProvider, useAuth } from './src/contexts/AuthContext';
import { ThemeProvider, useTheme } from './src/contexts/ThemeContext';
import AppNavigator, { navigationRef } from './src/navigation/AppNavigator';
import { registerForPushNotifications, getNotificationRoute } from './src/services/notifications';

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
      <NotificationHandler />
      <StatusBar style={isDark ? 'light' : 'dark'} />
      <AppNavigator />
    </AuthProvider>
  );
}

export default function App() {
  return (
    <ThemeProvider>
      <Main />
    </ThemeProvider>
  );
}

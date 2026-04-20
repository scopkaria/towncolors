import { useEffect, useRef, useState, useCallback } from 'react';
import * as Notifications from 'expo-notifications';
import { AppState, Platform } from 'react-native';
import { useAuth } from '../contexts/AuthContext';
import { notificationsApi } from '../api';
import { registerForPushNotifications, getNotificationRoute } from '../services/notifications';
import { useNavigation, NavigationProp } from '@react-navigation/native';

export function useNotifications() {
  const { user } = useAuth();
  const [unreadCount, setUnreadCount] = useState(0);
  const notificationListener = useRef<Notifications.Subscription>();
  const responseListener = useRef<Notifications.Subscription>();
  const navigation = useNavigation<any>();

  // Fetch unread count
  const refreshUnreadCount = useCallback(async () => {
    if (!user) return;
    try {
      const data = await notificationsApi.unreadCount();
      const count = data.count ?? data.unread_count ?? 0;
      setUnreadCount(count);
      await Notifications.setBadgeCountAsync(count);
    } catch {}
  }, [user]);

  useEffect(() => {
    if (!user) return;

    // Register push token on mount
    registerForPushNotifications();

    // Initial unread count
    refreshUnreadCount();

    // Poll unread count every 30s
    const interval = setInterval(refreshUnreadCount, 30000);

    // Refresh on app focus
    const appStateListener = AppState.addEventListener('change', (state) => {
      if (state === 'active') refreshUnreadCount();
    });

    // Handle incoming notifications while app is in foreground
    notificationListener.current = Notifications.addNotificationReceivedListener(() => {
      refreshUnreadCount();
    });

    // Handle notification tap (deep link)
    responseListener.current = Notifications.addNotificationResponseReceivedListener((response) => {
      const data = response.notification.request.content.data;
      const route = getNotificationRoute(data);
      if (route) {
        navigation.navigate(route.screen, route.params);
      }
    });

    return () => {
      clearInterval(interval);
      appStateListener.remove();
      if (notificationListener.current) {
        Notifications.removeNotificationSubscription(notificationListener.current);
      }
      if (responseListener.current) {
        Notifications.removeNotificationSubscription(responseListener.current);
      }
    };
  }, [user, refreshUnreadCount, navigation]);

  return { unreadCount, refreshUnreadCount };
}

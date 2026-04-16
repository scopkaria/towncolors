import * as Notifications from 'expo-notifications';
import * as Device from 'expo-device';
import Constants from 'expo-constants';
import { Platform } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { api } from '../api';

const PUSH_TOKEN_KEY = 'push_token';
const NOTIFICATION_SOUND_KEY = 'notification_sound';

// Configure default notification behavior
Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true,
    shouldPlaySound: true,
    shouldSetBadge: true,
    shouldShowBanner: true,
    shouldShowList: true,
  }),
});

// Register for push notifications
export async function registerForPushNotifications(): Promise<string | null> {
  try {
    if (!Device.isDevice) {
      console.log('Push notifications require a physical device');
      return null;
    }

    // Check existing permissions
    const { status: existingStatus } = await Notifications.getPermissionsAsync();
    let finalStatus = existingStatus;

    if (existingStatus !== 'granted') {
      const { status } = await Notifications.requestPermissionsAsync();
      finalStatus = status;
    }

    if (finalStatus !== 'granted') {
      console.log('Push notification permission denied');
      return null;
    }

    // Android notification channels
    if (Platform.OS === 'android') {
      await Notifications.setNotificationChannelAsync('default', {
        name: 'Default',
        importance: Notifications.AndroidImportance.MAX,
        vibrationPattern: [0, 250, 250, 250],
        lightColor: '#f97316',
      });

      await Notifications.setNotificationChannelAsync('messages', {
        name: 'Messages',
        importance: Notifications.AndroidImportance.HIGH,
        vibrationPattern: [0, 250, 250, 250],
        lightColor: '#3b82f6',
      });

      await Notifications.setNotificationChannelAsync('projects', {
        name: 'Projects',
        importance: Notifications.AndroidImportance.HIGH,
        lightColor: '#8b5cf6',
      });

      await Notifications.setNotificationChannelAsync('livechat', {
        name: 'Live Chat',
        importance: Notifications.AndroidImportance.MAX,
        vibrationPattern: [0, 250, 250, 250],
        lightColor: '#f97316',
      });
    }

    const projectId = Constants.expoConfig?.extra?.eas?.projectId;
    const tokenData = await Notifications.getExpoPushTokenAsync({
      projectId: projectId || '398ddbcd-0641-4cd3-a177-895b15190285',
    });
    const token = tokenData.data;

    // Save token locally
    await AsyncStorage.setItem(PUSH_TOKEN_KEY, token);

    // Send token to backend
    try {
      await api('/user/push-token', {
        method: 'POST',
        body: { token, platform: Platform.OS },
      });
    } catch (err) {
      console.log('Failed to send push token to server:', err);
    }

    return token;
  } catch (err) {
    console.log('Push notification setup failed (FCM not configured?):', err);
    return null;
  }
}

// Get saved notification sound preference
export async function getNotificationSound(): Promise<string> {
  const sound = await AsyncStorage.getItem(NOTIFICATION_SOUND_KEY);
  return sound || 'default';
}

// Save notification sound preference
export async function setNotificationSound(sound: string): Promise<void> {
  await AsyncStorage.setItem(NOTIFICATION_SOUND_KEY, sound);
}

// Parse notification data and return the target screen + params
export function getNotificationRoute(data: any): { screen: string; params?: any } | null {
  if (!data) return null;

  const type = data.type || '';
  const id = data.id;

  if (type.includes('message') || type === 'new_message') {
    return { screen: 'Chat', params: { conversationId: data.conversation_id, title: data.title || 'Chat' } };
  }

  if (type.includes('live_chat') || type === 'live_chat_message') {
    return { screen: 'LiveChat' };
  }

  if (type.includes('project') || type === 'project_assigned' || type === 'project_status' || type === 'project_submitted') {
    return { screen: 'ProjectDetail', params: { id: data.project_id || id } };
  }

  if (type.includes('invoice') || type.includes('payment')) {
    return { screen: 'InvoiceDetail', params: { id: data.invoice_id || id } };
  }

  // Fallback to notifications list
  return { screen: 'Notifications' };
}

// Schedule a local notification (for testing)
export async function scheduleLocalNotification(title: string, body: string, data?: any) {
  await Notifications.scheduleNotificationAsync({
    content: {
      title,
      body,
      data: data || {},
      sound: 'default',
    },
    trigger: null, // immediate
  });
}

import React, { useEffect, useState, useCallback } from 'react';
import {
  View, Text, StyleSheet, FlatList, TouchableOpacity,
  RefreshControl, ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useNavigation } from '@react-navigation/native';
import { notificationsApi } from '../api';
import { useTheme } from '../contexts/ThemeContext';
import { getNotificationRoute } from '../services/notifications';
import { spacing, fontSize } from '../theme';

export default function NotificationsScreen() {
  const { colors } = useTheme();
  const navigation = useNavigation<any>();
  const [notifications, setNotifications] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const loadNotifications = useCallback(async () => {
    try {
      const result = await notificationsApi.list();
      setNotifications(result.data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => { loadNotifications(); }, [loadNotifications]);

  const onRefresh = () => { setRefreshing(true); loadNotifications(); };

  async function handleNotificationPress(notification: any) {
    // Mark as read
    if (!notification.read_at) {
      try {
        await notificationsApi.markAsRead(notification.id);
        setNotifications(prev =>
          prev.map(n => n.id === notification.id ? { ...n, read_at: new Date().toISOString() } : n)
        );
      } catch (err) {
        console.error(err);
      }
    }

    // Deep link to the relevant screen
    const route = getNotificationRoute(notification.data);
    if (route && route.screen !== 'Notifications') {
      navigation.navigate(route.screen, route.params);
    }
  }

  async function handleMarkAllAsRead() {
    try {
      await notificationsApi.markAllAsRead();
      setNotifications(prev =>
        prev.map(n => ({ ...n, read_at: n.read_at || new Date().toISOString() }))
      );
    } catch (err) {
      console.error(err);
    }
  }

  function getNotificationIcon(type: string) {
    if (type.includes('Project')) return 'folder';
    if (type.includes('Invoice') || type.includes('Payment')) return 'receipt';
    if (type.includes('Message')) return 'chatbubble';
    if (type.includes('Portfolio')) return 'images';
    return 'notifications';
  }

  function getNotificationMessage(notification: any) {
    const data = notification.data;
    return data?.message || data?.title || notification.type.split('\\').pop() || 'Notification';
  }

  const renderNotification = ({ item }: any) => {
    const isUnread = !item.read_at;
    return (
      <TouchableOpacity
        style={[styles.card, { backgroundColor: isUnread ? colors.primary + '08' : colors.card, borderBottomColor: colors.border }]}
        onPress={() => handleNotificationPress(item)}
        activeOpacity={0.7}
      >
        <View style={[styles.iconWrap, { backgroundColor: isUnread ? colors.primary + '15' : colors.border + '40' }]}>
          <Ionicons
            name={getNotificationIcon(item.type) as any}
            size={20}
            color={isUnread ? colors.primary : colors.textLight}
          />
        </View>
        <View style={styles.content}>
          <Text style={[styles.message, { color: isUnread ? colors.text : colors.textSecondary }, isUnread && styles.messageUnread]} numberOfLines={2}>
            {getNotificationMessage(item)}
          </Text>
          <Text style={[styles.time, { color: colors.textLight }]}>
            {new Date(item.created_at).toLocaleDateString()} · {new Date(item.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
          </Text>
        </View>
        {isUnread && <View style={[styles.dot, { backgroundColor: colors.primary }]} />}
      </TouchableOpacity>
    );
  };

  if (loading) {
    return <View style={[styles.center, { backgroundColor: colors.background }]}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  const unreadCount = notifications.filter(n => !n.read_at).length;

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      {unreadCount > 0 && (
        <TouchableOpacity style={styles.markAllBtn} onPress={handleMarkAllAsRead}>
          <Text style={[styles.markAllText, { color: colors.primary }]}>Mark all as read ({unreadCount})</Text>
        </TouchableOpacity>
      )}

      <FlatList
        data={notifications}
        keyExtractor={item => item.id}
        renderItem={renderNotification}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.primary} />}
        contentContainerStyle={styles.list}
        ListEmptyComponent={
          <View style={styles.emptyWrap}>
            <Ionicons name="notifications-off-outline" size={48} color={colors.textLight} />
            <Text style={[styles.emptyText, { color: colors.textLight }]}>No notifications</Text>
          </View>
        }
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingTop: 80 },
  list: { paddingBottom: 20 },
  markAllBtn: { padding: spacing.md, alignItems: 'flex-end' },
  markAllText: { fontWeight: '700', fontSize: fontSize.sm },
  card: { flexDirection: 'row', alignItems: 'center', padding: spacing.md, borderBottomWidth: 1 },
  iconWrap: { width: 40, height: 40, borderRadius: 20, justifyContent: 'center', alignItems: 'center', marginRight: spacing.md },
  content: { flex: 1 },
  message: { fontSize: fontSize.sm, lineHeight: 20 },
  messageUnread: { fontWeight: '600' },
  time: { fontSize: fontSize.xs, marginTop: 2 },
  dot: { width: 8, height: 8, borderRadius: 4, marginLeft: spacing.sm },
  emptyWrap: { alignItems: 'center', paddingTop: 80 },
  emptyText: { fontSize: fontSize.md, marginTop: spacing.sm },
});

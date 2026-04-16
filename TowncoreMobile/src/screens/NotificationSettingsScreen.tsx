import React, { useEffect, useState } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity, ScrollView, Alert,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useTheme } from '../contexts/ThemeContext';
import {
  getNotificationSound, setNotificationSound, scheduleLocalNotification,
} from '../services/notifications';
import { spacing, fontSize } from '../theme';

const SOUND_OPTIONS = [
  { id: 'default', label: 'Default', icon: 'musical-notes' },
  { id: 'chime', label: 'Chime', icon: 'notifications' },
  { id: 'bell', label: 'Bell', icon: 'notifications-outline' },
  { id: 'ping', label: 'Ping', icon: 'radio-button-on' },
  { id: 'none', label: 'Silent', icon: 'volume-mute' },
];

export default function NotificationSettingsScreen() {
  const { colors } = useTheme();
  const [selectedSound, setSelectedSound] = useState('default');

  useEffect(() => {
    (async () => {
      const sound = await getNotificationSound();
      setSelectedSound(sound);
    })();
  }, []);

  async function handleSoundChange(soundId: string) {
    setSelectedSound(soundId);
    await setNotificationSound(soundId);
  }

  async function handleTestNotification() {
    try {
      await scheduleLocalNotification(
        'Test Notification',
        'This is a test notification from Towncore.',
        { type: 'test' }
      );
    } catch {
      Alert.alert('Error', 'Could not send test notification. Check permissions.');
    }
  }

  return (
    <ScrollView style={[styles.container, { backgroundColor: colors.background }]}>
      {/* Sound Selection */}
      <Text style={[styles.sectionTitle, { color: colors.textSecondary }]}>NOTIFICATION SOUND</Text>
      <View style={[styles.section, { backgroundColor: colors.card, borderColor: colors.border }]}>
        {SOUND_OPTIONS.map((option, index) => (
          <TouchableOpacity
            key={option.id}
            style={[
              styles.row,
              index < SOUND_OPTIONS.length - 1 && { borderBottomWidth: 1, borderBottomColor: colors.border },
            ]}
            onPress={() => handleSoundChange(option.id)}
            activeOpacity={0.7}
          >
            <Ionicons name={option.icon as any} size={20} color={colors.textSecondary} style={styles.rowIcon} />
            <Text style={[styles.rowLabel, { color: colors.text }]}>{option.label}</Text>
            {selectedSound === option.id && (
              <Ionicons name="checkmark-circle" size={22} color={colors.primary} />
            )}
          </TouchableOpacity>
        ))}
      </View>

      {/* Test */}
      <Text style={[styles.sectionTitle, { color: colors.textSecondary }]}>TEST</Text>
      <View style={[styles.section, { backgroundColor: colors.card, borderColor: colors.border }]}>
        <TouchableOpacity style={styles.row} onPress={handleTestNotification} activeOpacity={0.7}>
          <Ionicons name="paper-plane" size={20} color={colors.primary} style={styles.rowIcon} />
          <Text style={[styles.rowLabel, { color: colors.primary }]}>Send Test Notification</Text>
        </TouchableOpacity>
      </View>

      <Text style={[styles.hint, { color: colors.textLight }]}>
        Notification sounds may behave differently across devices. Some custom sounds require a new app build to take effect.
      </Text>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, paddingTop: spacing.md },
  sectionTitle: { fontSize: 12, fontWeight: '700', letterSpacing: 0.8, paddingHorizontal: spacing.lg, marginBottom: 8, marginTop: spacing.lg },
  section: { borderTopWidth: 1, borderBottomWidth: 1 },
  row: { flexDirection: 'row', alignItems: 'center', paddingVertical: 14, paddingHorizontal: spacing.lg },
  rowIcon: { marginRight: spacing.md, width: 24, textAlign: 'center' },
  rowLabel: { flex: 1, fontSize: fontSize.md, fontWeight: '500' },
  hint: { fontSize: fontSize.xs, paddingHorizontal: spacing.lg, paddingTop: spacing.md, lineHeight: 18 },
});

import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import ScreenHeader from './ScreenHeader';

type AllowedRoles = ('admin' | 'client' | 'freelancer')[];

type Props = {
  allowedRoles: AllowedRoles;
  children: React.ReactNode;
  navigation?: any;
};

/**
 * Wraps a screen and blocks access if the user's role is not in allowedRoles.
 * Shows a "no access" placeholder instead of the screen content.
 */
export default function RoleGuard({ allowedRoles, children, navigation }: Props) {
  const { user } = useAuth();
  const { colors } = useTheme();

  if (!user || !allowedRoles.includes(user.role as any)) {
    return (
      <View style={[styles.container, { backgroundColor: colors.background }]}>
        {navigation && <ScreenHeader title="Access Denied" onBack={() => navigation.goBack()} />}
        <View style={styles.content}>
          <Ionicons name="lock-closed-outline" size={48} color={colors.textLight} />
          <Text style={[styles.title, { color: colors.text }]}>Access Restricted</Text>
          <Text style={[styles.message, { color: colors.textLight }]}>
            This feature is not available for your account type.
          </Text>
        </View>
      </View>
    );
  }

  return <>{children}</>;
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  content: {
    flex: 1, justifyContent: 'center', alignItems: 'center',
    paddingHorizontal: 40,
  },
  title: {
    fontSize: 20, fontWeight: '700', marginTop: 16,
  },
  message: {
    fontSize: 14, textAlign: 'center', marginTop: 8, lineHeight: 20,
  },
});

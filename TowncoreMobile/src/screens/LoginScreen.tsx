import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  KeyboardAvoidingView, Platform, ScrollView, Alert, ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import { useBranding } from '../contexts/BrandingContext';
import BrandMark from '../components/BrandMark';
import { spacing, fontSize } from '../theme';

export default function LoginScreen({ navigation }: any) {
  const { login } = useAuth();
  const { colors, isDark, toggleTheme } = useTheme();
  const branding = useBranding();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [showPassword, setShowPassword] = useState(false);

  async function handleLogin() {
    if (!email || !password) {
      Alert.alert('Error', 'Please fill in all fields');
      return;
    }
    setLoading(true);
    try {
      await login(email.trim(), password);
    } catch (err: any) {
      Alert.alert('Login Failed', err.message || 'Invalid credentials');
    } finally {
      setLoading(false);
    }
  }

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={[styles.container, { backgroundColor: colors.background }]}
    >
      <ScrollView
        contentContainerStyle={styles.scroll}
        keyboardShouldPersistTaps="handled"
        showsVerticalScrollIndicator={false}
      >
        <View style={styles.decorWrap} pointerEvents="none">
          <View style={[styles.decorBubbleA, { backgroundColor: `${colors.primary}22` }]} />
          <View style={[styles.decorBubbleB, { backgroundColor: `${colors.primaryDark}1A` }]} />
        </View>

        <View style={styles.toggleRow}>
          <TouchableOpacity
            onPress={toggleTheme}
            style={[styles.toggleButton, { borderColor: colors.border, backgroundColor: colors.card }]}
            activeOpacity={0.7}
            hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
          >
            <Ionicons
              name={isDark ? 'moon' : 'sunny'}
              size={22}
              color={colors.text}
            />
          </TouchableOpacity>
        </View>

        <View style={styles.heroSection}>
          <BrandMark size={58} showName={false} />
          <Text style={[styles.logo, { color: colors.text }]}>{branding.appName}</Text>
          <Text style={[styles.tagline, { color: colors.textSecondary }]}>Project Management Platform</Text>
          <Text style={[styles.subtitle, { color: colors.text }]}>Welcome back, sign in to continue</Text>
        </View>

        <View style={[styles.form, { backgroundColor: colors.card, borderColor: colors.border }]}>
          <View style={styles.formHeader}>
            <Text style={[styles.formTitle, { color: colors.text }]}>Sign In</Text>
            <Text style={[styles.formCaption, { color: colors.textSecondary }]}>Secure access to your workspace</Text>
          </View>

          <View style={[styles.inputGroup, { backgroundColor: colors.inputBg, borderColor: colors.border }]}>
            <View style={styles.inputIcon}>
              <Ionicons name="mail-outline" size={20} color={colors.textLight} />
            </View>
            <TextInput
              style={[styles.input, { color: colors.text }]}
              value={email}
              onChangeText={setEmail}
              placeholder="Email address"
              placeholderTextColor={colors.textLight}
              keyboardType="email-address"
              autoCapitalize="none"
              autoCorrect={false}
            />
          </View>

          <View style={[styles.inputGroup, { backgroundColor: colors.inputBg, borderColor: colors.border }]}>
            <View style={styles.inputIcon}>
              <Ionicons name="lock-closed-outline" size={20} color={colors.textLight} />
            </View>
            <TextInput
              style={[styles.input, { color: colors.text }]}
              value={password}
              onChangeText={setPassword}
              placeholder="Password"
              placeholderTextColor={colors.textLight}
              secureTextEntry={!showPassword}
            />
            <TouchableOpacity
              style={styles.eyeIcon}
              onPress={() => setShowPassword(!showPassword)}
              hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
            >
              <Ionicons name={showPassword ? 'eye-off-outline' : 'eye-outline'} size={20} color={colors.textLight} />
            </TouchableOpacity>
          </View>

          <TouchableOpacity
            style={[styles.button, { backgroundColor: colors.primary, shadowColor: colors.primary }, loading && styles.buttonDisabled]}
            onPress={handleLogin}
            disabled={loading}
            activeOpacity={0.8}
          >
            {loading ? (
              <ActivityIndicator color={colors.text} />
            ) : (
              <View style={styles.buttonContent}>
                <Text style={styles.buttonText}>Sign In</Text>
                <Ionicons name="arrow-forward" size={18} color={colors.text} style={{ marginLeft: 8 }} />
              </View>
            )}
          </TouchableOpacity>
        </View>

        <View style={styles.footer}>
          <TouchableOpacity
            style={styles.registerButton}
            onPress={() => navigation.navigate('Register')}
            activeOpacity={0.7}
          >
            <Text style={[styles.footerText, { color: colors.textSecondary }]}>
              Don't have an account?{' '}
              <Text style={{ color: colors.primary, fontWeight: '700' }}>Create one</Text>
            </Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  scroll: { flexGrow: 1, paddingHorizontal: spacing.lg, paddingBottom: 40 },

  decorWrap: {
    ...StyleSheet.absoluteFillObject,
    overflow: 'hidden',
  },
  decorBubbleA: {
    position: 'absolute',
    width: 240,
    height: 240,
    borderRadius: 120,
    top: -70,
    right: -60,
  },
  decorBubbleB: {
    position: 'absolute',
    width: 180,
    height: 180,
    borderRadius: 90,
    bottom: 60,
    left: -40,
  },

  toggleRow: {
    alignItems: 'flex-end',
    marginTop: Platform.OS === 'ios' ? 52 : 28,
  },
  toggleButton: {
    width: 42,
    height: 42,
    borderRadius: 21,
    borderWidth: 1,
    justifyContent: 'center', alignItems: 'center',
  },

  heroSection: {
    alignItems: 'center',
    paddingTop: spacing.xl,
    paddingBottom: spacing.lg,
  },
  logo: { fontSize: 30, fontWeight: '800', letterSpacing: 0.5, marginTop: spacing.md },
  tagline: { fontSize: fontSize.sm, marginTop: 4 },
  subtitle: { fontSize: fontSize.md, marginTop: spacing.md, fontWeight: '600' },

  form: {
    borderRadius: 22,
    borderWidth: 1,
    paddingHorizontal: spacing.lg,
    paddingTop: spacing.lg,
    paddingBottom: spacing.md,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.1,
    shadowRadius: 18,
    elevation: 8,
  },
  formHeader: { marginBottom: spacing.md },
  formTitle: { fontSize: 22, fontWeight: '800' },
  formCaption: { fontSize: fontSize.sm, marginTop: 4 },
  inputGroup: {
    flexDirection: 'row', alignItems: 'center',
    borderRadius: 14, borderWidth: 1,
    marginBottom: spacing.md,
  },
  inputIcon: { paddingLeft: 14, paddingRight: 4 },
  input: { flex: 1, padding: 14, fontSize: fontSize.md },
  eyeIcon: { paddingRight: 14 },

  button: {
    borderRadius: 14, padding: 16, alignItems: 'center', marginTop: spacing.sm,
    shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.3, shadowRadius: 8, elevation: 4,
  },
  buttonDisabled: { opacity: 0.6 },
  buttonContent: { flexDirection: 'row', alignItems: 'center' },
  buttonText: { color: '#1B2632', fontSize: fontSize.md, fontWeight: '800' },

  footer: { paddingTop: spacing.lg, alignItems: 'center' },
  registerButton: { paddingVertical: spacing.sm },
  footerText: { fontSize: fontSize.sm },
});

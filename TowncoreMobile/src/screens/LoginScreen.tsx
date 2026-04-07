import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  KeyboardAvoidingView, Platform, ScrollView, Alert, ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../contexts/AuthContext';
import { colors, spacing, fontSize } from '../theme';

export default function LoginScreen({ navigation }: any) {
  const { login } = useAuth();
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
      style={styles.container}
    >
      <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
        {/* Hero Header */}
        <View style={styles.heroSection}>
          <View style={styles.logoCircle}>
            <Ionicons name="business" size={32} color="#fff" />
          </View>
          <Text style={styles.logo}>Towncore</Text>
          <Text style={styles.tagline}>Project Management Platform</Text>
          <Text style={styles.subtitle}>Sign in to your account</Text>
        </View>

        {/* Login Form */}
        <View style={styles.form}>
          <View style={styles.inputGroup}>
            <View style={styles.inputIcon}>
              <Ionicons name="mail-outline" size={20} color={colors.textLight} />
            </View>
            <TextInput
              style={styles.input}
              value={email}
              onChangeText={setEmail}
              placeholder="Email address"
              placeholderTextColor={colors.textLight}
              keyboardType="email-address"
              autoCapitalize="none"
              autoCorrect={false}
            />
          </View>

          <View style={styles.inputGroup}>
            <View style={styles.inputIcon}>
              <Ionicons name="lock-closed-outline" size={20} color={colors.textLight} />
            </View>
            <TextInput
              style={styles.input}
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
            style={[styles.button, loading && styles.buttonDisabled]}
            onPress={handleLogin}
            disabled={loading}
            activeOpacity={0.8}
          >
            {loading ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <View style={styles.buttonContent}>
                <Text style={styles.buttonText}>Sign In</Text>
                <Ionicons name="arrow-forward" size={18} color="#fff" style={{ marginLeft: 8 }} />
              </View>
            )}
          </TouchableOpacity>
        </View>

        {/* Footer */}
        <View style={styles.footer}>
          <TouchableOpacity
            style={styles.registerButton}
            onPress={() => navigation.navigate('Register')}
            activeOpacity={0.7}
          >
            <Text style={styles.footerText}>
              Don't have an account?{' '}
              <Text style={styles.footerBold}>Create one</Text>
            </Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.primary },
  scroll: { flexGrow: 1, justifyContent: 'center' },

  // Hero
  heroSection: {
    alignItems: 'center', paddingTop: 60, paddingBottom: spacing.xl,
    paddingHorizontal: spacing.lg,
  },
  logoCircle: {
    width: 64, height: 64, borderRadius: 32,
    backgroundColor: 'rgba(255,255,255,0.2)', justifyContent: 'center', alignItems: 'center',
    marginBottom: spacing.md,
  },
  logo: { fontSize: 34, fontWeight: '800', color: '#fff', letterSpacing: 0.5 },
  tagline: { fontSize: fontSize.sm, color: 'rgba(255,255,255,0.7)', marginTop: 2 },
  subtitle: { fontSize: fontSize.md, color: 'rgba(255,255,255,0.9)', marginTop: spacing.md, fontWeight: '500' },

  // Form
  form: {
    backgroundColor: colors.card, borderTopLeftRadius: 28, borderTopRightRadius: 28,
    paddingHorizontal: spacing.lg, paddingTop: spacing.xl + 4, paddingBottom: spacing.md,
    flex: 1,
    shadowColor: '#000', shadowOffset: { width: 0, height: -4 },
    shadowOpacity: 0.08, shadowRadius: 12, elevation: 10,
  },
  inputGroup: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: colors.inputBg, borderRadius: 14,
    borderWidth: 1, borderColor: colors.border,
    marginBottom: spacing.md,
  },
  inputIcon: { paddingLeft: 14, paddingRight: 4 },
  input: {
    flex: 1, padding: 14, fontSize: fontSize.md, color: colors.text,
  },
  eyeIcon: { paddingRight: 14 },

  button: {
    backgroundColor: colors.primary, borderRadius: 14, padding: 16,
    alignItems: 'center', marginTop: spacing.sm,
    shadowColor: colors.primary, shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3, shadowRadius: 8, elevation: 4,
  },
  buttonDisabled: { opacity: 0.6 },
  buttonContent: { flexDirection: 'row', alignItems: 'center' },
  buttonText: { color: '#fff', fontSize: fontSize.md, fontWeight: '700' },

  // Footer
  footer: { backgroundColor: colors.card, paddingBottom: 40, alignItems: 'center' },
  registerButton: { paddingVertical: spacing.sm },
  footerText: { color: colors.textSecondary, fontSize: fontSize.sm },
  footerBold: { color: colors.primary, fontWeight: '700' },
});

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

export default function RegisterScreen({ navigation }: any) {
  const { register } = useAuth();
  const { colors } = useTheme();
  const branding = useBranding();
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [role, setRole] = useState<'client' | 'freelancer'>('client');
  const [loading, setLoading] = useState(false);

  async function handleRegister() {
    if (!name || !email || !password || !confirmPassword) {
      Alert.alert('Error', 'Please fill in all fields');
      return;
    }
    if (password !== confirmPassword) {
      Alert.alert('Error', 'Passwords do not match');
      return;
    }
    setLoading(true);
    try {
      await register({
        name: name.trim(),
        email: email.trim(),
        password,
        password_confirmation: confirmPassword,
        role,
      });
    } catch (err: any) {
      Alert.alert('Registration Failed', err.message || 'Something went wrong');
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
          <View style={[styles.decorBubbleA, { backgroundColor: `${colors.primary}20` }]} />
          <View style={[styles.decorBubbleB, { backgroundColor: `${colors.primaryDark}18` }]} />
        </View>

        <View style={styles.header}>
          <BrandMark size={54} showName={false} />
          <Text style={[styles.logo, { color: colors.text }]}>{branding.appName}</Text>
          <Text style={[styles.subtitle, { color: colors.textSecondary }]}>Create your account</Text>
        </View>

        <View style={[styles.form, { backgroundColor: colors.card, borderColor: colors.border }]}>
          <Text style={[styles.formTitle, { color: colors.text }]}>Get Started</Text>
          <Text style={[styles.formSubtitle, { color: colors.textSecondary }]}>Set up your role and credentials</Text>

          <Text style={[styles.label, { color: colors.text }]}>I am a</Text>
          <View style={styles.roleRow}>
            <TouchableOpacity
              style={[
                styles.roleButton,
                { borderColor: colors.border, backgroundColor: colors.inputBg },
                role === 'client' && { borderColor: colors.primary, backgroundColor: `${colors.primary}1A` },
              ]}
              onPress={() => setRole('client')}
            >
              <Ionicons name="briefcase-outline" size={18} color={role === 'client' ? colors.primary : colors.textSecondary} />
              <Text style={[styles.roleText, { color: role === 'client' ? colors.primary : colors.textSecondary }]}>Client</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[
                styles.roleButton,
                { borderColor: colors.border, backgroundColor: colors.inputBg },
                role === 'freelancer' && { borderColor: colors.primary, backgroundColor: `${colors.primary}1A` },
              ]}
              onPress={() => setRole('freelancer')}
            >
              <Ionicons name="color-wand-outline" size={18} color={role === 'freelancer' ? colors.primary : colors.textSecondary} />
              <Text style={[styles.roleText, { color: role === 'freelancer' ? colors.primary : colors.textSecondary }]}>Freelancer</Text>
            </TouchableOpacity>
          </View>

          <Text style={[styles.label, { color: colors.text }]}>Full Name</Text>
          <TextInput
            style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.border, color: colors.text }]}
            value={name}
            onChangeText={setName}
            placeholder="John Doe"
            placeholderTextColor={colors.textLight}
            autoCapitalize="words"
          />

          <Text style={[styles.label, { color: colors.text }]}>Email</Text>
          <TextInput
            style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.border, color: colors.text }]}
            value={email}
            onChangeText={setEmail}
            placeholder="you@example.com"
            placeholderTextColor={colors.textLight}
            keyboardType="email-address"
            autoCapitalize="none"
            autoCorrect={false}
          />

          <Text style={[styles.label, { color: colors.text }]}>Password</Text>
          <TextInput
            style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.border, color: colors.text }]}
            value={password}
            onChangeText={setPassword}
            placeholder="••••••••"
            placeholderTextColor={colors.textLight}
            secureTextEntry
          />

          <Text style={[styles.label, { color: colors.text }]}>Confirm Password</Text>
          <TextInput
            style={[styles.input, { backgroundColor: colors.inputBg, borderColor: colors.border, color: colors.text }]}
            value={confirmPassword}
            onChangeText={setConfirmPassword}
            placeholder="••••••••"
            placeholderTextColor={colors.textLight}
            secureTextEntry
          />

          <TouchableOpacity
            style={[styles.button, { backgroundColor: colors.primary }, loading && styles.buttonDisabled]}
            onPress={handleRegister}
            disabled={loading}
          >
            {loading ? (
              <ActivityIndicator color={colors.text} />
            ) : (
              <Text style={styles.buttonText}>Create Account</Text>
            )}
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.linkButton}
            onPress={() => navigation.navigate('Login')}
          >
            <Text style={[styles.linkText, { color: colors.textSecondary }]}>
              Already have an account? <Text style={[styles.linkBold, { color: colors.primary }]}>Sign In</Text>
            </Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  scroll: { flexGrow: 1, justifyContent: 'center', padding: spacing.lg },
  decorWrap: {
    ...StyleSheet.absoluteFillObject,
    overflow: 'hidden',
  },
  decorBubbleA: {
    position: 'absolute',
    width: 210,
    height: 210,
    borderRadius: 105,
    top: -60,
    right: -30,
  },
  decorBubbleB: {
    position: 'absolute',
    width: 170,
    height: 170,
    borderRadius: 85,
    bottom: 40,
    left: -35,
  },
  header: { alignItems: 'center', marginBottom: spacing.xl },
  logo: { fontSize: 30, fontWeight: '800', marginTop: spacing.md },
  subtitle: { fontSize: fontSize.md, marginTop: spacing.xs },
  form: {
    borderRadius: 20,
    borderWidth: 1,
    padding: spacing.lg,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.1,
    shadowRadius: 18,
    elevation: 8,
  },
  formTitle: { fontSize: 22, fontWeight: '800' },
  formSubtitle: { marginTop: 4, fontSize: fontSize.sm },
  label: { fontSize: fontSize.sm, fontWeight: '700', marginBottom: spacing.xs, marginTop: spacing.md },
  input: { borderRadius: 12, padding: 14, fontSize: fontSize.md, borderWidth: 1 },
  roleRow: { flexDirection: 'row', gap: spacing.sm },
  roleButton: { flex: 1, padding: 13, borderRadius: 12, borderWidth: 1.5, alignItems: 'center', flexDirection: 'row', justifyContent: 'center', gap: 8 },
  roleText: { fontSize: fontSize.sm, fontWeight: '700' },
  button: { borderRadius: 12, padding: 16, alignItems: 'center', marginTop: spacing.lg },
  buttonDisabled: { opacity: 0.6 },
  buttonText: { color: '#1B2632', fontSize: fontSize.md, fontWeight: '800' },
  linkButton: { alignItems: 'center', marginTop: spacing.lg },
  linkText: { fontSize: fontSize.sm },
  linkBold: { fontWeight: '700' },
});

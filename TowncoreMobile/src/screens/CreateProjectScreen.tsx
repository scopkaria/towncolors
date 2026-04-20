import React, { useState, useEffect } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  ScrollView, Alert, ActivityIndicator,
} from 'react-native';
import { projectsApi } from '../api';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useTheme } from '../contexts/ThemeContext';
import { spacing, fontSize } from '../theme';
import ScreenHeader from '../components/ScreenHeader';
import { TAB_BAR_TOTAL_HEIGHT } from '../constants/layout';

export default function CreateProjectScreen({ navigation }: any) {
  const { colors } = useTheme();
  const insets = useSafeAreaInsets();
  const [title, setTitle] = useState('');
  const [description, setDescription] = useState('');
  const [categories, setCategories] = useState<any[]>([]);
  const [selectedCategories, setSelectedCategories] = useState<number[]>([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    projectsApi.categories().then(setCategories).catch(() => {});
  }, []);

  function toggleCategory(id: number) {
    setSelectedCategories(prev =>
      prev.includes(id) ? prev.filter(c => c !== id) : [...prev, id]
    );
  }

  async function handleSubmit() {
    if (!title.trim() || !description.trim()) {
      Alert.alert('Error', 'Title and description are required');
      return;
    }
    setLoading(true);
    try {
      await projectsApi.create({
        title: title.trim(),
        description: description.trim(),
        categories: selectedCategories,
      });
      Alert.alert('Success', 'Project submitted successfully');
      navigation.goBack();
    } catch (err: any) {
      Alert.alert('Error', err.message);
    } finally {
      setLoading(false);
    }
  }

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <ScreenHeader title="New Project" onBack={() => navigation.goBack()} />
      <ScrollView style={{ flex: 1 }} keyboardShouldPersistTaps="handled" contentContainerStyle={{ paddingBottom: TAB_BAR_TOTAL_HEIGHT + insets.bottom + 20 }}>
        <View style={styles.form}>
          <Text style={[styles.label, { color: colors.text }]}>Project Title</Text>
          <TextInput
            style={[styles.input, { backgroundColor: colors.card, borderColor: colors.border, color: colors.text }]}
            value={title}
            onChangeText={setTitle}
            placeholder="e.g., Website Redesign"
            placeholderTextColor={colors.textLight}
          />

          <Text style={[styles.label, { color: colors.text }]}>Description</Text>
          <TextInput
            style={[styles.input, styles.textArea, { backgroundColor: colors.card, borderColor: colors.border, color: colors.text }]}
            value={description}
            onChangeText={setDescription}
            placeholder="Describe your project requirements..."
            placeholderTextColor={colors.textLight}
            multiline
            numberOfLines={6}
            textAlignVertical="top"
          />

          {categories.length > 0 && (
            <>
              <Text style={[styles.label, { color: colors.text }]}>Categories</Text>
              <View style={styles.tagRow}>
                {categories.map((cat: any) => (
                  <TouchableOpacity
                    key={cat.id}
                    style={[
                      styles.tag,
                      { borderColor: colors.border, backgroundColor: colors.card },
                      selectedCategories.includes(cat.id) && { borderColor: colors.primary, backgroundColor: colors.primary + '15' },
                    ]}
                    onPress={() => toggleCategory(cat.id)}
                  >
                    <Text style={[
                      styles.tagText,
                      { color: colors.textSecondary },
                      selectedCategories.includes(cat.id) && { color: colors.primary },
                    ]}>
                      {cat.name}
                    </Text>
                  </TouchableOpacity>
                ))}
              </View>
            </>
          )}

          <TouchableOpacity
            style={[styles.button, { backgroundColor: colors.primary }, loading && styles.buttonDisabled]}
            onPress={handleSubmit}
            disabled={loading}
          >
            {loading ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <Text style={styles.buttonText}>Submit Project</Text>
            )}
          </TouchableOpacity>
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  form: { padding: spacing.lg },
  label: { fontSize: fontSize.sm, fontWeight: '600', marginBottom: spacing.xs, marginTop: spacing.md },
  input: { borderRadius: 12, padding: 14, fontSize: fontSize.md, borderWidth: 1 },
  textArea: { minHeight: 140 },
  tagRow: { flexDirection: 'row', flexWrap: 'wrap', gap: spacing.xs },
  tag: { paddingHorizontal: 14, paddingVertical: 8, borderRadius: 20, borderWidth: 1.5 },
  tagText: { fontSize: fontSize.sm, fontWeight: '600' },
  button: { borderRadius: 14, padding: 16, alignItems: 'center', marginTop: spacing.xl },
  buttonDisabled: { opacity: 0.6 },
  buttonText: { color: '#fff', fontSize: fontSize.md, fontWeight: '700' },
});

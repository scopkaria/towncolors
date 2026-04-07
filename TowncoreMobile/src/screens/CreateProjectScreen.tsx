import React, { useState, useEffect } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  ScrollView, Alert, ActivityIndicator,
} from 'react-native';
import { projectsApi } from '../api';
import { colors, spacing, fontSize } from '../theme';

export default function CreateProjectScreen({ navigation }: any) {
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
    <ScrollView style={styles.container} keyboardShouldPersistTaps="handled">
      <View style={styles.form}>
        <Text style={styles.label}>Project Title</Text>
        <TextInput
          style={styles.input}
          value={title}
          onChangeText={setTitle}
          placeholder="e.g., Website Redesign"
        />

        <Text style={styles.label}>Description</Text>
        <TextInput
          style={[styles.input, styles.textArea]}
          value={description}
          onChangeText={setDescription}
          placeholder="Describe your project requirements..."
          multiline
          numberOfLines={6}
          textAlignVertical="top"
        />

        {categories.length > 0 && (
          <>
            <Text style={styles.label}>Categories</Text>
            <View style={styles.tagRow}>
              {categories.map((cat: any) => (
                <TouchableOpacity
                  key={cat.id}
                  style={[styles.tag, selectedCategories.includes(cat.id) && styles.tagActive]}
                  onPress={() => toggleCategory(cat.id)}
                >
                  <Text style={[styles.tagText, selectedCategories.includes(cat.id) && styles.tagTextActive]}>
                    {cat.name}
                  </Text>
                </TouchableOpacity>
              ))}
            </View>
          </>
        )}

        <TouchableOpacity
          style={[styles.button, loading && styles.buttonDisabled]}
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
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  form: { padding: spacing.lg },
  label: { fontSize: fontSize.sm, fontWeight: '600', color: colors.text, marginBottom: spacing.xs, marginTop: spacing.md },
  input: { backgroundColor: colors.card, borderRadius: 12, padding: 14, fontSize: fontSize.md, borderWidth: 1, borderColor: colors.border },
  textArea: { minHeight: 140 },
  tagRow: { flexDirection: 'row', flexWrap: 'wrap', gap: spacing.xs },
  tag: { paddingHorizontal: 14, paddingVertical: 8, borderRadius: 20, borderWidth: 1.5, borderColor: colors.border, backgroundColor: colors.card },
  tagActive: { borderColor: colors.primary, backgroundColor: colors.primary + '15' },
  tagText: { fontSize: fontSize.sm, color: colors.textSecondary, fontWeight: '600' },
  tagTextActive: { color: colors.primary },
  button: { backgroundColor: colors.primary, borderRadius: 12, padding: 16, alignItems: 'center', marginTop: spacing.xl },
  buttonDisabled: { opacity: 0.6 },
  buttonText: { color: colors.white, fontSize: fontSize.md, fontWeight: '700' },
});

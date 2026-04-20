import React, { useEffect, useState } from 'react';
import {
  View, Text, StyleSheet, ScrollView, Image, ActivityIndicator,
  Dimensions, useWindowDimensions,
} from 'react-native';
import { blogApi } from '../api';
import { MEDIA_BASE_URL } from '../config';
import { useTheme } from '../contexts/ThemeContext';
import { spacing, fontSize } from '../theme';
import ScreenHeader from '../components/ScreenHeader';

export default function BlogPostScreen({ route, navigation }: any) {
  const { colors } = useTheme();
  const { slug } = route.params;
  const [post, setPost] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    blogApi.get(slug).then(setPost).catch(console.error).finally(() => setLoading(false));
  }, [slug]);

  if (loading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  if (!post) {
    return <View style={styles.center}><Text>Post not found</Text></View>;
  }

  function getImageUrl(path: string) {
    if (!path) return null;
    return `${MEDIA_BASE_URL}/storage/${path}`;
  }

  const imageUrl = getImageUrl(post.featured_image);

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
    <ScreenHeader title="Article" onBack={() => navigation.goBack()} />
    <ScrollView style={{ flex: 1 }}>
      {imageUrl && (
        <Image source={{ uri: imageUrl }} style={[styles.image, { backgroundColor: colors.inputBg }]} resizeMode="cover" />
      )}
      <View style={styles.content}>
        <Text style={[styles.title, { color: colors.text }]}>{post.title}</Text>
        <Text style={[styles.date, { color: colors.textLight }]}>
          {new Date(post.published_at).toLocaleDateString('en-US', {
            weekday: 'long', month: 'long', day: 'numeric', year: 'numeric',
          })}
        </Text>
        <Text style={[styles.body, { color: colors.text }]}>{post.content?.replace(/<[^>]*>/g, '') || ''}</Text>
      </View>
    </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  image: { width: '100%', height: 250 },
  content: { padding: spacing.lg },
  title: { fontSize: fontSize.xxl, fontWeight: '800', lineHeight: 36 },
  date: { fontSize: fontSize.sm, marginTop: spacing.sm, marginBottom: spacing.lg },
  body: { fontSize: fontSize.md, lineHeight: 26 },
});

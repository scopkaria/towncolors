import React, { useEffect, useState } from 'react';
import {
  View, Text, StyleSheet, ScrollView, Image, ActivityIndicator,
  Dimensions, useWindowDimensions,
} from 'react-native';
import { blogApi } from '../api';
import API_BASE_URL from '../config';
import { colors, spacing, fontSize } from '../theme';

export default function BlogPostScreen({ route }: any) {
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
    const baseUrl = API_BASE_URL.replace('/api', '');
    return `${baseUrl}/storage/${path}`;
  }

  const imageUrl = getImageUrl(post.featured_image);

  return (
    <ScrollView style={styles.container}>
      {imageUrl && (
        <Image source={{ uri: imageUrl }} style={styles.image} resizeMode="cover" />
      )}
      <View style={styles.content}>
        <Text style={styles.title}>{post.title}</Text>
        <Text style={styles.date}>
          {new Date(post.published_at).toLocaleDateString('en-US', {
            weekday: 'long', month: 'long', day: 'numeric', year: 'numeric',
          })}
        </Text>
        <Text style={styles.body}>{post.content?.replace(/<[^>]*>/g, '') || ''}</Text>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  image: { width: '100%', height: 250, backgroundColor: colors.inputBg },
  content: { padding: spacing.lg },
  title: { fontSize: fontSize.xxl, fontWeight: '800', color: colors.text, lineHeight: 36 },
  date: { fontSize: fontSize.sm, color: colors.textLight, marginTop: spacing.sm, marginBottom: spacing.lg },
  body: { fontSize: fontSize.md, color: colors.text, lineHeight: 26 },
});

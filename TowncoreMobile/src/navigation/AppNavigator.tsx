import React, { useEffect, useRef } from 'react';
import { ActivityIndicator, View, Platform, Animated, Text, StyleSheet, Image, Dimensions, TouchableOpacity } from 'react-native';
import { NavigationContainer, createNavigationContainerRef } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';

// Auth Screens
import LoginScreen from '../screens/LoginScreen';
import RegisterScreen from '../screens/RegisterScreen';

// Main Screens
import DashboardScreen from '../screens/DashboardScreen';
import ProjectsScreen from '../screens/ProjectsScreen';
import ProjectDetailScreen from '../screens/ProjectDetailScreen';
import CreateProjectScreen from '../screens/CreateProjectScreen';
import ConversationsScreen from '../screens/ConversationsScreen';
import ChatScreen from '../screens/ChatScreen';
import InvoicesScreen from '../screens/InvoicesScreen';
import InvoiceDetailScreen from '../screens/InvoiceDetailScreen';
import PortfolioScreen from '../screens/PortfolioScreen';
import BlogScreen from '../screens/BlogScreen';
import BlogPostScreen from '../screens/BlogPostScreen';
import NotificationsScreen from '../screens/NotificationsScreen';
import ProfileScreen from '../screens/ProfileScreen';
import LiveChatScreen from '../screens/LiveChatScreen';
import NotificationSettingsScreen from '../screens/NotificationSettingsScreen';
import MenuScreen from '../screens/MenuScreen';
import SubscriptionScreen from '../screens/SubscriptionScreen';
import FilesScreen from '../screens/FilesScreen';
import ChecklistScreen from '../screens/ChecklistScreen';

export const navigationRef = createNavigationContainerRef<any>();

const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

// Auth Stack
function AuthStack() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }}>
      <Stack.Screen name="Login" component={LoginScreen} />
      <Stack.Screen name="Register" component={RegisterScreen} />
    </Stack.Navigator>
  );
}

// Projects Stack
function ProjectsStack() {
  const { colors } = useTheme();
  const screenOptions = {
    headerStyle: { backgroundColor: colors.card },
    headerTintColor: colors.text,
    headerTitleStyle: { fontWeight: '700' as const },
    headerShadowVisible: false,
  };
  return (
    <Stack.Navigator screenOptions={screenOptions}>
      <Stack.Screen name="ProjectsList" component={ProjectsScreen} options={{ headerShown: false }} />
      <Stack.Screen name="ProjectDetail" component={ProjectDetailScreen} options={{ title: 'Project Details' }} />
      <Stack.Screen name="CreateProject" component={CreateProjectScreen} options={{ title: 'New Project' }} />
    </Stack.Navigator>
  );
}

// Messages Stack
function MessagesStack() {
  const { colors } = useTheme();
  const screenOptions = {
    headerStyle: { backgroundColor: colors.card },
    headerTintColor: colors.text,
    headerTitleStyle: { fontWeight: '700' as const },
    headerShadowVisible: false,
  };
  return (
    <Stack.Navigator screenOptions={screenOptions}>
      <Stack.Screen name="Conversations" component={ConversationsScreen} options={{ title: 'Messages' }} />
      <Stack.Screen
        name="Chat"
        component={ChatScreen}
        options={({ route }: any) => ({ title: route.params?.title || 'Chat' })}
      />
    </Stack.Navigator>
  );
}

// Custom center tab button (raised Home)
function CenterTabButton({ onPress, colors }: { onPress: () => void; colors: any }) {
  return (
    <TouchableOpacity style={centerBtnStyles.wrapper} onPress={onPress} activeOpacity={0.8}>
      <View style={[centerBtnStyles.circle, { backgroundColor: colors.primary }]}>
        <Ionicons name="home" size={26} color="#fff" />
      </View>
    </TouchableOpacity>
  );
}

const centerBtnStyles = StyleSheet.create({
  wrapper: { top: -22, justifyContent: 'center', alignItems: 'center' },
  circle: {
    width: 58, height: 58, borderRadius: 29, justifyContent: 'center', alignItems: 'center',
    shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.25, shadowRadius: 8, elevation: 8,
    borderWidth: 4, borderColor: '#fff',
  },
});

// Main Tab Navigator — 5 tabs with center home
function MainTabs() {
  const { colors } = useTheme();
  const insets = useSafeAreaInsets();
  const bottomInset = Math.max(insets.bottom, 8);

  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        headerShown: false,
        tabBarActiveTintColor: colors.primary,
        tabBarInactiveTintColor: colors.textLight,
        tabBarStyle: {
          backgroundColor: colors.card,
          borderTopColor: 'transparent',
          borderTopWidth: 0,
          position: 'absolute',
          left: 12,
          right: 12,
          bottom: bottomInset,
          borderRadius: 22,
          paddingTop: 6,
          paddingBottom: 6,
          height: 64,
          shadowColor: '#000',
          shadowOffset: { width: 0, height: 6 },
          shadowOpacity: 0.15,
          shadowRadius: 16,
          elevation: 12,
        },
        tabBarLabelStyle: { fontSize: 10, fontWeight: '700', marginTop: -2 },
        tabBarItemStyle: { borderRadius: 12, marginHorizontal: 1 },
        tabBarIcon: ({ focused, color }) => {
          let iconName: any;
          const size = 22;
          switch (route.name) {
            case 'Projects': iconName = focused ? 'folder' : 'folder-outline'; break;
            case 'Messages': iconName = focused ? 'chatbubbles' : 'chatbubbles-outline'; break;
            case 'Home': iconName = 'home'; break;
            case 'LiveChat': iconName = focused ? 'chatbubble-ellipses' : 'chatbubble-ellipses-outline'; break;
            case 'Menu': iconName = focused ? 'grid' : 'grid-outline'; break;
          }
          return <Ionicons name={iconName} size={size} color={color} />;
        },
      })}
    >
      <Tab.Screen name="Projects" component={ProjectsStack} />
      <Tab.Screen name="Messages" component={MessagesStack} />
      <Tab.Screen
        name="Home"
        component={DashboardScreen}
        options={({ navigation: nav }) => ({
          tabBarButton: (props) => (
            <CenterTabButton onPress={() => nav.navigate('Home')} colors={colors} />
          ),
        })}
      />
      <Tab.Screen name="LiveChat" component={LiveChatScreen} options={{ tabBarLabel: 'Chat' }} />
      <Tab.Screen name="Menu" component={MenuScreen} />
    </Tab.Navigator>
  );
}

// App Main — tabs + overlay screens accessible from drawer
function AppMain() {
  const { colors } = useTheme();
  const screenOptions = {
    headerStyle: { backgroundColor: colors.card },
    headerTintColor: colors.text,
    headerTitleStyle: { fontWeight: '700' as const },
    headerShadowVisible: false,
  };
  return (
    <Stack.Navigator>
      <Stack.Screen name="Tabs" component={MainTabs} options={{ headerShown: false }} />
      <Stack.Screen name="Invoices" component={InvoicesScreen} options={{ ...screenOptions, title: 'Invoices' }} />
      <Stack.Screen name="InvoiceDetail" component={InvoiceDetailScreen} options={{ ...screenOptions, title: 'Invoice Details' }} />
      <Stack.Screen name="Portfolio" component={PortfolioScreen} options={{ ...screenOptions, title: 'Portfolio' }} />
      <Stack.Screen name="Blog" component={BlogScreen} options={{ ...screenOptions, title: 'Blog' }} />
      <Stack.Screen name="BlogPost" component={BlogPostScreen} options={{ ...screenOptions, title: 'Article' }} />
      <Stack.Screen name="Notifications" component={NotificationsScreen} options={{ ...screenOptions, title: 'Notifications' }} />
      <Stack.Screen name="Profile" component={ProfileScreen} options={{ ...screenOptions, title: 'Profile' }} />
      <Stack.Screen name="NotificationSettings" component={NotificationSettingsScreen} options={{ ...screenOptions, title: 'Notification Settings' }} />
      <Stack.Screen name="Subscription" component={SubscriptionScreen} options={{ ...screenOptions, headerShown: false }} />
      <Stack.Screen name="Files" component={FilesScreen} options={{ ...screenOptions, headerShown: false }} />
      <Stack.Screen name="Checklist" component={ChecklistScreen} options={{ ...screenOptions, headerShown: false }} />
    </Stack.Navigator>
  );
}

// Branded Splash / Preloader
function SplashScreen() {
  const { colors } = useTheme();
  const logoScale = useRef(new Animated.Value(0.3)).current;
  const logoOpacity = useRef(new Animated.Value(0)).current;
  const textOpacity = useRef(new Animated.Value(0)).current;
  const spinValue = useRef(new Animated.Value(0)).current;
  const barWidth = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    // Logo entrance
    Animated.sequence([
      Animated.parallel([
        Animated.spring(logoScale, { toValue: 1, friction: 6, tension: 40, useNativeDriver: true }),
        Animated.timing(logoOpacity, { toValue: 1, duration: 500, useNativeDriver: true }),
      ]),
      Animated.timing(textOpacity, { toValue: 1, duration: 350, useNativeDriver: true }),
    ]).start();

    // Spinning loader
    Animated.loop(
      Animated.timing(spinValue, { toValue: 1, duration: 1200, useNativeDriver: true })
    ).start();

    // Progress bar
    Animated.timing(barWidth, { toValue: 1, duration: 2500, useNativeDriver: false }).start();
  }, []);

  const spin = spinValue.interpolate({ inputRange: [0, 1], outputRange: ['0deg', '360deg'] });

  return (
    <View style={[splashStyles.container, { backgroundColor: colors.primary }]}>
      <View style={splashStyles.topGlow} />
      <Animated.Image
        source={require('../../assets/icon.png')}
        style={[
          splashStyles.logo,
          { opacity: logoOpacity, transform: [{ scale: logoScale }] },
        ]}
        resizeMode="contain"
      />
      <Animated.Text style={[splashStyles.title, { opacity: textOpacity }]}>
        TOWNCORE
      </Animated.Text>
      <Animated.Text style={[splashStyles.tagline, { opacity: textOpacity }]}>
        Project Management
      </Animated.Text>
      <View style={splashStyles.loaderArea}>
        <Animated.View style={{ transform: [{ rotate: spin }] }}>
          <Ionicons name="sync-outline" size={22} color="rgba(255,255,255,0.85)" />
        </Animated.View>
        <View style={splashStyles.barTrack}>
          <Animated.View style={[splashStyles.barFill, {
            width: barWidth.interpolate({ inputRange: [0, 1], outputRange: ['0%', '100%'] }),
          }]} />
        </View>
      </View>
    </View>
  );
}

const splashStyles = StyleSheet.create({
  container: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  topGlow: {
    position: 'absolute', top: -80, width: 300, height: 300, borderRadius: 150,
    backgroundColor: 'rgba(255,255,255,0.08)',
  },
  logo: { width: 110, height: 110, borderRadius: 28, marginBottom: 20 },
  title: { fontSize: 32, fontWeight: '900', color: '#fff', letterSpacing: 6 },
  tagline: { fontSize: 13, fontWeight: '500', color: 'rgba(255,255,255,0.7)', letterSpacing: 2, marginTop: 6, textTransform: 'uppercase' },
  loaderArea: { alignItems: 'center', marginTop: 48, gap: 16 },
  barTrack: { width: 140, height: 3, borderRadius: 2, backgroundColor: 'rgba(255,255,255,0.2)', overflow: 'hidden' },
  barFill: { height: '100%', borderRadius: 2, backgroundColor: 'rgba(255,255,255,0.8)' },
});

// Root Navigator
export default function AppNavigator() {
  const { user, isLoading } = useAuth();
  const { colors } = useTheme();

  if (isLoading) {
    return <SplashScreen />;
  }

  return (
    <NavigationContainer ref={navigationRef}>
      {user ? <AppMain /> : <AuthStack />}
    </NavigationContainer>
  );
}

import React, { useEffect, useRef } from 'react';
import { ActivityIndicator, View, Platform, Animated, Text, StyleSheet, Image, Dimensions, TouchableOpacity } from 'react-native';
import { NavigationContainer, createNavigationContainerRef } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import RoleGuard from '../components/RoleGuard';

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
import UsersScreen from '../screens/UsersScreen';
import BlogManageScreen from '../screens/BlogManageScreen';
import PortfolioManageScreen from '../screens/PortfolioManageScreen';

export const navigationRef = createNavigationContainerRef<any>();

const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

// ── Role-guarded screen wrappers ──────────────────────────

// Admin-only: LiveChat agent console
const GuardedLiveChatScreen = (props: any) => (
  <RoleGuard allowedRoles={['admin']} navigation={props.navigation}>
    <LiveChatScreen {...props} />
  </RoleGuard>
);

// Client-only: Files
const GuardedFilesScreen = (props: any) => (
  <RoleGuard allowedRoles={['client']} navigation={props.navigation}>
    <FilesScreen {...props} />
  </RoleGuard>
);

// Admin/Client: Invoices, Subscription
const GuardedPortfolioScreen = (props: any) => (
  <RoleGuard allowedRoles={['admin', 'freelancer']} navigation={props.navigation}>
    <PortfolioScreen {...props} />
  </RoleGuard>
);

const GuardedBlogScreen = (props: any) => (
  <RoleGuard allowedRoles={['admin']} navigation={props.navigation}>
    <BlogScreen {...props} />
  </RoleGuard>
);

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
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }}>
      <Stack.Screen name="ProjectsList" component={ProjectsScreen} />
      <Stack.Screen name="ProjectDetail" component={ProjectDetailScreen} />
      <Stack.Screen name="CreateProject" component={CreateProjectScreen} />
    </Stack.Navigator>
  );
}

// Messages Stack
function MessagesStack() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }}>
      <Stack.Screen name="Conversations" component={ConversationsScreen} />
      <Stack.Screen name="Chat" component={ChatScreen} />
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

// Main Tab Navigator — role-based tabs
function MainTabs() {
  const { user } = useAuth();
  const { colors } = useTheme();
  const insets = useSafeAreaInsets();
  const bottomInset = Math.max(insets.bottom, 8);
  const role = user?.role || 'client';

  const tabBarStyle = {
    backgroundColor: colors.card,
    borderTopColor: 'transparent',
    borderTopWidth: 0,
    position: 'absolute' as const,
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
  };

  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        headerShown: false,
        tabBarActiveTintColor: colors.primary,
        tabBarInactiveTintColor: colors.textLight,
        tabBarStyle,
        tabBarLabelStyle: { fontSize: 10, fontWeight: '700', marginTop: -2 },
        tabBarItemStyle: { borderRadius: 12, marginHorizontal: 1 },
        tabBarIcon: ({ focused, color }) => {
          let iconName: any;
          const size = 22;
          switch (route.name) {
            case 'Projects':   iconName = focused ? 'folder' : 'folder-outline'; break;
            case 'Messages':   iconName = focused ? 'chatbubbles' : 'chatbubbles-outline'; break;
            case 'Home':       iconName = 'home'; break;
            case 'LiveChat':   iconName = focused ? 'headset' : 'headset-outline'; break;
            case 'MyPlan':     iconName = focused ? 'shield-checkmark' : 'shield-checkmark-outline'; break;
            case 'Tasks':      iconName = focused ? 'checkbox' : 'checkbox-outline'; break;
            case 'Menu':       iconName = focused ? 'grid' : 'grid-outline'; break;
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
          tabBarButton: () => (
            <CenterTabButton onPress={() => nav.navigate('Home')} colors={colors} />
          ),
        })}
      />
      {/* 4th tab: role-specific */}
      {role === 'admin' && (
        <Tab.Screen
          name="LiveChat"
          component={LiveChatScreen}
          options={{ tabBarLabel: 'Support' }}
        />
      )}
      {role === 'client' && (
        <Tab.Screen
          name="MyPlan"
          component={SubscriptionScreen}
          options={{ tabBarLabel: 'My Plan' }}
        />
      )}
      {role === 'freelancer' && (
        <Tab.Screen
          name="Tasks"
          component={ChecklistScreen}
          options={{ tabBarLabel: 'Tasks' }}
        />
      )}
      <Tab.Screen name="Menu" component={MenuScreen} />
    </Tab.Navigator>
  );
}

// App Main — tabs + overlay screens
function AppMain() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }}>
      <Stack.Screen name="Tabs" component={MainTabs} />
      <Stack.Screen name="Invoices" component={InvoicesScreen} />
      <Stack.Screen name="InvoiceDetail" component={InvoiceDetailScreen} />
      <Stack.Screen name="Portfolio" component={GuardedPortfolioScreen} />
      <Stack.Screen name="Blog" component={GuardedBlogScreen} />
      <Stack.Screen name="BlogPost" component={BlogPostScreen} />
      <Stack.Screen name="Notifications" component={NotificationsScreen} />
      <Stack.Screen name="Profile" component={ProfileScreen} />
      <Stack.Screen name="NotificationSettings" component={NotificationSettingsScreen} />
      <Stack.Screen name="Subscription" component={SubscriptionScreen} />
      <Stack.Screen name="Files" component={GuardedFilesScreen} />
      <Stack.Screen name="Checklist" component={ChecklistScreen} />
      <Stack.Screen name="LiveChat" component={LiveChatScreen} />
      <Stack.Screen name="Users" component={UsersScreen} />
      <Stack.Screen name="BlogManage" component={BlogManageScreen} />
      <Stack.Screen name="PortfolioManage" component={PortfolioManageScreen} />
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

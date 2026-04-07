import React, { createContext, useContext, useEffect, useState } from 'react';
import * as SecureStore from 'expo-secure-store';
import { authApi, setToken, removeToken } from '../api';

type User = {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'client' | 'freelancer';
};

type AuthContextType = {
  user: User | null;
  isLoading: boolean;
  login: (email: string, password: string) => Promise<void>;
  register: (data: { name: string; email: string; password: string; password_confirmation: string; role: string }) => Promise<void>;
  logout: () => Promise<void>;
};

const AuthContext = createContext<AuthContextType>({} as AuthContextType);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    loadUser();
  }, []);

  async function loadUser() {
    try {
      const token = await SecureStore.getItemAsync('auth_token');
      if (token) {
        const userData = await authApi.getUser();
        setUser(userData);
      }
    } catch {
      await removeToken();
    } finally {
      setIsLoading(false);
    }
  }

  async function login(email: string, password: string) {
    const response = await authApi.login(email, password);
    await setToken(response.token);
    setUser(response.user);
  }

  async function register(data: { name: string; email: string; password: string; password_confirmation: string; role: string }) {
    const response = await authApi.register(data);
    await setToken(response.token);
    setUser(response.user);
  }

  async function logout() {
    try {
      await authApi.logout();
    } catch {}
    await removeToken();
    setUser(null);
  }

  return (
    <AuthContext.Provider value={{ user, isLoading, login, register, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  return useContext(AuthContext);
}

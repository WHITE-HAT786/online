import React, { createContext, useContext, useEffect, useState, ReactNode } from "react";
import { storage } from "@/src/utils/storage";
import { apiGet, apiPost, AUTH_KEY } from "@/src/api";

export type AuthUser = {
  user_id: string;
  name: string;
  email: string;
  picture?: string | null;
  role: string;
  created_at: string;
};

type AuthCtx = {
  user: AuthUser | null;
  loading: boolean;
  loginEmail: (email: string, password: string) => Promise<void>;
  registerEmail: (name: string, email: string, password: string) => Promise<void>;
  loginGoogleSession: (sessionId: string) => Promise<void>;
  logout: () => Promise<void>;
  refresh: () => Promise<void>;
};

const AuthContext = createContext<AuthCtx | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<AuthUser | null>(null);
  const [loading, setLoading] = useState(true);

  const persist = async (token: string, u: AuthUser) => {
    await storage.secureSet(AUTH_KEY, token);
    setUser(u);
  };

  const refresh = async () => {
    try {
      const token = await storage.secureGet<string>(AUTH_KEY, "");
      if (!token) {
        setUser(null);
        return;
      }
      const me = await apiGet<AuthUser>("/auth/me");
      setUser(me);
    } catch {
      await storage.secureRemove(AUTH_KEY);
      setUser(null);
    }
  };

  useEffect(() => {
    (async () => {
      await refresh();
      setLoading(false);
    })();
  }, []);

  const loginEmail = async (email: string, password: string) => {
    const res = await apiPost<{ token: string; user: AuthUser }>(
      "/auth/login",
      { email, password },
      false,
    );
    await persist(res.token, res.user);
  };

  const registerEmail = async (name: string, email: string, password: string) => {
    const res = await apiPost<{ token: string; user: AuthUser }>(
      "/auth/register",
      { name, email, password },
      false,
    );
    await persist(res.token, res.user);
  };

  const loginGoogleSession = async (session_id: string) => {
    const res = await apiPost<{ token: string; user: AuthUser }>(
      "/auth/google/session",
      { session_id },
      false,
    );
    await persist(res.token, res.user);
  };

  const logout = async () => {
    try {
      await apiPost("/auth/logout", {});
    } catch {}
    await storage.secureRemove(AUTH_KEY);
    setUser(null);
  };

  return (
    <AuthContext.Provider
      value={{ user, loading, loginEmail, registerEmail, loginGoogleSession, logout, refresh }}
    >
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error("useAuth must be used inside AuthProvider");
  return ctx;
}

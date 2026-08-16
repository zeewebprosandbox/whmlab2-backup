"use client";

import React, { createContext, useContext, useState, useEffect } from "react";

interface User {
  id: number;
  name: string;
  email: string;
  balance: string;
  supportPin: string;
}

interface AuthContextType {
  user: User | null;
  isAuthenticated: boolean;
  login: (email: string) => void;
  logout: () => void;
}

const AuthContext = createContext<AuthContextType>({
  user: null,
  isAuthenticated: false,
  login: () => {},
  logout: () => {},
});

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);

  useEffect(() => {
    // Load persisted auth session
    const savedUser = localStorage.getItem("whm_user_session");
    if (savedUser) {
      try {
        setUser(JSON.parse(savedUser));
      } catch (e) {
        console.error("Failed to restore user session", e);
      }
    } else {
      // Default initial mock session
      const defaultUser = {
        id: 1,
        name: "John Doe",
        email: "john@example.com",
        balance: "$145.00",
        supportPin: "849-201",
      };
      setUser(defaultUser);
      localStorage.setItem("whm_user_session", JSON.stringify(defaultUser));
    }
  }, []);

  const login = (email: string) => {
    const newUser = {
      id: 1,
      name: email.split("@")[0] || "Operator User",
      email: email,
      balance: "$145.00",
      supportPin: "849-201",
    };
    setUser(newUser);
    localStorage.setItem("whm_user_session", JSON.stringify(newUser));
    localStorage.setItem("whm_auth_token", "demo_jwt_token_active");
  };

  const logout = () => {
    setUser(null);
    localStorage.removeItem("whm_user_session");
    localStorage.removeItem("whm_auth_token");
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        isAuthenticated: !!user,
        login,
        logout,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);

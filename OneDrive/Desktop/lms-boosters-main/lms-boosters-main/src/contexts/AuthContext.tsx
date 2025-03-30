// src/contexts/AuthContext.tsx
import React, { createContext, useState, useContext, useEffect, ReactNode } from 'react';
import { useNavigate } from 'react-router-dom';
import { toast } from 'sonner';
import api from '@/lib/api';

interface User {
  id: string;
  name: string;
  email: string;
  role: string;
}

interface AuthCredentials {
  email: string;
  password: string;
}

interface RegisterData {
  name: string;
  email: string;
  password: string;
  role?: string;
}

interface AuthContextType {
  user: User | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  login: (credentials: AuthCredentials) => Promise<void>;
  register: (data: RegisterData) => Promise<void>;
  logout: () => void;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const navigate = useNavigate();

  // Verificar si el usuario está autenticado al cargar la aplicación
  useEffect(() => {
    const checkAuth = async () => {
      setIsLoading(true);
      try {
        const token = localStorage.getItem('lms_token');
        
        if (token) {
          // Verificar token con el servidor
          const response = await api.get('/auth/me');
          
          if (response.data.success) {
            const userData = response.data.data;
            setUser({
              id: userData._id,
              name: userData.name,
              email: userData.email,
              role: userData.role
            });
          } else {
            // Si hay problemas con el token, hacer logout
            handleLogout();
          }
        }
      } catch (error) {
        console.error('Error al verificar la autenticación:', error);
        handleLogout();
      } finally {
        setIsLoading(false);
      }
    };

    checkAuth();
  }, []);

  // Función para iniciar sesión
  const login = async (credentials: AuthCredentials) => {
    setIsLoading(true);
    try {
      const response = await api.post('/auth/login', credentials);
      
      if (response.data.success) {
        const { user, token } = response.data;
        
        // Guardar token en localStorage
        localStorage.setItem('lms_token', token);
        
        // Actualizar estado
        setUser({
          id: user.id,
          name: user.name,
          email: user.email,
          role: user.role
        });
        
        toast.success('Inicio de sesión exitoso');
        navigate('/dashboard');
      }
    } catch (error: any) {
      console.error('Error al iniciar sesión:', error);
      
      // Mostrar mensaje de error
      const errorMessage = error.response?.data?.message || 'Error al iniciar sesión';
      toast.error(errorMessage);
      
      throw error;
    } finally {
      setIsLoading(false);
    }
  };

  // Función para registrarse
  const register = async (data: RegisterData) => {
    setIsLoading(true);
    try {
      const response = await api.post('/auth/register', data);
      
      if (response.data.success) {
        const { user, token } = response.data;
        
        // Guardar token en localStorage
        localStorage.setItem('lms_token', token);
        
        // Actualizar estado
        setUser({
          id: user.id,
          name: user.name,
          email: user.email,
          role: user.role
        });
        
        toast.success('Registro exitoso');
        navigate('/dashboard');
      }
    } catch (error: any) {
      console.error('Error al registrarse:', error);
      
      // Mostrar mensaje de error
      const errorMessage = error.response?.data?.message || 'Error al registrarse';
      toast.error(errorMessage);
      
      throw error;
    } finally {
      setIsLoading(false);
    }
  };

  // Función para cerrar sesión
  const handleLogout = () => {
    localStorage.removeItem('lms_token');
    setUser(null);
  };

  const logout = () => {
    handleLogout();
    toast.success('Sesión cerrada');
    navigate('/login');
  };

  const value = {
    user,
    isAuthenticated: !!user,
    isLoading,
    login,
    register,
    logout
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};

// Hook personalizado para acceder al contexto
export const useAuth = () => {
  const context = useContext(AuthContext);
  
  if (context === undefined) {
    throw new Error('useAuth debe ser usado dentro de un AuthProvider');
  }
  
  return context;
};
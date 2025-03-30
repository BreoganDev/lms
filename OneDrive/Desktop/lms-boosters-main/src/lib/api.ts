// src/lib/api.ts
import axios from 'axios';
import { toast } from 'sonner';

// API base URL
const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:3001/api';

// Crear instancia axios con configuración base
const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Interceptor para agregar el token de autenticación a las solicitudes
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('lms_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Interceptor para manejar errores de respuesta
api.interceptors.response.use(
  (response) => response,
  (error) => {
    const { response } = error;
    
    if (response) {
      // Manejar errores de autenticación
      if (response.status === 401) {
        localStorage.removeItem('lms_token');
        localStorage.removeItem('lms_user');
        window.location.href = '/login';
        toast.error('Su sesión ha expirado. Por favor, inicie sesión nuevamente.');
      }
      
      // Mostrar mensaje de error
      const errorMessage = response.data?.message || 'Ha ocurrido un error';
      toast.error(errorMessage);
    } else {
      // Error de red o servidor no disponible
      toast.error('No se pudo conectar con el servidor. Verifique su conexión a Internet.');
    }
    
    return Promise.reject(error);
  }
);

export default api;
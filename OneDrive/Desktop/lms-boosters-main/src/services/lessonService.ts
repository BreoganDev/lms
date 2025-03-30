// src/services/lessonService.ts
import api from '@/lib/api';
import { Lesson } from '@/types';

export const lessonService = {
  // Obtener todas las lecciones
  async getAll(filters = {}): Promise<Lesson[]> {
    try {
      const response = await api.get<{success: boolean; data: Lesson[]}>('/lessons', { params: filters });
      return response.data.data;
    } catch (error) {
      console.error('Error al obtener lecciones:', error);
      throw error;
    }
  },

  // Obtener lecciones por curso
  async getByCourse(courseId: number | string): Promise<Lesson[]> {
    try {
      const response = await api.get<{success: boolean; data: Lesson[]}>(`/courses/${courseId}/lessons`);
      return response.data.data;
    } catch (error) {
      console.error(`Error al obtener lecciones del curso con ID ${courseId}:`, error);
      throw error;
    }
  },

  // Obtener una lección por ID
  async getById(id: number | string): Promise<Lesson> {
    try {
      const response = await api.get<{success: boolean; data: Lesson}>(`/lessons/${id}`);
      return response.data.data;
    } catch (error) {
      console.error(`Error al obtener lección con ID ${id}:`, error);
      throw error;
    }
  },

  // Crear una nueva lección
  async create(lessonData: Partial<Lesson>): Promise<Lesson> {
    try {
      const response = await api.post<{success: boolean; data: Lesson}>('/lessons', lessonData);
      return response.data.data;
    } catch (error) {
      console.error('Error al crear lección:', error);
      throw error;
    }
  },

  // Actualizar una lección existente
  async update(id: number | string, lessonData: Partial<Lesson>): Promise<Lesson> {
    try {
      const response = await api.put<{success: boolean; data: Lesson}>(`/lessons/${id}`, lessonData);
      return response.data.data;
    } catch (error) {
      console.error(`Error al actualizar lección con ID ${id}:`, error);
      throw error;
    }
  },

  // Eliminar una lección
  async delete(id: number | string): Promise<void> {
    try {
      await api.delete(`/lessons/${id}`);
    } catch (error) {
      console.error(`Error al eliminar lección con ID ${id}:`, error);
      throw error;
    }
  }
};
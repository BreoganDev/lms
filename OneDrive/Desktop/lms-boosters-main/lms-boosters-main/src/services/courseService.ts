// src/services/courseService.ts
import api from '@/lib/api';
import { Course } from '@/types';

export const courseService = {
  // Obtener todos los cursos
  async getAll(filters = {}): Promise<Course[]> {
    try {
      const response = await api.get<{success: boolean; data: Course[]}>('/courses', { params: filters });
      return response.data.data;
    } catch (error) {
      console.error('Error al obtener cursos:', error);
      throw error;
    }
  },

  // Obtener un curso por ID
  async getById(id: number | string): Promise<Course> {
    try {
      const response = await api.get<{success: boolean; data: Course}>(`/courses/${id}`);
      return response.data.data;
    } catch (error) {
      console.error(`Error al obtener curso con ID ${id}:`, error);
      throw error;
    }
  },

  // Crear un nuevo curso
  async create(courseData: Partial<Course>): Promise<Course> {
    try {
      const response = await api.post<{success: boolean; data: Course}>('/courses', courseData);
      return response.data.data;
    } catch (error) {
      console.error('Error al crear curso:', error);
      throw error;
    }
  },

  // Actualizar un curso existente
  async update(id: number | string, courseData: Partial<Course>): Promise<Course> {
    try {
      const response = await api.put<{success: boolean; data: Course}>(`/courses/${id}`, courseData);
      return response.data.data;
    } catch (error) {
      console.error(`Error al actualizar curso con ID ${id}:`, error);
      throw error;
    }
  },

  // Eliminar un curso
  async delete(id: number | string): Promise<void> {
    try {
      await api.delete(`/courses/${id}`);
    } catch (error) {
      console.error(`Error al eliminar curso con ID ${id}:`, error);
      throw error;
    }
  },

  // Obtener lecciones de un curso
  async getLessons(courseId: number | string): Promise<any[]> {
    try {
      const response = await api.get<{success: boolean; data: any[]}>(`/courses/${courseId}/lessons`);
      return response.data.data;
    } catch (error) {
      console.error(`Error al obtener lecciones del curso con ID ${courseId}:`, error);
      throw error;
    }
  },

  // Subir imagen del curso
  async uploadImage(courseId: number | string, imageFile: File): Promise<string> {
    try {
      const formData = new FormData();
      formData.append('image', imageFile);

      const response = await api.put<{success: boolean; data: string}>(`/courses/${courseId}/photo`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });

      return response.data.data;
    } catch (error) {
      console.error(`Error al subir imagen para el curso con ID ${courseId}:`, error);
      throw error;
    }
  },

  // Obtener cursos por instructor
  async getByInstructor(instructorId: number | string): Promise<Course[]> {
    try {
      const response = await api.get<{success: boolean; data: Course[]}>(`/courses/instructor/${instructorId}`);
      return response.data.data;
    } catch (error) {
      console.error(`Error al obtener cursos del instructor con ID ${instructorId}:`, error);
      throw error;
    }
  }
};
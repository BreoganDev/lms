// src/services/studentService.ts
import api from '@/lib/api';
import { Student } from '@/types';

export const studentService = {
  // Obtener todos los estudiantes
  async getAll(filters = {}): Promise<Student[]> {
    try {
      const response = await api.get<{success: boolean; data: Student[]}>('/students', { params: filters });
      return response.data.data;
    } catch (error) {
      console.error('Error al obtener estudiantes:', error);
      throw error;
    }
  },

  // Obtener un estudiante por ID
  async getById(id: number | string): Promise<Student> {
    try {
      const response = await api.get<{success: boolean; data: Student}>(`/students/${id}`);
      return response.data.data;
    } catch (error) {
      console.error(`Error al obtener estudiante con ID ${id}:`, error);
      throw error;
    }
  },

  // Crear un nuevo estudiante
  async create(studentData: Partial<Student>): Promise<Student> {
    try {
      const response = await api.post<{success: boolean; data: Student}>('/students', studentData);
      return response.data.data;
    } catch (error) {
      console.error('Error al crear estudiante:', error);
      throw error;
    }
  },

  // Actualizar un estudiante existente
  async update(id: number | string, studentData: Partial<Student>): Promise<Student> {
    try {
      const response = await api.put<{success: boolean; data: Student}>(`/students/${id}`, studentData);
      return response.data.data;
    } catch (error) {
      console.error(`Error al actualizar estudiante con ID ${id}:`, error);
      throw error;
    }
  },

  // Eliminar un estudiante
  async delete(id: number | string): Promise<void> {
    try {
      await api.delete(`/students/${id}`);
    } catch (error) {
      console.error(`Error al eliminar estudiante con ID ${id}:`, error);
      throw error;
    }
  },

  // Matricular un estudiante en un curso
  async enrollInCourse(studentId: number | string, courseId: number | string): Promise<void> {
    try {
      await api.post(`/students/${studentId}/enroll`, { courseId });
    } catch (error) {
      console.error(`Error al matricular estudiante ${studentId} en el curso ${courseId}:`, error);
      throw error;
    }
  },

  // Dar de baja a un estudiante de un curso
  async unenrollFromCourse(studentId: number | string, courseId: number | string): Promise<void> {
    try {
      await api.delete(`/students/${studentId}/courses/${courseId}`);
    } catch (error) {
      console.error(`Error al dar de baja estudiante ${studentId} del curso ${courseId}:`, error);
      throw error;
    }
  }
};
// src/services/quizService.ts
import api from '@/lib/api';
import { Quiz, QuizSubmission } from '@/types';

export const quizService = {
  // Obtener todos los quizzes
  async getAll(filters = {}): Promise<Quiz[]> {
    try {
      const response = await api.get<{success: boolean; data: Quiz[]}>('/quizzes', { params: filters });
      return response.data.data;
    } catch (error) {
      console.error('Error al obtener cuestionarios:', error);
      throw error;
    }
  },

  // Obtener quizzes por curso
  async getByCourse(courseId: number | string): Promise<Quiz[]> {
    try {
      const response = await api.get<{success: boolean; data: Quiz[]}>(`/courses/${courseId}/quizzes`);
      return response.data.data;
    } catch (error) {
      console.error(`Error al obtener cuestionarios del curso con ID ${courseId}:`, error);
      throw error;
    }
  },

  // Obtener un quiz por ID
  async getById(id: number | string): Promise<Quiz> {
    try {
      const response = await api.get<{success: boolean; data: Quiz}>(`/quizzes/${id}`);
      return response.data.data;
    } catch (error) {
      console.error(`Error al obtener cuestionario con ID ${id}:`, error);
      throw error;
    }
  },

  // Crear un nuevo quiz
  async create(quizData: Partial<Quiz>): Promise<Quiz> {
    try {
      const response = await api.post<{success: boolean; data: Quiz}>('/quizzes', quizData);
      return response.data.data;
    } catch (error) {
      console.error('Error al crear cuestionario:', error);
      throw error;
    }
  },

  // Actualizar un quiz existente
  async update(id: number | string, quizData: Partial<Quiz>): Promise<Quiz> {
    try {
      const response = await api.put<{success: boolean; data: Quiz}>(`/quizzes/${id}`, quizData);
      return response.data.data;
    } catch (error) {
      console.error(`Error al actualizar cuestionario con ID ${id}:`, error);
      throw error;
    }
  },

  // Eliminar un quiz
  async delete(id: number | string): Promise<void> {
    try {
      await api.delete(`/quizzes/${id}`);
    } catch (error) {
      console.error(`Error al eliminar cuestionario con ID ${id}:`, error);
      throw error;
    }
  },

  // Enviar respuestas de un quiz
  async submitQuiz(quizId: number | string, answers: QuizSubmission): Promise<{score: number; passed: boolean}> {
    try {
      const response = await api.post<{success: boolean; data: {score: number; passed: boolean}}>(`/quizzes/${quizId}/submit`, answers);
      return response.data.data;
    } catch (error) {
      console.error(`Error al enviar respuestas del cuestionario con ID ${quizId}:`, error);
      throw error;
    }
  }
};
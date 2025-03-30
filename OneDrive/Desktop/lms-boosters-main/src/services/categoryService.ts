// src/services/categoryService.ts
import api from '@/lib/api';
import { Category } from '@/types';

export const categoryService = {
  // Obtener todas las categorías
  async getAll(): Promise<Category[]> {
    try {
      const response = await api.get<{success: boolean; data: Category[]}>('/categories');
      return response.data.data;
    } catch (error) {
      console.error('Error al obtener categorías:', error);
      throw error;
    }
  },

  // Obtener una categoría por ID
  async getById(id: number | string): Promise<Category> {
    try {
      const response = await api.get<{success: boolean; data: Category}>(`/categories/${id}`);
      return response.data.data;
    } catch (error) {
      console.error(`Error al obtener categoría con ID ${id}:`, error);
      throw error;
    }
  },

  // Crear una nueva categoría
  async create(categoryData: Partial<Category>): Promise<Category> {
    try {
      const response = await api.post<{success: boolean; data: Category}>('/categories', categoryData);
      return response.data.data;
    } catch (error) {
      console.error('Error al crear categoría:', error);
      throw error;
    }
  },

  // Actualizar una categoría existente
  async update(id: number | string, categoryData: Partial<Category>): Promise<Category> {
    try {
      const response = await api.put<{success: boolean; data: Category}>(`/categories/${id}`, categoryData);
      return response.data.data;
    } catch (error) {
      console.error(`Error al actualizar categoría con ID ${id}:`, error);
      throw error;
    }
  },

  // Eliminar una categoría
  async delete(id: number | string): Promise<void> {
    try {
      await api.delete(`/categories/${id}`);
    } catch (error) {
      console.error(`Error al eliminar categoría con ID ${id}:`, error);
      throw error;
    }
  },

  // Obtener cursos por categoría
  async getCourses(categoryId: number | string): Promise<any[]> {
    try {
      const response = await api.get<{success: boolean; data: any[]}>(`/categories/${categoryId}/courses`);
      return response.data.data;
    } catch (error) {
      console.error(`Error al obtener cursos de la categoría con ID ${categoryId}:`, error);
      throw error;
    }
  }
};
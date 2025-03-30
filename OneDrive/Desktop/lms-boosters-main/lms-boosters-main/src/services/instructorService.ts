// src/services/instructorService.ts
import api from '@/lib/api';
import { Instructor } from '@/types';

export const instructorService = {
  // Obtener todos los instructores
  async getAll(): Promise<Instructor[]> {
    try {
      const response = await api.get<{success: boolean; data: Instructor[]}>('/instructors');
      return response.data.data;
    } catch (error) {
      console.error('Error al obtener instructores:', error);
      throw error;
    }
  },

  // Obtener un instructor por ID
  async getById(id: number | string): Promise<Instructor> {
    try {
      const response = await api.get<{success: boolean; data: Instructor}>(`/instructors/${id}`);
      return response.data.data;
    } catch (error) {
      console.error(`Error al obtener instructor con ID ${id}:`, error);
      throw error;
    }
  },

  // Crear un nuevo instructor
  async create(instructorData: Partial<Instructor>): Promise<Instructor> {
    try {
      const response = await api.post<{success: boolean; data: Instructor}>('/instructors', instructorData);
      return response.data.data;
    } catch (error) {
      console.error('Error al crear instructor:', error);
      throw error;
    }
  },

  // Actualizar un instructor existente
  async update(id: number | string, instructorData: Partial<Instructor>): Promise<Instructor> {
    try {
      const response = await api.put<{success: boolean; data: Instructor}>(`/instructors/${id}`, instructorData);
      return response.data.data;
    } catch (error) {
      console.error(`Error al actualizar instructor con ID ${id}:`, error);
      throw error;
    }
  },

  // Eliminar un instructor
  async delete(id: number | string): Promise<void> {
    try {
      await api.delete(`/instructors/${id}`);
    } catch (error) {
      console.error(`Error al eliminar instructor con ID ${id}:`, error);
      throw error;
    }
  },

  // Subir avatar del instructor
  async uploadAvatar(instructorId: number | string, avatarFile: File): Promise<string> {
    try {
      const formData = new FormData();
      formData.append('avatar', avatarFile);

      const response = await api.put<{success: boolean; data: any}>(`/instructors/${instructorId}/avatar`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });

      return response.data.data.avatar;
    } catch (error) {
      console.error(`Error al subir avatar para el instructor con ID ${instructorId}:`, error);
      throw error;
    }
  }
};
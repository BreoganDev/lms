// src/services/index.ts
// Este archivo exporta todos los servicios

export * from './authService';
export * from './courseService';
export * from './lessonService';
export * from './studentService';
export * from './categoryService';
export * from './instructorService';
export * from './quizService';

// src/services/authService.ts
import api from '@/lib/api';

// Definimos los tipos para la autenticación
export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  name: string;
  email: string;
  password: string;
  role?: string;
}

export interface AuthResponse {
  user: {
    id: number;
    name: string;
    email: string;
    role: string;
  };
  token: string;
}

// Servicio de autenticación
export const authService = {
  // Iniciar sesión
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth/login', credentials);
    // Guardar token y datos de usuario en localStorage
    localStorage.setItem('lms_token', response.data.token);
    localStorage.setItem('lms_user', JSON.stringify(response.data.user));
    return response.data;
  },

  // Registrar nuevo usuario
  async register(data: RegisterData): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth/register', data);
    // Guardar token y datos de usuario en localStorage
    localStorage.setItem('lms_token', response.data.token);
    localStorage.setItem('lms_user', JSON.stringify(response.data.user));
    return response.data;
  },

  // Cerrar sesión
  logout(): void {
    localStorage.removeItem('lms_token');
    localStorage.removeItem('lms_user');
  },

  // Verificar si el usuario está autenticado
  isAuthenticated(): boolean {
    return !!localStorage.getItem('lms_token');
  },

  // Obtener usuario actual
  getCurrentUser() {
    const userStr = localStorage.getItem('lms_user');
    return userStr ? JSON.parse(userStr) : null;
  }
};

// src/services/courseService.ts
import api from '@/lib/api';
import { Course } from '@/types';

export const courseService = {
  // Obtener todos los cursos
  async getAll(): Promise<Course[]> {
    const response = await api.get<Course[]>('/courses');
    return response.data;
  },

  // Obtener un curso por ID
  async getById(id: number): Promise<Course> {
    const response = await api.get<Course>(`/courses/${id}`);
    return response.data;
  },

  // Crear un nuevo curso
  async create(courseData: Partial<Course>): Promise<Course> {
    const response = await api.post<Course>('/courses', courseData);
    return response.data;
  },

  // Actualizar un curso existente
  async update(id: number, courseData: Partial<Course>): Promise<Course> {
    const response = await api.put<Course>(`/courses/${id}`, courseData);
    return response.data;
  },

  // Eliminar un curso
  async delete(id: number): Promise<void> {
    await api.delete(`/courses/${id}`);
  }
};

// src/services/lessonService.ts
import api from '@/lib/api';
import { Lesson } from '@/types';

export const lessonService = {
  // Obtener todas las lecciones
  async getAll(): Promise<Lesson[]> {
    const response = await api.get<Lesson[]>('/lessons');
    return response.data;
  },

  // Obtener lecciones por curso
  async getByCourse(courseId: number): Promise<Lesson[]> {
    const response = await api.get<Lesson[]>(`/courses/${courseId}/lessons`);
    return response.data;
  },

  // Obtener una lección por ID
  async getById(id: number): Promise<Lesson> {
    const response = await api.get<Lesson>(`/lessons/${id}`);
    return response.data;
  },

  // Crear una nueva lección
  async create(lessonData: Partial<Lesson>): Promise<Lesson> {
    const response = await api.post<Lesson>('/lessons', lessonData);
    return response.data;
  },

  // Actualizar una lección existente
  async update(id: number, lessonData: Partial<Lesson>): Promise<Lesson> {
    const response = await api.put<Lesson>(`/lessons/${id}`, lessonData);
    return response.data;
  },

  // Eliminar una lección
  async delete(id: number): Promise<void> {
    await api.delete(`/lessons/${id}`);
  }
};

// src/services/studentService.ts
import api from '@/lib/api';
import { Student } from '@/types';

export const studentService = {
  // Obtener todos los estudiantes
  async getAll(): Promise<Student[]> {
    const response = await api.get<Student[]>('/students');
    return response.data;
  },

  // Obtener un estudiante por ID
  async getById(id: number): Promise<Student> {
    const response = await api.get<Student>(`/students/${id}`);
    return response.data;
  },

  // Crear un nuevo estudiante
  async create(studentData: Partial<Student>): Promise<Student> {
    const response = await api.post<Student>('/students', studentData);
    return response.data;
  },

  // Actualizar un estudiante existente
  async update(id: number, studentData: Partial<Student>): Promise<Student> {
    const response = await api.put<Student>(`/students/${id}`, studentData);
    return response.data;
  },

  // Eliminar un estudiante
  async delete(id: number): Promise<void> {
    await api.delete(`/students/${id}`);
  },

  // Matricular un estudiante en un curso
  async enrollInCourse(studentId: number, courseId: number): Promise<void> {
    await api.post(`/students/${studentId}/enroll`, { courseId });
  },

  // Dar de baja a un estudiante de un curso
  async unenrollFromCourse(studentId: number, courseId: number): Promise<void> {
    await api.delete(`/students/${studentId}/courses/${courseId}`);
  }
};

// src/services/categoryService.ts
import api from '@/lib/api';
import { Category } from '@/types';

export const categoryService = {
  // Obtener todas las categorías
  async getAll(): Promise<Category[]> {
    const response = await api.get<Category[]>('/categories');
    return response.data;
  },

  // Obtener una categoría por ID
  async getById(id: number): Promise<Category> {
    const response = await api.get<Category>(`/categories/${id}`);
    return response.data;
  },

  // Crear una nueva categoría
  async create(categoryData: Partial<Category>): Promise<Category> {
    const response = await api.post<Category>('/categories', categoryData);
    return response.data;
  },

  // Actualizar una categoría existente
  async update(id: number, categoryData: Partial<Category>): Promise<Category> {
    const response = await api.put<Category>(`/categories/${id}`, categoryData);
    return response.data;
  },

  // Eliminar una categoría
  async delete(id: number): Promise<void> {
    await api.delete(`/categories/${id}`);
  }
};

// src/services/instructorService.ts
import api from '@/lib/api';
import { Instructor } from '@/types';

export const instructorService = {
  // Obtener todos los instructores
  async getAll(): Promise<Instructor[]> {
    const response = await api.get<Instructor[]>('/instructors');
    return response.data;
  },

  // Obtener un instructor por ID
  async getById(id: number): Promise<Instructor> {
    const response = await api.get<Instructor>(`/instructors/${id}`);
    return response.data;
  },

  // Crear un nuevo instructor
  async create(instructorData: Partial<Instructor>): Promise<Instructor> {
    const response = await api.post<Instructor>('/instructors', instructorData);
    return response.data;
  },

  // Actualizar un instructor existente
  async update(id: number, instructorData: Partial<Instructor>): Promise<Instructor> {
    const response = await api.put<Instructor>(`/instructors/${id}`, instructorData);
    return response.data;
  },

  // Eliminar un instructor
  async delete(id: number): Promise<void> {
    await api.delete(`/instructors/${id}`);
  }
};

// src/services/quizService.ts
import api from '@/lib/api';
import { Quiz, QuizSubmission } from '@/types';

export const quizService = {
  // Obtener todos los quizzes
  async getAll(): Promise<Quiz[]> {
    const response = await api.get<Quiz[]>('/quizzes');
    return response.data;
  },

  // Obtener quizzes por curso
  async getByCourse(courseId: number): Promise<Quiz[]> {
    const response = await api.get<Quiz[]>(`/courses/${courseId}/quizzes`);
    return response.data;
  },

  // Obtener un quiz por ID
  async getById(id: number): Promise<Quiz> {
    const response = await api.get<Quiz>(`/quizzes/${id}`);
    return response.data;
  },

  // Crear un nuevo quiz
  async create(quizData: Partial<Quiz>): Promise<Quiz> {
    const response = await api.post<Quiz>('/quizzes', quizData);
    return response.data;
  },

  // Actualizar un quiz existente
  async update(id: number, quizData: Partial<Quiz>): Promise<Quiz> {
    const response = await api.put<Quiz>(`/quizzes/${id}`, quizData);
    return response.data;
  },

  // Eliminar un quiz
  async delete(id: number): Promise<void> {
    await api.delete(`/quizzes/${id}`);
  },

  // Enviar respuestas de un quiz
  async submitQuiz(quizId: number, answers: QuizSubmission): Promise<{score: number; passed: boolean}> {
    const response = await api.post<{score: number; passed: boolean}>(`/quizzes/${quizId}/submit`, answers);
    return response.data;
  }
};
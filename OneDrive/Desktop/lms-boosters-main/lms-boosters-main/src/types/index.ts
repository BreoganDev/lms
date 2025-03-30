// src/types/index.ts
// Modelo Course
export interface Course {
    id: number;
    title: string;
    description: string;
    price: number;
    isPublished: boolean;
    featuredImage?: string;
    category: string | Category;
    categoryId?: number;
    isPremium: boolean;
    showInCatalog: boolean;
    instructor: string | Instructor;
    instructorId?: number;
    lessons?: number;
    students?: number;
    status: string;
    createdAt?: string;
    updatedAt?: string;
  }
  
  // Modelo Lesson
  export interface Lesson {
    id: number;
    title: string;
    content: string;
    courseId: number;
    course?: string | Course;
    order: number;
    duration: string;
    status: string;
    createdAt?: string;
    updatedAt?: string;
  }
  
  // Modelo Student
  export interface Student {
    id: number;
    name: string;
    email: string;
    phone?: string;
    isActive: boolean;
    courses: number;
    status: string;
    enrolledCourses?: {id: number, title: string}[];
    createdAt?: string;
    updatedAt?: string;
  }
  
  // Modelo Category
  export interface Category {
    id: number;
    name: string;
    slug: string;
    description?: string;
    icon?: string;
    coursesCount: number;
    createdAt?: string;
    updatedAt?: string;
  }
  
  // Modelo Instructor
  export interface Instructor {
    id: number;
    name: string;
    email: string;
    bio?: string;
    phone?: string;
    isActive: boolean;
    expertise?: string;
    website?: string;
    avatar?: string;
    courses: number;
    rating: number;
    status: string;
    createdAt?: string;
    updatedAt?: string;
  }
  
  // Modelos para Quiz
  export interface QuizSubmission {
    studentId: number;
    answers: {
      questionId: number;
      selectedOptionIds: number[];
    }[];
  }
  
  export interface Quiz {
    id: number;
    title: string;
    description?: string;
    courseId: number;
    course?: string;
    passingScore: number;
    questions: any[];
    questionsCount?: number;
    status: string;
    createdAt?: string;
    updatedAt?: string;
  }
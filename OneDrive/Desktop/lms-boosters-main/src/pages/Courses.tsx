
import React, { useState } from 'react';
import MainLayout from '@/components/layout/MainLayout';
import { CourseList, initialCourses, Course } from '@/components/courses/CourseList';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { toast } from 'sonner';

const Courses = () => {
  const navigate = useNavigate();
  const [courses, setCourses] = useState<Course[]>(initialCourses);

  const handleCreateCourse = () => {
    navigate('/courses/new');
  };

  const handleDeleteCourse = (id: number) => {
    setCourses(courses.filter(course => course.id !== id));
    toast.success('Curso eliminado con éxito');
  };

  return (
    <MainLayout>
      <div className="space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold tracking-tight">Cursos</h1>
          <Button onClick={handleCreateCourse} className="flex items-center gap-2">
            <Plus size={16} /> Nuevo Curso
          </Button>
        </div>

        <CourseList courses={courses} onDelete={handleDeleteCourse} />
      </div>
    </MainLayout>
  );
};

export default Courses;

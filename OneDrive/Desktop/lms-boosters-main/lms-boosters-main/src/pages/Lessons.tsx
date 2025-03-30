
import React, { useState } from 'react';
import MainLayout from '@/components/layout/MainLayout';
import { LessonList, initialLessons, Lesson } from '@/components/lessons/LessonList';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { toast } from 'sonner';

const Lessons = () => {
  const navigate = useNavigate();
  const [lessons, setLessons] = useState<Lesson[]>(initialLessons);

  const handleCreateLesson = () => {
    navigate('/lessons/new');
  };

  const handleDeleteLesson = (id: number) => {
    setLessons(lessons.filter(lesson => lesson.id !== id));
    toast.success('Lección eliminada con éxito');
  };

  return (
    <MainLayout>
      <div className="space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold tracking-tight">Lecciones</h1>
          <Button onClick={handleCreateLesson} className="flex items-center gap-2">
            <Plus size={16} /> Nueva Lección
          </Button>
        </div>

        <LessonList lessons={lessons} onDelete={handleDeleteLesson} />
      </div>
    </MainLayout>
  );
};

export default Lessons;

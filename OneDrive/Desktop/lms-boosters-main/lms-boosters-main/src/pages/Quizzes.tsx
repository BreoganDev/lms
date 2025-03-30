
import React, { useState } from 'react';
import MainLayout from '@/components/layout/MainLayout';
import { QuizList, initialQuizzes, Quiz } from '@/components/quizzes/QuizList';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { toast } from 'sonner';

const Quizzes = () => {
  const navigate = useNavigate();
  const [quizzes, setQuizzes] = useState<Quiz[]>(initialQuizzes);

  const handleCreateQuiz = () => {
    navigate('/quizzes/new');
  };

  const handleDeleteQuiz = (id: number) => {
    setQuizzes(quizzes.filter(quiz => quiz.id !== id));
    toast.success('Cuestionario eliminado con éxito');
  };

  return (
    <MainLayout>
      <div className="space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold tracking-tight">Cuestionarios</h1>
          <Button onClick={handleCreateQuiz} className="flex items-center gap-2">
            <Plus size={16} /> Nuevo Cuestionario
          </Button>
        </div>

        <QuizList quizzes={quizzes} onDelete={handleDeleteQuiz} />
      </div>
    </MainLayout>
  );
};

export default Quizzes;

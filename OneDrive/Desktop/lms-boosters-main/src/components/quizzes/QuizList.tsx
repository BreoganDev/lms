
import React, { useState } from 'react';
import { 
  Table, 
  TableBody, 
  TableCell, 
  TableHead, 
  TableHeader, 
  TableRow 
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Eye, Pencil, Trash2 } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import { toast } from 'sonner';

// Type for quiz data
export interface Quiz {
  id: number;
  title: string;
  course: string;
  questions: number;
  status: string;
}

// Initial quizzes data
export const initialQuizzes: Quiz[] = [
  { 
    id: 1, 
    title: 'HTML Básico Quiz', 
    course: 'Desarrollo Web',
    questions: 10,
    status: 'Published'
  },
  { 
    id: 2, 
    title: 'JavaScript Avanzado Quiz', 
    course: 'Programación',
    questions: 15,
    status: 'Draft'
  },
  { 
    id: 3, 
    title: 'Principios de UI Quiz', 
    course: 'Diseño',
    questions: 8,
    status: 'Published'
  },
  { 
    id: 4, 
    title: 'Node.js Fundamentos Quiz', 
    course: 'Desarrollo Backend',
    questions: 12,
    status: 'Draft'
  },
  { 
    id: 5, 
    title: 'Python Básico Quiz', 
    course: 'Programación',
    questions: 10,
    status: 'Published'
  },
];

interface QuizListProps {
  quizzes?: Quiz[];
  onDelete?: (id: number) => void;
}

export const QuizList = ({ quizzes = initialQuizzes, onDelete }: QuizListProps) => {
  const navigate = useNavigate();
  const [quizToDelete, setQuizToDelete] = useState<number | null>(null);

  const handleDelete = (id: number) => {
    if (onDelete) {
      onDelete(id);
    } else {
      toast.success('Cuestionario eliminado con éxito');
    }
    setQuizToDelete(null);
  };

  return (
    <>
      <div className="rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Título</TableHead>
              <TableHead>Curso</TableHead>
              <TableHead>Preguntas</TableHead>
              <TableHead>Estado</TableHead>
              <TableHead className="text-right">Acciones</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {quizzes.map((quiz) => (
              <TableRow key={quiz.id}>
                <TableCell className="font-medium">{quiz.title}</TableCell>
                <TableCell>{quiz.course}</TableCell>
                <TableCell>{quiz.questions}</TableCell>
                <TableCell>
                  <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                    quiz.status === 'Published' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-yellow-100 text-yellow-800'
                  }`}>
                    {quiz.status}
                  </span>
                </TableCell>
                <TableCell className="text-right">
                  <div className="flex justify-end gap-2">
                    <Button variant="ghost" size="sm" onClick={() => navigate(`/quizzes/${quiz.id}`)}>
                      <Eye size={16} />
                      <span className="sr-only">Ver</span>
                    </Button>
                    <Button variant="ghost" size="sm" onClick={() => navigate(`/quizzes/${quiz.id}/edit`)}>
                      <Pencil size={16} />
                      <span className="sr-only">Editar</span>
                    </Button>
                    <Button variant="ghost" size="sm" onClick={() => setQuizToDelete(quiz.id)}>
                      <Trash2 size={16} className="text-destructive" />
                      <span className="sr-only">Eliminar</span>
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>

      <AlertDialog open={quizToDelete !== null} onOpenChange={(open) => !open && setQuizToDelete(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>¿Está seguro de eliminar este cuestionario?</AlertDialogTitle>
            <AlertDialogDescription>
              Esta acción no se puede deshacer. El cuestionario será eliminado permanentemente.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancelar</AlertDialogCancel>
            <AlertDialogAction onClick={() => quizToDelete && handleDelete(quizToDelete)} className="bg-destructive text-destructive-foreground">
              Eliminar
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </>
  );
};

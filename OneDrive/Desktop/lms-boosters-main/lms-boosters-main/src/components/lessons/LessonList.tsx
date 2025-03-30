
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

// Type for lesson data
export interface Lesson {
  id: number;
  title: string;
  course: string;
  duration: string;
  status: string;
}

// Initial lessons data
export const initialLessons: Lesson[] = [
  { 
    id: 1, 
    title: 'Introducción a HTML', 
    course: 'Desarrollo Web',
    duration: '45 min',
    status: 'Published'
  },
  { 
    id: 2, 
    title: 'CSS Básico', 
    course: 'Desarrollo Web',
    duration: '60 min',
    status: 'Published'
  },
  { 
    id: 3, 
    title: 'JavaScript Fundamentos', 
    course: 'Programación',
    duration: '75 min',
    status: 'Draft'
  },
  { 
    id: 4, 
    title: 'Principios de Diseño UI', 
    course: 'Diseño',
    duration: '50 min',
    status: 'Published'
  },
  { 
    id: 5, 
    title: 'Node.js Básico', 
    course: 'Desarrollo Backend',
    duration: '65 min',
    status: 'Draft'
  },
];

interface LessonListProps {
  lessons?: Lesson[];
  onDelete?: (id: number) => void;
}

export const LessonList = ({ lessons = initialLessons, onDelete }: LessonListProps) => {
  const navigate = useNavigate();
  const [lessonToDelete, setLessonToDelete] = useState<number | null>(null);

  const handleDelete = (id: number) => {
    if (onDelete) {
      onDelete(id);
    } else {
      toast.success('Lección eliminada con éxito');
    }
    setLessonToDelete(null);
  };

  return (
    <>
      <div className="rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Título</TableHead>
              <TableHead>Curso</TableHead>
              <TableHead>Duración</TableHead>
              <TableHead>Estado</TableHead>
              <TableHead className="text-right">Acciones</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {lessons.map((lesson) => (
              <TableRow key={lesson.id}>
                <TableCell className="font-medium">{lesson.title}</TableCell>
                <TableCell>{lesson.course}</TableCell>
                <TableCell>{lesson.duration}</TableCell>
                <TableCell>
                  <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                    lesson.status === 'Published' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-yellow-100 text-yellow-800'
                  }`}>
                    {lesson.status}
                  </span>
                </TableCell>
                <TableCell className="text-right">
                  <div className="flex justify-end gap-2">
                    <Button variant="ghost" size="sm" onClick={() => navigate(`/lessons/${lesson.id}`)}>
                      <Eye size={16} />
                      <span className="sr-only">Ver</span>
                    </Button>
                    <Button variant="ghost" size="sm" onClick={() => navigate(`/lessons/${lesson.id}/edit`)}>
                      <Pencil size={16} />
                      <span className="sr-only">Editar</span>
                    </Button>
                    <Button variant="ghost" size="sm" onClick={() => setLessonToDelete(lesson.id)}>
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

      <AlertDialog open={lessonToDelete !== null} onOpenChange={(open) => !open && setLessonToDelete(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>¿Está seguro de eliminar esta lección?</AlertDialogTitle>
            <AlertDialogDescription>
              Esta acción no se puede deshacer. La lección será eliminada permanentemente.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancelar</AlertDialogCancel>
            <AlertDialogAction onClick={() => lessonToDelete && handleDelete(lessonToDelete)} className="bg-destructive text-destructive-foreground">
              Eliminar
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </>
  );
};

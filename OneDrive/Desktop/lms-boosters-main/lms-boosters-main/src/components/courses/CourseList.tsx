
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

// Type for course data
export interface Course {
  id: number;
  title: string;
  category: string;
  lessons: number;
  students: number;
  status: string;
}

// Initial courses data
export const initialCourses: Course[] = [
  { 
    id: 1, 
    title: 'Introducción a React', 
    category: 'Desarrollo Web',
    lessons: 10,
    students: 24,
    status: 'Published'
  },
  { 
    id: 2, 
    title: 'JavaScript Avanzado', 
    category: 'Programación',
    lessons: 8,
    students: 16,
    status: 'Draft'
  },
  { 
    id: 3, 
    title: 'Diseño UI/UX', 
    category: 'Diseño',
    lessons: 12,
    students: 32,
    status: 'Published'
  },
  { 
    id: 4, 
    title: 'Node.js para Principiantes', 
    category: 'Desarrollo Backend',
    lessons: 14,
    students: 18,
    status: 'Published'
  },
  { 
    id: 5, 
    title: 'Python Básico', 
    category: 'Programación',
    lessons: 9,
    students: 28,
    status: 'Draft'
  },
];

interface CourseListProps {
  courses?: Course[];
  onDelete?: (id: number) => void;
}

export const CourseList = ({ courses = initialCourses, onDelete }: CourseListProps) => {
  const navigate = useNavigate();
  const [courseToDelete, setCourseToDelete] = useState<number | null>(null);

  const handleDelete = (id: number) => {
    if (onDelete) {
      onDelete(id);
    } else {
      toast.success('Curso eliminado con éxito');
    }
    setCourseToDelete(null);
  };

  return (
    <>
      <div className="rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Título</TableHead>
              <TableHead>Categoría</TableHead>
              <TableHead>Lecciones</TableHead>
              <TableHead>Estudiantes</TableHead>
              <TableHead>Estado</TableHead>
              <TableHead className="text-right">Acciones</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {courses.map((course) => (
              <TableRow key={course.id}>
                <TableCell className="font-medium">{course.title}</TableCell>
                <TableCell>{course.category}</TableCell>
                <TableCell>{course.lessons}</TableCell>
                <TableCell>{course.students}</TableCell>
                <TableCell>
                  <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                    course.status === 'Published' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-yellow-100 text-yellow-800'
                  }`}>
                    {course.status}
                  </span>
                </TableCell>
                <TableCell className="text-right">
                  <div className="flex justify-end gap-2">
                    <Button variant="ghost" size="sm" onClick={() => navigate(`/courses/${course.id}`)}>
                      <Eye size={16} />
                      <span className="sr-only">Ver</span>
                    </Button>
                    <Button variant="ghost" size="sm" onClick={() => navigate(`/courses/${course.id}/edit`)}>
                      <Pencil size={16} />
                      <span className="sr-only">Editar</span>
                    </Button>
                    <Button variant="ghost" size="sm" onClick={() => setCourseToDelete(course.id)}>
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

      <AlertDialog open={courseToDelete !== null} onOpenChange={(open) => !open && setCourseToDelete(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>¿Está seguro de eliminar este curso?</AlertDialogTitle>
            <AlertDialogDescription>
              Esta acción no se puede deshacer. El curso será eliminado permanentemente.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancelar</AlertDialogCancel>
            <AlertDialogAction onClick={() => courseToDelete && handleDelete(courseToDelete)} className="bg-destructive text-destructive-foreground">
              Eliminar
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </>
  );
};

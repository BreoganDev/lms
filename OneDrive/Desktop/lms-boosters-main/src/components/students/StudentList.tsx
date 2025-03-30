
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

// Type for student data
export interface Student {
  id: number;
  name: string;
  email: string;
  courses: number;
  status: string;
}

// Initial students data
export const initialStudents: Student[] = [
  { 
    id: 1, 
    name: 'Juan Pérez', 
    email: 'juan.perez@ejemplo.com',
    courses: 3,
    status: 'Active'
  },
  { 
    id: 2, 
    name: 'María López', 
    email: 'maria.lopez@ejemplo.com',
    courses: 2,
    status: 'Active'
  },
  { 
    id: 3, 
    name: 'Pedro Sánchez', 
    email: 'pedro.sanchez@ejemplo.com',
    courses: 1,
    status: 'Inactive'
  },
  { 
    id: 4, 
    name: 'Ana Martínez', 
    email: 'ana.martinez@ejemplo.com',
    courses: 4,
    status: 'Active'
  },
  { 
    id: 5, 
    name: 'Carlos Rodríguez', 
    email: 'carlos.rodriguez@ejemplo.com',
    courses: 0,
    status: 'Inactive'
  },
];

interface StudentListProps {
  students?: Student[];
  onDelete?: (id: number) => void;
}

export const StudentList = ({ students = initialStudents, onDelete }: StudentListProps) => {
  const navigate = useNavigate();
  const [studentToDelete, setStudentToDelete] = useState<number | null>(null);

  const handleDelete = (id: number) => {
    if (onDelete) {
      onDelete(id);
    } else {
      toast.success('Estudiante eliminado con éxito');
    }
    setStudentToDelete(null);
  };

  return (
    <>
      <div className="rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Nombre</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>Cursos</TableHead>
              <TableHead>Estado</TableHead>
              <TableHead className="text-right">Acciones</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {students.map((student) => (
              <TableRow key={student.id}>
                <TableCell className="font-medium">{student.name}</TableCell>
                <TableCell>{student.email}</TableCell>
                <TableCell>{student.courses}</TableCell>
                <TableCell>
                  <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                    student.status === 'Active' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-red-100 text-red-800'
                  }`}>
                    {student.status}
                  </span>
                </TableCell>
                <TableCell className="text-right">
                  <div className="flex justify-end gap-2">
                    <Button variant="ghost" size="sm" onClick={() => navigate(`/students/${student.id}`)}>
                      <Eye size={16} />
                      <span className="sr-only">Ver</span>
                    </Button>
                    <Button variant="ghost" size="sm" onClick={() => navigate(`/students/${student.id}/edit`)}>
                      <Pencil size={16} />
                      <span className="sr-only">Editar</span>
                    </Button>
                    <Button variant="ghost" size="sm" onClick={() => setStudentToDelete(student.id)}>
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

      <AlertDialog open={studentToDelete !== null} onOpenChange={(open) => !open && setStudentToDelete(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>¿Está seguro de eliminar este estudiante?</AlertDialogTitle>
            <AlertDialogDescription>
              Esta acción no se puede deshacer. El estudiante será eliminado permanentemente.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancelar</AlertDialogCancel>
            <AlertDialogAction onClick={() => studentToDelete && handleDelete(studentToDelete)} className="bg-destructive text-destructive-foreground">
              Eliminar
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </>
  );
};


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
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useNavigate } from 'react-router-dom';
import { Instructor } from '@/pages/Instructors';
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

interface InstructorListProps {
  instructors: Instructor[];
  onDelete: (id: number) => void;
  onEdit: (id: number) => void;
}

export const InstructorList = ({ instructors, onDelete, onEdit }: InstructorListProps) => {
  const navigate = useNavigate();
  const [instructorToDelete, setInstructorToDelete] = useState<number | null>(null);
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);

  const handleDelete = (id: number) => {
    setInstructorToDelete(id);
    setIsDeleteDialogOpen(true);
  };

  const confirmDelete = () => {
    if (instructorToDelete) {
      onDelete(instructorToDelete);
      setIsDeleteDialogOpen(false);
      setInstructorToDelete(null);
    }
  };

  const handleView = (id: number) => {
    // For now, just navigate to the edit page as we don't have a view page
    navigate(`/instructors/${id}/edit`);
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
              <TableHead>Valoración</TableHead>
              <TableHead>Estado</TableHead>
              <TableHead className="text-right">Acciones</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {instructors.map((instructor) => (
              <TableRow key={instructor.id}>
                <TableCell>
                  <div className="flex items-center gap-3">
                    <Avatar>
                      <AvatarImage src={instructor.avatar} />
                      <AvatarFallback>{instructor.name.substring(0, 2)}</AvatarFallback>
                    </Avatar>
                    <span className="font-medium">{instructor.name}</span>
                  </div>
                </TableCell>
                <TableCell>{instructor.email}</TableCell>
                <TableCell>{instructor.courses}</TableCell>
                <TableCell>
                  <div className="flex items-center gap-1">
                    <span className="text-amber-500">★</span>
                    <span>{instructor.rating}</span>
                  </div>
                </TableCell>
                <TableCell>
                  <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                    instructor.status === 'Active' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-red-100 text-red-800'
                  }`}>
                    {instructor.status}
                  </span>
                </TableCell>
                <TableCell className="text-right">
                  <div className="flex justify-end gap-2">
                    <Button variant="ghost" size="sm" onClick={() => handleView(instructor.id)}>
                      <Eye size={16} />
                      <span className="sr-only">Ver</span>
                    </Button>
                    <Button variant="ghost" size="sm" onClick={() => onEdit(instructor.id)}>
                      <Pencil size={16} />
                      <span className="sr-only">Editar</span>
                    </Button>
                    <Button variant="ghost" size="sm" onClick={() => handleDelete(instructor.id)}>
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

      <AlertDialog open={isDeleteDialogOpen} onOpenChange={setIsDeleteDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>¿Está seguro de eliminar este instructor?</AlertDialogTitle>
            <AlertDialogDescription>
              Esta acción no se puede deshacer. El instructor será eliminado permanentemente del sistema.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancelar</AlertDialogCancel>
            <AlertDialogAction onClick={confirmDelete} className="bg-destructive text-destructive-foreground">
              Eliminar
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </>
  );
};

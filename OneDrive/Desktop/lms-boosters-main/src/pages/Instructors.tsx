
import React, { useState } from 'react';
import MainLayout from '@/components/layout/MainLayout';
import { InstructorList } from '@/components/instructors/InstructorList';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { toast } from 'sonner';

// Type for the instructor data
export interface Instructor {
  id: number;
  name: string;
  email: string;
  courses: number;
  rating: number;
  status: string;
  avatar: string;
}

// Initial instructors data
export const initialInstructors: Instructor[] = [
  { 
    id: 1, 
    name: 'Ana García', 
    email: 'ana.garcia@ejemplo.com',
    courses: 3,
    rating: 4.8,
    status: 'Active',
    avatar: '',
  },
  { 
    id: 2, 
    name: 'Carlos Rodríguez', 
    email: 'carlos.rodriguez@ejemplo.com',
    courses: 2,
    rating: 4.5,
    status: 'Active',
    avatar: '',
  },
  { 
    id: 3, 
    name: 'Laura Martínez', 
    email: 'laura.martinez@ejemplo.com',
    courses: 4,
    rating: 4.9,
    status: 'Active',
    avatar: '',
  },
];

const Instructors = () => {
  const navigate = useNavigate();
  const [instructors, setInstructors] = useState<Instructor[]>(initialInstructors);

  const handleCreateInstructor = () => {
    navigate('/instructors/new');
  };

  const handleEditInstructor = (id: number) => {
    navigate(`/instructors/${id}/edit`);
  };

  const handleDeleteInstructor = (id: number) => {
    setInstructors(instructors.filter(instructor => instructor.id !== id));
    toast.success('Instructor eliminado con éxito');
  };

  return (
    <MainLayout>
      <div className="space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold tracking-tight">Instructores</h1>
          <Button onClick={handleCreateInstructor} className="flex items-center gap-2">
            <Plus size={16} /> Nuevo Instructor
          </Button>
        </div>

        <InstructorList 
          instructors={instructors}
          onDelete={handleDeleteInstructor}
          onEdit={handleEditInstructor}
        />
      </div>
    </MainLayout>
  );
};

export default Instructors;


import React, { useState } from 'react';
import MainLayout from '@/components/layout/MainLayout';
import { StudentList, initialStudents, Student } from '@/components/students/StudentList';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { toast } from 'sonner';

const Students = () => {
  const navigate = useNavigate();
  const [students, setStudents] = useState<Student[]>(initialStudents);

  const handleCreateStudent = () => {
    navigate('/students/new');
  };

  const handleDeleteStudent = (id: number) => {
    setStudents(students.filter(student => student.id !== id));
    toast.success('Estudiante eliminado con éxito');
  };

  return (
    <MainLayout>
      <div className="space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold tracking-tight">Estudiantes</h1>
          <Button onClick={handleCreateStudent} className="flex items-center gap-2">
            <Plus size={16} /> Nuevo Estudiante
          </Button>
        </div>

        <StudentList students={students} onDelete={handleDeleteStudent} />
      </div>
    </MainLayout>
  );
};

export default Students;

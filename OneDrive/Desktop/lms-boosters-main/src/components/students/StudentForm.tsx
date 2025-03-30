
import React from 'react';
import { useForm } from 'react-hook-form';
import { useNavigate, useParams } from 'react-router-dom';
import MainLayout from '@/components/layout/MainLayout';
import { Button } from '@/components/ui/button';
import { Form, FormField, FormItem, FormLabel, FormControl, FormMessage, FormDescription } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { toast } from 'sonner';
import { Card, CardContent } from '@/components/ui/card';

type StudentFormData = {
  name: string;
  email: string;
  phone: string;
  isActive: boolean;
  role: string;
  password: string;
  confirmPassword: string;
  enrolledCourses: string[];
};

const StudentForm = () => {
  const navigate = useNavigate();
  const { id } = useParams();
  const isEditing = Boolean(id);

  const form = useForm<StudentFormData>({
    defaultValues: {
      name: '',
      email: '',
      phone: '',
      isActive: true,
      role: 'student',
      password: '',
      confirmPassword: '',
      enrolledCourses: [],
    },
  });

  const onSubmit = (data: StudentFormData) => {
    console.log('Estudiante guardado:', data);
    
    // Aquí se integraría con una API real
    setTimeout(() => {
      toast.success(
        isEditing ? 'Estudiante actualizado con éxito' : 'Estudiante creado con éxito'
      );
      navigate('/students');
    }, 500);
  };

  const mockCourses = [
    { id: '1', title: 'Introducción a React' },
    { id: '2', title: 'JavaScript Avanzado' },
    { id: '3', title: 'Diseño UI/UX' },
    { id: '4', title: 'Node.js para Principiantes' },
    { id: '5', title: 'Python Básico' },
  ];

  return (
    <MainLayout>
      <div className="space-y-6 max-w-3xl mx-auto">
        <h1 className="text-3xl font-bold tracking-tight">
          {isEditing ? 'Editar Estudiante' : 'Crear Nuevo Estudiante'}
        </h1>

        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
            <Card>
              <CardContent className="pt-6 space-y-6">
                <FormField
                  control={form.control}
                  name="name"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Nombre</FormLabel>
                      <FormControl>
                        <Input {...field} placeholder="Ingrese el nombre del estudiante" />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="email"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Email</FormLabel>
                      <FormControl>
                        <Input {...field} type="email" placeholder="nombre@ejemplo.com" />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="phone"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Teléfono</FormLabel>
                      <FormControl>
                        <Input {...field} placeholder="Ingrese el teléfono" />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="role"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Rol</FormLabel>
                      <Select onValueChange={field.onChange} defaultValue={field.value}>
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue placeholder="Seleccione un rol" />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          <SelectItem value="student">Estudiante</SelectItem>
                          <SelectItem value="instructor">Instructor</SelectItem>
                          <SelectItem value="admin">Administrador</SelectItem>
                        </SelectContent>
                      </Select>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                {!isEditing && (
                  <>
                    <FormField
                      control={form.control}
                      name="password"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Contraseña</FormLabel>
                          <FormControl>
                            <Input {...field} type="password" />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />

                    <FormField
                      control={form.control}
                      name="confirmPassword"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Confirmar Contraseña</FormLabel>
                          <FormControl>
                            <Input {...field} type="password" />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </>
                )}

                <FormField
                  control={form.control}
                  name="isActive"
                  render={({ field }) => (
                    <FormItem className="flex items-start space-x-3 space-y-0 rounded-md p-4 border">
                      <FormControl>
                        <Checkbox 
                          checked={field.value} 
                          onCheckedChange={field.onChange}
                        />
                      </FormControl>
                      <div className="space-y-1 leading-none">
                        <FormLabel>Activo</FormLabel>
                        <FormDescription>
                          El estudiante puede acceder a la plataforma y los cursos.
                        </FormDescription>
                      </div>
                    </FormItem>
                  )}
                />

                <div className="mt-6">
                  <h3 className="text-lg font-medium mb-4">Cursos Matriculados</h3>
                  <div className="space-y-3">
                    {mockCourses.map((course) => (
                      <div key={course.id} className="flex items-center space-x-2">
                        <Checkbox id={`course-${course.id}`} />
                        <label htmlFor={`course-${course.id}`} className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                          {course.title}
                        </label>
                      </div>
                    ))}
                  </div>
                </div>
              </CardContent>
            </Card>

            <div className="flex gap-4 justify-end">
              <Button type="button" variant="outline" onClick={() => navigate('/students')}>
                Cancelar
              </Button>
              <Button type="submit">Guardar</Button>
            </div>
          </form>
        </Form>
      </div>
    </MainLayout>
  );
};

export default StudentForm;

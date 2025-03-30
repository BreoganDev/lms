
import React from 'react';
import { useForm } from 'react-hook-form';
import { useNavigate, useParams } from 'react-router-dom';
import MainLayout from '@/components/layout/MainLayout';
import { Button } from '@/components/ui/button';
import { Form, FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { 
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { toast } from 'sonner';

type LessonFormData = {
  title: string;
  content: string;
  courseId: string;
  order: number;
};

const mockCourses = [
  { id: '1', title: 'WordPress Development Fundamentals' },
  { id: '2', title: 'Advanced Theme Customization' },
  { id: '3', title: 'Plugin Development Masterclass' },
  { id: '4', title: 'WooCommerce Integration' },
];

const LessonForm = () => {
  const navigate = useNavigate();
  const { id } = useParams();
  const isEditing = Boolean(id);

  const form = useForm<LessonFormData>({
    defaultValues: {
      title: '',
      content: '',
      courseId: '',
      order: 1,
    },
  });

  const onSubmit = (data: LessonFormData) => {
    console.log('Lección guardada:', data);
    
    // Aquí se integraría con una API real
    setTimeout(() => {
      toast.success(
        isEditing ? 'Lección actualizada con éxito' : 'Lección creada con éxito'
      );
      navigate('/lessons');
    }, 500);
  };

  return (
    <MainLayout>
      <div className="space-y-6 max-w-3xl mx-auto">
        <h1 className="text-3xl font-bold tracking-tight">
          {isEditing ? 'Editar Lección' : 'Crear Nueva Lección'}
        </h1>

        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
            <FormField
              control={form.control}
              name="title"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Título</FormLabel>
                  <FormControl>
                    <Input {...field} placeholder="Ingrese el título de la lección" />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={form.control}
              name="courseId"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Curso</FormLabel>
                  <Select 
                    onValueChange={field.onChange} 
                    defaultValue={field.value}
                  >
                    <FormControl>
                      <SelectTrigger>
                        <SelectValue placeholder="Seleccione un curso" />
                      </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                      {mockCourses.map(course => (
                        <SelectItem key={course.id} value={course.id}>
                          {course.title}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={form.control}
              name="content"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Contenido</FormLabel>
                  <FormControl>
                    <Textarea 
                      {...field} 
                      placeholder="Ingrese el contenido de la lección"
                      className="min-h-32"
                    />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={form.control}
              name="order"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Orden</FormLabel>
                  <FormControl>
                    <Input 
                      type="number" 
                      {...field} 
                      onChange={(e) => field.onChange(parseInt(e.target.value))}
                    />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <div className="flex gap-4 justify-end">
              <Button type="button" variant="outline" onClick={() => navigate('/lessons')}>
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

export default LessonForm;

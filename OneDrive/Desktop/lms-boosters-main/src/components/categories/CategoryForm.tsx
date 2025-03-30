// src/components/categories/CategoryForm.tsx
import React, { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { useNavigate, useParams } from 'react-router-dom';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import MainLayout from '@/components/layout/MainLayout';
import { Button } from '@/components/ui/button';
import { Form, FormField, FormItem, FormLabel, FormControl, FormMessage, FormDescription } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { toast } from 'sonner';
import { Spinner } from '@/components/ui/spinner';
import { categoryService } from '@/services/categoryService';

// Esquema de validación con Zod
const categorySchema = z.object({
  name: z.string().min(2, { message: 'El nombre debe tener al menos 2 caracteres' }),
  description: z.string().optional(),
  slug: z.string().min(2, { message: 'El slug debe tener al menos 2 caracteres' }),
  icon: z.string().optional(),
});

type CategoryFormData = z.infer<typeof categorySchema>;

const CategoryForm = () => {
  const navigate = useNavigate();
  const { id } = useParams();
  const isEditing = Boolean(id);
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [isFetching, setIsFetching] = useState<boolean>(isEditing);

  // Inicializar formulario
  const form = useForm<CategoryFormData>({
    resolver: zodResolver(categorySchema),
    defaultValues: {
      name: '',
      description: '',
      slug: '',
      icon: '',
    },
  });

  // Cargar datos de la categoría si estamos editando
  useEffect(() => {
    const fetchCategory = async () => {
      if (isEditing && id) {
        try {
          setIsFetching(true);
          const category = await categoryService.getById(id);
          
          // Actualizar formulario con datos de la categoría
          form.reset({
            name: category.name,
            description: category.description || '',
            slug: category.slug,
            icon: category.icon || '',
          });
        } catch (error) {
          console.error('Error al cargar la categoría:', error);
          toast.error('No se pudo cargar la categoría');
          navigate('/categories');
        } finally {
          setIsFetching(false);
        }
      }
    };

    fetchCategory();
  }, [isEditing, id, form, navigate]);

  // Función para generar slug a partir del nombre
  const generateSlug = (name: string) => {
    return name
      .toLowerCase()
      .replace(/\s+/g, '-')
      .replace(/[^\w\-]+/g, '')
      .replace(/\-\-+/g, '-')
      .replace(/^-+/, '')
      .replace(/-+$/, '');
  };

  // Manejar cambio en el campo de nombre
  const onNameChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const name = e.target.value;
    form.setValue('name', name);
    
    // Solo actualizar el slug automáticamente si está vacío o si no estamos editando
    if (!form.getValues('slug') || !isEditing) {
      form.setValue('slug', generateSlug(name));
    }
  };

  // Manejar envío del formulario
  const onSubmit = async (data: CategoryFormData) => {
    try {
      setIsLoading(true);
      
      if (isEditing && id) {
        // Actualizar categoría existente
        await categoryService.update(id, data);
        toast.success('Categoría actualizada con éxito');
      } else {
        // Crear nueva categoría
        await categoryService.create(data);
        toast.success('Categoría creada con éxito');
      }
      
      navigate('/categories');
    } catch (error: any) {
      console.error('Error al guardar la categoría:', error);
      
      // Mostrar mensaje de error específico si está disponible
      const errorMessage = error.response?.data?.message || 'Error al guardar la categoría';
      toast.error(errorMessage);
    } finally {
      setIsLoading(false);
    }
  };

  // Mostrar spinner mientras se cargan los datos
  if (isFetching) {
    return (
      <MainLayout>
        <div className="flex justify-center items-center h-[60vh]">
          <Spinner className="h-8 w-8" />
        </div>
      </MainLayout>
    );
  }

  return (
    <MainLayout>
      <div className="space-y-6 max-w-3xl mx-auto">
        <h1 className="text-3xl font-bold tracking-tight">
          {isEditing ? 'Editar Categoría' : 'Crear Nueva Categoría'}
        </h1>

        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle>Información de la Categoría</CardTitle>
              </CardHeader>
              <CardContent className="space-y-6">
                <FormField
                  control={form.control}
                  name="name"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Nombre</FormLabel>
                      <FormControl>
                        <Input 
                          {...field} 
                          placeholder="Ingrese el nombre de la categoría" 
                          onChange={onNameChange}
                          disabled={isLoading}
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="description"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Descripción</FormLabel>
                      <FormControl>
                        <Textarea 
                          {...field} 
                          placeholder="Describa brevemente la categoría"
                          className="min-h-32"
                          disabled={isLoading}
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="slug"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Slug</FormLabel>
                      <FormControl>
                        <Input 
                          {...field} 
                          placeholder="nombre-de-la-categoria" 
                          disabled={isLoading}
                        />
                      </FormControl>
                      <FormDescription>
                        El slug se utiliza en las URLs. Se genera automáticamente a partir del nombre, pero puede editarlo.
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="icon"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Icono (nombre de clase)</FormLabel>
                      <FormControl>
                        <Input 
                          {...field} 
                          placeholder="Ej: fa-book, icon-web, etc." 
                          disabled={isLoading}
                        />
                      </FormControl>
                      <FormDescription>
                        Nombre de la clase CSS del icono que se mostrará junto a la categoría.
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </CardContent>
            </Card>

            <div className="flex gap-4 justify-end">
              <Button 
                type="button" 
                variant="outline" 
                onClick={() => navigate('/categories')}
                disabled={isLoading}
              >
                Cancelar
              </Button>
              <Button type="submit" disabled={isLoading}>
                {isLoading && <Spinner className="mr-2 h-4 w-4" />}
                Guardar Categoría
              </Button>
            </div>
          </form>
        </Form>
      </div>
    </MainLayout>
  );
};

export default CategoryForm;
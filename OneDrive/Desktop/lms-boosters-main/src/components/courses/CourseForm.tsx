
import React, { useState } from 'react';
import { useForm } from 'react-hook-form';
import { useNavigate, useParams } from 'react-router-dom';
import MainLayout from '@/components/layout/MainLayout';
import { Button } from '@/components/ui/button';
import { Form, FormField, FormItem, FormLabel, FormControl, FormMessage, FormDescription } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Switch } from '@/components/ui/switch';
import { toast } from 'sonner';
import { DollarSign, Plus, X, Upload } from 'lucide-react';

type CourseFormData = {
  title: string;
  description: string;
  price: number;
  isPublished: boolean;
  featuredImage?: string;
  category: string;
  isPremium: boolean;
  showInCatalog: boolean;
  instructor: string;
};

const CourseForm = () => {
  const navigate = useNavigate();
  const { id } = useParams();
  const isEditing = Boolean(id);
  const [selectedImage, setSelectedImage] = useState<string | null>(null);

  const form = useForm<CourseFormData>({
    defaultValues: {
      title: '',
      description: '',
      price: 0,
      isPublished: false,
      category: '',
      isPremium: false,
      showInCatalog: true,
      instructor: '',
    },
  });

  const onSubmit = (data: CourseFormData) => {
    console.log('Curso guardado:', data);
    
    // Aquí se integraría con una API real
    setTimeout(() => {
      toast.success(
        isEditing ? 'Curso actualizado con éxito' : 'Curso creado con éxito'
      );
      navigate('/courses');
    }, 500);
  };

  const mockCategories = [
    "Desarrollo Web",
    "Programación",
    "Diseño",
    "Marketing Digital",
    "Idiomas",
    "Negocios"
  ];

  const mockInstructors = [
    { id: '1', name: 'Ana García' },
    { id: '2', name: 'Carlos Rodríguez' },
    { id: '3', name: 'Laura Martínez' },
  ];

  const handleImageUpload = (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        setSelectedImage(e.target?.result as string);
      };
      reader.readAsDataURL(file);
    }
  };
  
  return (
    <MainLayout>
      <div className="space-y-6 max-w-3xl mx-auto">
        <h1 className="text-3xl font-bold tracking-tight">
          {isEditing ? 'Editar Curso' : 'Crear Nuevo Curso'}
        </h1>

        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle>Información Básica</CardTitle>
              </CardHeader>
              <CardContent className="space-y-6">
                <FormField
                  control={form.control}
                  name="title"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Título</FormLabel>
                      <FormControl>
                        <Input {...field} placeholder="Ingrese el título del curso" />
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
                          placeholder="Ingrese la descripción del curso"
                          className="min-h-32"
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="category"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Categoría</FormLabel>
                      <Select onValueChange={field.onChange} defaultValue={field.value}>
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue placeholder="Seleccionar categoría" />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          {mockCategories.map((category) => (
                            <SelectItem key={category} value={category}>
                              {category}
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
                  name="instructor"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Instructor</FormLabel>
                      <Select onValueChange={field.onChange} defaultValue={field.value}>
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue placeholder="Seleccionar instructor" />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          {mockInstructors.map((instructor) => (
                            <SelectItem key={instructor.id} value={instructor.id}>
                              {instructor.name}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <div className="space-y-3">
                  <label className="block text-sm font-medium text-gray-700">Imagen Destacada</label>
                  <div className="flex items-center space-x-5">
                    <div className="flex-shrink-0 h-40 w-40 border-2 border-dashed border-gray-300 rounded-md flex items-center justify-center">
                      {selectedImage ? (
                        <div className="relative h-full w-full">
                          <img 
                            src={selectedImage} 
                            alt="Vista previa" 
                            className="h-full w-full object-cover rounded-md" 
                          />
                          <button
                            type="button"
                            onClick={() => setSelectedImage(null)}
                            className="absolute top-1 right-1 bg-red-500 text-white p-1 rounded-full"
                          >
                            <X size={16} />
                          </button>
                        </div>
                      ) : (
                        <div className="text-center">
                          <Upload className="mx-auto h-12 w-12 text-gray-400" />
                          <p className="text-xs text-gray-500 mt-1">Subir imagen</p>
                        </div>
                      )}
                    </div>
                    <div>
                      <Button 
                        type="button" 
                        variant="outline" 
                        className="relative"
                        onClick={() => document.getElementById('image-upload')?.click()}
                      >
                        <input
                          id="image-upload"
                          type="file"
                          className="sr-only"
                          accept="image/*"
                          onChange={handleImageUpload}
                        />
                        <Plus size={16} className="mr-2" />
                        Seleccionar Imagen
                      </Button>
                      <p className="text-xs text-gray-500 mt-2">PNG, JPG o GIF. Máximo 2MB.</p>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Precios y Disponibilidad</CardTitle>
              </CardHeader>
              <CardContent className="space-y-6">
                <FormField
                  control={form.control}
                  name="price"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Precio</FormLabel>
                      <FormControl>
                        <div className="relative">
                          <DollarSign className="absolute left-2 top-2.5 h-5 w-5 text-gray-400" />
                          <Input 
                            type="number" 
                            {...field} 
                            onChange={(e) => field.onChange(parseFloat(e.target.value))}
                            className="pl-9"
                          />
                        </div>
                      </FormControl>
                      <FormDescription>
                        Establezca 0 si el curso es gratuito.
                      </FormDescription>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="isPremium"
                  render={({ field }) => (
                    <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                      <div className="space-y-0.5">
                        <FormLabel className="text-base">Curso Premium</FormLabel>
                        <FormDescription>
                          Marque esta opción si es un curso premium.
                        </FormDescription>
                      </div>
                      <FormControl>
                        <Switch
                          checked={field.value}
                          onCheckedChange={field.onChange}
                        />
                      </FormControl>
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="isPublished"
                  render={({ field }) => (
                    <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                      <div className="space-y-0.5">
                        <FormLabel className="text-base">Publicado</FormLabel>
                        <FormDescription>
                          Este curso será visible para los estudiantes.
                        </FormDescription>
                      </div>
                      <FormControl>
                        <Switch
                          checked={field.value}
                          onCheckedChange={field.onChange}
                        />
                      </FormControl>
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="showInCatalog"
                  render={({ field }) => (
                    <FormItem className="flex flex-row items-center justify-between rounded-lg border p-4">
                      <div className="space-y-0.5">
                        <FormLabel className="text-base">Mostrar en Catálogo</FormLabel>
                        <FormDescription>
                          Este curso aparecerá en el catálogo de cursos.
                        </FormDescription>
                      </div>
                      <FormControl>
                        <Switch
                          checked={field.value}
                          onCheckedChange={field.onChange}
                        />
                      </FormControl>
                    </FormItem>
                  )}
                />
              </CardContent>
            </Card>

            <div className="flex gap-4 justify-end">
              <Button type="button" variant="outline" onClick={() => navigate('/courses')}>
                Cancelar
              </Button>
              <Button type="submit">Guardar Curso</Button>
            </div>
          </form>
        </Form>
      </div>
    </MainLayout>
  );
};

export default CourseForm;

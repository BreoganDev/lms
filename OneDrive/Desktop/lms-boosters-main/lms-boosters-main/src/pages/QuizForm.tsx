
import React from 'react';
import { useFieldArray, useForm } from 'react-hook-form';
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
import { Plus, Trash2 } from 'lucide-react';
import { Checkbox } from '@/components/ui/checkbox';

type QuestionOption = {
  text: string;
  isCorrect: boolean;
};

type Question = {
  text: string;
  type: 'multiple_choice' | 'true_false';
  options: QuestionOption[];
};

type QuizFormData = {
  title: string;
  description: string;
  courseId: string;
  passingScore: number;
  questions: Question[];
};

const mockCourses = [
  { id: '1', title: 'WordPress Development Fundamentals' },
  { id: '2', title: 'Advanced Theme Customization' },
  { id: '3', title: 'Plugin Development Masterclass' },
  { id: '4', title: 'WooCommerce Integration' },
];

const QuizForm = () => {
  const navigate = useNavigate();
  const { id } = useParams();
  const isEditing = Boolean(id);

  const form = useForm<QuizFormData>({
    defaultValues: {
      title: '',
      description: '',
      courseId: '',
      passingScore: 70,
      questions: [
        {
          text: '',
          type: 'multiple_choice',
          options: [
            { text: '', isCorrect: false },
            { text: '', isCorrect: false },
          ],
        },
      ],
    },
  });

  const { fields: questionsFields, append: appendQuestion, remove: removeQuestion } = 
    useFieldArray({
      control: form.control,
      name: "questions",
    });

  const onSubmit = (data: QuizFormData) => {
    console.log('Cuestionario guardado:', data);
    
    // Aquí se integraría con una API real
    setTimeout(() => {
      toast.success(
        isEditing ? 'Cuestionario actualizado con éxito' : 'Cuestionario creado con éxito'
      );
      navigate('/quizzes');
    }, 500);
  };

  const addOption = (questionIndex: number) => {
    const currentOptions = form.getValues(`questions.${questionIndex}.options`);
    form.setValue(`questions.${questionIndex}.options`, [
      ...currentOptions,
      { text: '', isCorrect: false }
    ]);
  };

  const removeOption = (questionIndex: number, optionIndex: number) => {
    const currentOptions = form.getValues(`questions.${questionIndex}.options`);
    if (currentOptions.length <= 2) {
      toast.error("Una pregunta debe tener al menos 2 opciones");
      return;
    }
    
    const newOptions = [...currentOptions];
    newOptions.splice(optionIndex, 1);
    form.setValue(`questions.${questionIndex}.options`, newOptions);
  };

  return (
    <MainLayout>
      <div className="space-y-6 max-w-4xl mx-auto pb-12">
        <h1 className="text-3xl font-bold tracking-tight">
          {isEditing ? 'Editar Cuestionario' : 'Crear Nuevo Cuestionario'}
        </h1>

        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-8">
            <div className="space-y-6 border p-6 rounded-lg">
              <h2 className="text-xl font-semibold">Información General</h2>
              
              <FormField
                control={form.control}
                name="title"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Título</FormLabel>
                    <FormControl>
                      <Input {...field} placeholder="Ingrese el título del cuestionario" />
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
                        placeholder="Ingrese la descripción del cuestionario" 
                      />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                  name="passingScore"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Porcentaje para Aprobar (%)</FormLabel>
                      <FormControl>
                        <Input 
                          type="number" 
                          min="0"
                          max="100"
                          {...field} 
                          onChange={(e) => field.onChange(parseInt(e.target.value))}
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
              </div>
            </div>

            <div className="space-y-6">
              <div className="flex items-center justify-between">
                <h2 className="text-xl font-semibold">Preguntas</h2>
                <Button 
                  type="button" 
                  variant="outline" 
                  onClick={() => appendQuestion({
                    text: '',
                    type: 'multiple_choice',
                    options: [
                      { text: '', isCorrect: false },
                      { text: '', isCorrect: false },
                    ],
                  })}
                  className="flex items-center gap-2"
                >
                  <Plus size={16} /> Añadir Pregunta
                </Button>
              </div>

              {questionsFields.map((field, questionIndex) => (
                <div key={field.id} className="border p-6 rounded-lg space-y-6">
                  <div className="flex justify-between items-start">
                    <h3 className="font-medium">Pregunta {questionIndex + 1}</h3>
                    {questionsFields.length > 1 && (
                      <Button 
                        type="button" 
                        variant="ghost" 
                        onClick={() => removeQuestion(questionIndex)}
                        size="sm"
                      >
                        <Trash2 size={16} className="text-destructive" />
                      </Button>
                    )}
                  </div>
                  
                  <FormField
                    control={form.control}
                    name={`questions.${questionIndex}.text`}
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Texto de la Pregunta</FormLabel>
                        <FormControl>
                          <Textarea {...field} placeholder="Ingrese la pregunta" />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name={`questions.${questionIndex}.type`}
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Tipo de Pregunta</FormLabel>
                        <Select 
                          onValueChange={field.onChange} 
                          defaultValue={field.value}
                        >
                          <FormControl>
                            <SelectTrigger>
                              <SelectValue />
                            </SelectTrigger>
                          </FormControl>
                          <SelectContent>
                            <SelectItem value="multiple_choice">Opción múltiple</SelectItem>
                            <SelectItem value="true_false">Verdadero/Falso</SelectItem>
                          </SelectContent>
                        </Select>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <div className="space-y-4">
                    <div className="flex items-center justify-between">
                      <FormLabel className="font-medium">Opciones</FormLabel>
                      <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        onClick={() => addOption(questionIndex)}
                        className="flex items-center gap-1"
                      >
                        <Plus size={14} /> Añadir Opción
                      </Button>
                    </div>

                    {form.watch(`questions.${questionIndex}.options`)?.map((_, optionIndex) => (
                      <div key={optionIndex} className="flex items-center gap-4">
                        <FormField
                          control={form.control}
                          name={`questions.${questionIndex}.options.${optionIndex}.isCorrect`}
                          render={({ field }) => (
                            <FormItem className="flex items-center space-x-2 space-y-0">
                              <FormControl>
                                <Checkbox 
                                  checked={field.value} 
                                  onCheckedChange={field.onChange}
                                />
                              </FormControl>
                            </FormItem>
                          )}
                        />
                        
                        <FormField
                          control={form.control}
                          name={`questions.${questionIndex}.options.${optionIndex}.text`}
                          render={({ field }) => (
                            <FormItem className="flex-1">
                              <FormControl>
                                <Input {...field} placeholder="Opción de respuesta" />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />
                        
                        <Button
                          type="button"
                          variant="ghost"
                          size="icon"
                          onClick={() => removeOption(questionIndex, optionIndex)}
                        >
                          <Trash2 size={16} className="text-muted-foreground" />
                        </Button>
                      </div>
                    ))}
                  </div>
                </div>
              ))}
            </div>

            <div className="flex gap-4 justify-end">
              <Button type="button" variant="outline" onClick={() => navigate('/quizzes')}>
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

export default QuizForm;

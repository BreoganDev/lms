const bcrypt = require('bcryptjs');
const db = require('../models');

// Datos iniciales para categorías
const categories = [
  { 
    name: 'Desarrollo Web', 
    slug: 'desarrollo-web',
    description: 'Cursos relacionados con el desarrollo web',
    icon: 'fa-code',
    coursesCount: 3
  },
  { 
    name: 'Programación', 
    slug: 'programacion',
    description: 'Cursos de lenguajes de programación',
    icon: 'fa-laptop-code',
    coursesCount: 2
  },
  { 
    name: 'Diseño', 
    slug: 'diseno',
    description: 'Cursos de diseño gráfico y UX/UI',
    icon: 'fa-pencil-ruler',
    coursesCount: 1
  },
  { 
    name: 'Marketing Digital', 
    slug: 'marketing-digital',
    description: 'Cursos de marketing online y estrategias digitales',
    icon: 'fa-bullhorn',
    coursesCount: 0
  },
  { 
    name: 'Idiomas', 
    slug: 'idiomas',
    description: 'Cursos para aprender diferentes idiomas',
    icon: 'fa-language',
    coursesCount: 0
  }
];

// Función para crear usuarios iniciales
const createUsers = async () => {
  // Encriptar contraseñas
  const salt = await bcrypt.genSalt(10);
  const adminPassword = await bcrypt.hash('admin123', salt);
  const instructorPassword = await bcrypt.hash('instructor123', salt);
  const studentPassword = await bcrypt.hash('student123', salt);

  // Crear usuarios
  const admin = await db.User.create({
    name: 'Admin User',
    email: 'admin@ejemplo.com',
    password: adminPassword,
    role: 'admin',
    isActive: true
  });

  const instructor1 = await db.User.create({
    name: 'Roberto Gómez',
    email: 'roberto.gomez@ejemplo.com',
    password: instructorPassword,
    role: 'instructor',
    isActive: true
  });

  const instructor2 = await db.User.create({
    name: 'Laura Sánchez',
    email: 'laura.sanchez@ejemplo.com',
    password: instructorPassword,
    role: 'instructor',
    isActive: true
  });

  const student1 = await db.User.create({
    name: 'Juan Pérez',
    email: 'juan.perez@ejemplo.com',
    password: studentPassword,
    role: 'student',
    isActive: true
  });

  const student2 = await db.User.create({
    name: 'María López',
    email: 'maria.lopez@ejemplo.com',
    password: studentPassword,
    role: 'student',
    isActive: true
  });

  const student3 = await db.User.create({
    name: 'Pedro Sánchez',
    email: 'pedro.sanchez@ejemplo.com',
    password: studentPassword,
    role: 'student',
    isActive: false
  });

  console.log('Usuarios creados correctamente');
  return { admin, instructor1, instructor2, student1, student2, student3 };
};

// Función para crear categorías iniciales
const createCategories = async () => {
  const createdCategories = await Promise.all(
    categories.map(category => db.Category.create(category))
  );
  
  console.log('Categorías creadas correctamente');
  return createdCategories;
};

// Función para crear cursos iniciales
const createCourses = async (instructors, categories) => {
  const courses = [
    {
      title: 'Introducción a HTML y CSS',
      description: 'Aprende los fundamentos del desarrollo web con HTML y CSS',
      instructorId: instructors.instructor1.id,
      categoryId: categories[0].id, // Desarrollo Web
      image: 'html-css.jpg',
      price: 29.99,
      duration: '10 horas',
      rating: 4.7,
      studentsCount: 1250,
      isPublished: true,
      slug: 'introduccion-html-css'
    },
    {
      title: 'JavaScript Avanzado',
      description: 'Domina conceptos avanzados de JavaScript y programación asíncrona',
      instructorId: instructors.instructor1.id,
      categoryId: categories[0].id, // Desarrollo Web
      image: 'javascript.jpg',
      price: 49.99,
      duration: '15 horas',
      rating: 4.9,
      studentsCount: 875,
      isPublished: true,
      slug: 'javascript-avanzado'
    },
    {
      title: 'Diseño UX/UI desde cero',
      description: 'Aprende a diseñar interfaces de usuario efectivas y con buena experiencia de usuario',
      instructorId: instructors.instructor2.id,
      categoryId: categories[2].id, // Diseño
      image: 'ux-ui.jpg',
      price: 39.99,
      duration: '12 horas',
      rating: 4.6,
      studentsCount: 950,
      isPublished: true,
      slug: 'diseno-ux-ui-desde-cero'
    },
    {
      title: 'Desarrollo de Aplicaciones Móviles con React Native',
      description: 'Aprende a crear aplicaciones móviles multiplataforma con React Native',
      instructorId: instructors.instructor1.id,
      categoryId: categories[1].id, // Programación
      image: 'react-native.jpg',
      price: 59.99,
      duration: '20 horas',
      rating: 4.8,
      studentsCount: 720,
      isPublished: false,
      slug: 'react-native-apps'
    }
  ];

  const createdCourses = await Promise.all(
    courses.map(course => db.Course.create(course))
  );
  
  console.log('Cursos creados correctamente');
  return createdCourses;
};

// Función para crear lecciones iniciales
const createLessons = async (courses) => {
  const lessons = [
    // Lecciones para el curso de HTML y CSS
    {
      courseId: courses[0].id,
      title: 'Introducción a HTML',
      description: 'Aprende los conceptos básicos de HTML',
      content: 'HTML (HyperText Markup Language) es el lenguaje estándar para crear páginas web...',
      videoUrl: 'https://example.com/videos/html-intro',
      duration: '45 minutos',
      order: 1,
      isPublished: true
    },
    {
      courseId: courses[0].id,
      title: 'Estructura básica de una página HTML',
      description: 'Aprende cómo estructurar correctamente una página HTML',
      content: 'Toda página HTML debe comenzar con una declaración DOCTYPE...',
      videoUrl: 'https://example.com/videos/html-structure',
      duration: '50 minutos',
      order: 2,
      isPublished: true
    },
    {
      courseId: courses[0].id,
      title: 'Introducción a CSS',
      description: 'Aprende a dar estilo a tus páginas web con CSS',
      content: 'CSS (Cascading Style Sheets) es un lenguaje utilizado para describir la presentación de un documento HTML...',
      videoUrl: 'https://example.com/videos/css-intro',
      duration: '55 minutos',
      order: 3,
      isPublished: true
    },
    
    // Lecciones para el curso de JavaScript Avanzado
    {
      courseId: courses[1].id,
      title: 'Introducción a JavaScript',
      description: 'Conceptos básicos de JavaScript',
      content: 'JavaScript es un lenguaje de programación que permite implementar funcionalidades complejas en páginas web...',
      videoUrl: 'https://example.com/videos/js-intro',
      duration: '60 minutos',
      order: 1,
      isPublished: true
    },
    {
      courseId: courses[1].id,
      title: 'Promesas en JavaScript',
      description: 'Aprende a trabajar con código asíncrono en JavaScript',
      content: 'Las promesas representan un valor que puede estar disponible ahora, en el futuro, o nunca...',
      videoUrl: 'https://example.com/videos/js-promises',
      duration: '65 minutos',
      order: 2,
      isPublished: true
    }
  ];

  const createdLessons = await Promise.all(
    lessons.map(lesson => db.Lesson.create(lesson))
  );
  
  console.log('Lecciones creadas correctamente');
  return createdLessons;
};

// Función para crear cuestionarios iniciales
const createQuizzes = async (lessons) => {
  const quizzes = [
    {
      title: 'Cuestionario sobre HTML',
      description: 'Evalúa tus conocimientos de HTML',
      lessonId: lessons[1].id, // Estructura básica de HTML
      timeLimit: 15, // minutos
      passingScore: 70, // porcentaje necesario para aprobar
      isPublished: true
    },
    {
      title: 'Cuestionario sobre CSS',
      description: 'Evalúa tus conocimientos de CSS',
      lessonId: lessons[2].id, // Introducción a CSS
      timeLimit: 10,
      passingScore: 60,
      isPublished: true
    }
  ];

  const createdQuizzes = await Promise.all(
    quizzes.map(quiz => db.Quiz.create(quiz))
  );
  
  console.log('Cuestionarios creados correctamente');
  return createdQuizzes;
};

// Función para crear preguntas y opciones
const createQuestionsAndOptions = async (quizzes) => {
  // Preguntas para el quiz de HTML
  const htmlQuizQuestions = [
    {
      quizId: quizzes[0].id,
      text: '¿Qué significa HTML?',
      type: 'multiple-choice',
      order: 1,
      options: [
        { text: 'Hyper Text Markup Language', isCorrect: true, order: 1 },
        { text: 'High Technology Modern Language', isCorrect: false, order: 2 },
        { text: 'Hyperlink and Text Markup Language', isCorrect: false, order: 3 },
        { text: 'Home Tool Markup Language', isCorrect: false, order: 4 }
      ]
    },
    {
      quizId: quizzes[0].id,
      text: '¿Cuál de estos elementos HTML define un encabezado de nivel 1?',
      type: 'multiple-choice',
      order: 2,
      options: [
        { text: '<heading>', isCorrect: false, order: 1 },
        { text: '<h1>', isCorrect: true, order: 2 },
        { text: '<head>', isCorrect: false, order: 3 },
        { text: '<header>', isCorrect: false, order: 4 }
      ]
    }
  ];

  // Preguntas para el quiz de CSS
  const cssQuizQuestions = [
    {
      quizId: quizzes[1].id,
      text: '¿Qué significa CSS?',
      type: 'multiple-choice',
      order: 1,
      options: [
        { text: 'Creative Style Sheets', isCorrect: false, order: 1 },
        { text: 'Computer Style Sheets', isCorrect: false, order: 2 },
        { text: 'Cascading Style Sheets', isCorrect: true, order: 3 },
        { text: 'Colorful Style Sheets', isCorrect: false, order: 4 }
      ]
    },
    {
      quizId: quizzes[1].id,
      text: '¿Cuál es la forma correcta de aplicar un estilo a todos los elementos <p>?',
      type: 'multiple-choice',
      order: 2,
      options: [
        { text: 'p { }', isCorrect: true, order: 1 },
        { text: '.p { }', isCorrect: false, order: 2 },
        { text: '#p { }', isCorrect: false, order: 3 },
        { text: 'all.p { }', isCorrect: false, order: 4 }
      ]
    }
  ];

  // Crear preguntas y opciones para ambos quizzes
  for (const question of [...htmlQuizQuestions, ...cssQuizQuestions]) {
    const options = question.options;
    delete question.options;
    
    const createdQuestion = await db.Question.create(question);
    
    // Crear opciones para la pregunta
    for (const option of options) {
      await db.Option.create({
        ...option,
        questionId: createdQuestion.id
      });
    }
  }
  
  console.log('Preguntas y opciones creadas correctamente');
};

// Función para crear matrículas (enrollments)
const createEnrollments = async (students, courses) => {
  const enrollments = [
    {
      studentId: students.student1.id,
      courseId: courses[0].id, // HTML y CSS
      progress: 75,
      completed: false,
      enrollmentDate: new Date('2023-01-10')
    },
    {
      studentId: students.student1.id,
      courseId: courses[1].id, // JavaScript
      progress: 30,
      completed: false,
      enrollmentDate: new Date('2023-02-15')
    },
    {
      studentId: students.student2.id,
      courseId: courses[0].id, // HTML y CSS
      progress: 100,
      completed: true,
      enrollmentDate: new Date('2023-01-05'),
      completionDate: new Date('2023-02-20')
    },
    {
      studentId: students.student3.id,
      courseId: courses[2].id, // Diseño UX/UI
      progress: 10,
      completed: false,
      enrollmentDate: new Date('2023-03-20')
    }
  ];

  const createdEnrollments = await Promise.all(
    enrollments.map(enrollment => db.Enrollment.create(enrollment))
  );
  
  console.log('Matrículas creadas correctamente');
  return createdEnrollments;
};

// Función principal para ejecutar todas las migraciones
const seedDatabase = async () => {
  try {
    // Crear datos en orden
    const users = await createUsers();
    const createdCategories = await createCategories();
    const courses = await createCourses(users, createdCategories);
    const lessons = await createLessons(courses);
    const quizzes = await createQuizzes(lessons);
    await createQuestionsAndOptions(quizzes);
    await createEnrollments(users, courses);

    console.log('¡Base de datos poblada exitosamente!');
    process.exit(0);
  } catch (error) {
    console.error('Error al poblar la base de datos:', error);
    process.exit(1);
  }
};

// Ejecutar la función de seeding si este archivo se ejecuta directamente
if (require.main === module) {
  seedDatabase();
}

module.exports = {
  seedDatabase,
  createUsers,
  createCategories,
  createCourses,
  createLessons,
  createQuizzes,
  createQuestionsAndOptions,
  createEnrollments
};
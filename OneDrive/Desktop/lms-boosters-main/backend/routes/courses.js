const express = require('express');
const router = express.Router();

// Datos de ejemplo para pruebas
const dummyCourses = [
  { 
    id: 1, 
    title: 'Introducción a React', 
    description: 'Aprende los fundamentos de React',
    price: 29.99,
    isPublished: true,
    category: {
      id: 1,
      name: 'Desarrollo Web'
    },
    isPremium: false,
    showInCatalog: true,
    instructor: {
      id: 1,
      name: 'Ana García'
    },
    lessons: 10,
    students: 42,
    status: 'Published',
    createdAt: '2023-01-01T00:00:00Z'
  },
  { 
    id: 2, 
    title: 'JavaScript Avanzado', 
    description: 'Domina JavaScript y sus características avanzadas',
    price: 39.99,
    isPublished: true,
    category: {
      id: 2,
      name: 'Programación'
    },
    isPremium: true,
    showInCatalog: true,
    instructor: {
      id: 2,
      name: 'Carlos Rodríguez'
    },
    lessons: 15,
    students: 28,
    status: 'Published',
    createdAt: '2023-02-01T00:00:00Z'
  }
];

// @desc    Obtener todos los cursos
// @route   GET /api/courses
// @access  Public
router.get('/', (req, res) => {
  res.status(200).json({
    success: true,
    count: dummyCourses.length,
    data: dummyCourses
  });
});

// @desc    Obtener un curso
// @route   GET /api/courses/:id
// @access  Public
router.get('/:id', (req, res) => {
  const course = dummyCourses.find(c => c.id === parseInt(req.params.id));
  
  if (!course) {
    return res.status(404).json({
      success: false,
      message: 'Curso no encontrado'
    });
  }
  
  res.status(200).json({
    success: true,
    data: course
  });
});

// @desc    Crear un curso
// @route   POST /api/courses
// @access  Private
router.post('/', (req, res) => {
  // Simulación de creación
  const newCourse = {
    id: dummyCourses.length + 1,
    ...req.body,
    createdAt: new Date().toISOString()
  };
  
  res.status(201).json({
    success: true,
    data: newCourse
  });
});

// @desc    Actualizar un curso
// @route   PUT /api/courses/:id
// @access  Private
router.put('/:id', (req, res) => {
  const course = dummyCourses.find(c => c.id === parseInt(req.params.id));
  
  if (!course) {
    return res.status(404).json({
      success: false,
      message: 'Curso no encontrado'
    });
  }
  
  // Simulación de actualización
  const updatedCourse = {
    ...course,
    ...req.body
  };
  
  res.status(200).json({
    success: true,
    data: updatedCourse
  });
});

// @desc    Eliminar un curso
// @route   DELETE /api/courses/:id
// @access  Private
router.delete('/:id', (req, res) => {
  const course = dummyCourses.find(c => c.id === parseInt(req.params.id));
  
  if (!course) {
    return res.status(404).json({
      success: false,
      message: 'Curso no encontrado'
    });
  }
  
  // En una implementación real, aquí eliminaríamos el curso
  
  res.status(200).json({
    success: true,
    data: {}
  });
});

// @desc    Obtener lecciones de un curso
// @route   GET /api/courses/:id/lessons
// @access  Public/Private
router.get('/:id/lessons', (req, res) => {
  const course = dummyCourses.find(c => c.id === parseInt(req.params.id));
  
  if (!course) {
    return res.status(404).json({
      success: false,
      message: 'Curso no encontrado'
    });
  }
  
  // Lecciones de ejemplo
  const lessons = [
    {
      id: 1,
      title: 'Introducción al curso',
      content: 'Bienvenidos al curso...',
      courseId: course.id,
      order: 1,
      duration: '15:00',
      status: 'Published'
    },
    {
      id: 2,
      title: 'Primeros pasos',
      content: 'En esta lección...',
      courseId: course.id,
      order: 2,
      duration: '22:30',
      status: 'Published'
    }
  ];
  
  res.status(200).json({
    success: true,
    count: lessons.length,
    data: lessons
  });
});

// @desc    Obtener cursos por instructor
// @route   GET /api/courses/instructor/:instructorId
// @access  Public
router.get('/instructor/:instructorId', (req, res) => {
  const instructorId = parseInt(req.params.instructorId);
  const courses = dummyCourses.filter(c => c.instructor.id === instructorId);
  
  res.status(200).json({
    success: true,
    count: courses.length,
    data: courses
  });
});

module.exports = router;
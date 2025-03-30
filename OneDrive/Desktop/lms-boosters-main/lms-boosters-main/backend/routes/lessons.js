const express = require('express');
const router = express.Router();

// Datos de ejemplo para pruebas
const dummyLessons = [
  { 
    id: 1, 
    title: 'Introducción a HTML', 
    content: 'HTML es el lenguaje de marcado estándar para crear páginas web...',
    course: {
      id: 1,
      title: 'Desarrollo Web'
    },
    order: 1,
    duration: '45 min',
    status: 'Published'
  },
  { 
    id: 2, 
    title: 'CSS Básico', 
    content: 'CSS (Cascading Style Sheets) es el lenguaje que utilizamos para dar estilo...',
    course: {
      id: 1,
      title: 'Desarrollo Web'
    },
    order: 2,
    duration: '60 min',
    status: 'Published'
  },
  { 
    id: 3, 
    title: 'JavaScript Fundamentos', 
    content: 'JavaScript es un lenguaje de programación que permite implementar...',
    course: {
      id: 2,
      title: 'Programación'
    },
    order: 1,
    duration: '75 min',
    status: 'Draft'
  }
];

// @desc    Obtener todas las lecciones
// @route   GET /api/lessons
// @access  Private (Admin)
router.get('/', (req, res) => {
  let filteredLessons = [...dummyLessons];
  
  // Filtrado por curso
  if (req.query.course) {
    const courseId = parseInt(req.query.course);
    filteredLessons = filteredLessons.filter(lesson => lesson.course.id === courseId);
  }
  
  // Filtrado por estado
  if (req.query.status) {
    filteredLessons = filteredLessons.filter(lesson => lesson.status === req.query.status);
  }
  
  res.status(200).json({
    success: true,
    count: filteredLessons.length,
    data: filteredLessons
  });
});

// @desc    Obtener una lección
// @route   GET /api/lessons/:id
// @access  Public/Private
router.get('/:id', (req, res) => {
  const lesson = dummyLessons.find(l => l.id === parseInt(req.params.id));
  
  if (!lesson) {
    return res.status(404).json({
      success: false,
      message: 'Lección no encontrada'
    });
  }
  
  res.status(200).json({
    success: true,
    data: lesson
  });
});

// @desc    Crear una lección
// @route   POST /api/lessons
// @access  Private (Instructor, Admin)
router.post('/', (req, res) => {
  // Simulación de creación
  const newLesson = {
    id: dummyLessons.length + 1,
    ...req.body,
    createdAt: new Date().toISOString()
  };
  
  res.status(201).json({
    success: true,
    data: newLesson
  });
});

// @desc    Actualizar una lección
// @route   PUT /api/lessons/:id
// @access  Private (Instructor, Admin)
router.put('/:id', (req, res) => {
  const lesson = dummyLessons.find(l => l.id === parseInt(req.params.id));
  
  if (!lesson) {
    return res.status(404).json({
      success: false,
      message: 'Lección no encontrada'
    });
  }
  
  // Simulación de actualización
  const updatedLesson = {
    ...lesson,
    ...req.body
  };
  
  res.status(200).json({
    success: true,
    data: updatedLesson
  });
});

// @desc    Eliminar una lección
// @route   DELETE /api/lessons/:id
// @access  Private (Instructor, Admin)
router.delete('/:id', (req, res) => {
  const lesson = dummyLessons.find(l => l.id === parseInt(req.params.id));
  
  if (!lesson) {
    return res.status(404).json({
      success: false,
      message: 'Lección no encontrada'
    });
  }
  
  // En una implementación real, aquí eliminaríamos la lección
  
  res.status(200).json({
    success: true,
    data: {}
  });
});

module.exports = router;
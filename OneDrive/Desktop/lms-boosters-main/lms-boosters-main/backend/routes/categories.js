const express = require('express');
const router = express.Router();

// Datos de ejemplo para pruebas
const dummyCategories = [
  { 
    id: 1, 
    name: 'Desarrollo Web', 
    slug: 'desarrollo-web',
    description: 'Cursos relacionados con el desarrollo web',
    icon: 'fa-code',
    coursesCount: 12
  },
  { 
    id: 2, 
    name: 'Programación', 
    slug: 'programacion',
    description: 'Cursos de lenguajes de programación',
    icon: 'fa-laptop-code',
    coursesCount: 8
  },
  { 
    id: 3, 
    name: 'Diseño', 
    slug: 'diseno',
    description: 'Cursos de diseño gráfico y UX/UI',
    icon: 'fa-pencil-ruler',
    coursesCount: 5
  },
  { 
    id: 4, 
    name: 'Marketing Digital', 
    slug: 'marketing-digital',
    description: 'Cursos de marketing online y estrategias digitales',
    icon: 'fa-bullhorn',
    coursesCount: 7
  },
  { 
    id: 5, 
    name: 'Idiomas', 
    slug: 'idiomas',
    description: 'Cursos para aprender diferentes idiomas',
    icon: 'fa-language',
    coursesCount: 4
  }
];

// @desc    Obtener todas las categorías
// @route   GET /api/categories
// @access  Public
router.get('/', (req, res) => {
  res.status(200).json({
    success: true,
    count: dummyCategories.length,
    data: dummyCategories
  });
});

// @desc    Obtener una categoría
// @route   GET /api/categories/:id
// @access  Public
router.get('/:id', (req, res) => {
  const category = dummyCategories.find(c => c.id === parseInt(req.params.id));
  
  if (!category) {
    return res.status(404).json({
      success: false,
      message: 'Categoría no encontrada'
    });
  }
  
  res.status(200).json({
    success: true,
    data: category
  });
});

// @desc    Crear una categoría
// @route   POST /api/categories
// @access  Private (Admin)
router.post('/', (req, res) => {
  // Simulación de creación
  const newCategory = {
    id: dummyCategories.length + 1,
    ...req.body,
    coursesCount: 0,
    createdAt: new Date().toISOString()
  };
  
  res.status(201).json({
    success: true,
    data: newCategory
  });
});

// @desc    Actualizar una categoría
// @route   PUT /api/categories/:id
// @access  Private (Admin)
router.put('/:id', (req, res) => {
  const category = dummyCategories.find(c => c.id === parseInt(req.params.id));
  
  if (!category) {
    return res.status(404).json({
      success: false,
      message: 'Categoría no encontrada'
    });
  }
  
  // Simulación de actualización
  const updatedCategory = {
    ...category,
    ...req.body
  };
  
  res.status(200).json({
    success: true,
    data: updatedCategory
  });
});

// @desc    Eliminar una categoría
// @route   DELETE /api/categories/:id
// @access  Private (Admin)
router.delete('/:id', (req, res) => {
  const category = dummyCategories.find(c => c.id === parseInt(req.params.id));
  
  if (!category) {
    return res.status(404).json({
      success: false,
      message: 'Categoría no encontrada'
    });
  }
  
  // En una implementación real, aquí verificaríamos si hay cursos asociados
  // y eliminaríamos la categoría
  
  res.status(200).json({
    success: true,
    data: {}
  });
});

// @desc    Obtener cursos por categoría
// @route   GET /api/categories/:id/courses
// @access  Public
router.get('/:id/courses', (req, res) => {
  const category = dummyCategories.find(c => c.id === parseInt(req.params.id));
  
  if (!category) {
    return res.status(404).json({
      success: false,
      message: 'Categoría no encontrada'
    });
  }
  
  // Cursos de ejemplo para la categoría
  const courses = [
    {
      id: 1,
      title: `Curso de ${category.name} 1`,
      description: `Descripción del curso de ${category.name} 1`,
      price: 29.99,
      instructor: {
        id: 1,
        name: 'Instructor Demo'
      }
    },
    {
      id: 2,
      title: `Curso de ${category.name} 2`,
      description: `Descripción del curso de ${category.name} 2`,
      price: 39.99,
      instructor: {
        id: 2,
        name: 'Otro Instructor'
      }
    }
  ];
  
  res.status(200).json({
    success: true,
    count: courses.length,
    data: courses
  });
});

module.exports = router;
const express = require('express');
const router = express.Router();

// Datos de ejemplo para pruebas
const dummyInstructors = [
  { 
    id: 1, 
    name: 'Ana García', 
    email: 'ana.garcia@ejemplo.com',
    bio: 'Desarrolladora web con más de 10 años de experiencia',
    phone: '+34600111222',
    isActive: true,
    expertise: 'Desarrollo Web, React, Node.js',
    website: 'https://anagarcia.dev',
    avatar: '',
    courses: 3,
    rating: 4.8,
    status: 'Active'
  },
  { 
    id: 2, 
    name: 'Carlos Rodríguez', 
    email: 'carlos.rodriguez@ejemplo.com',
    bio: 'Especialista en JavaScript y arquitecturas frontend',
    phone: '+34600333444',
    isActive: true,
    expertise: 'JavaScript, Vue.js, Angular',
    website: 'https://carlosrodriguez.io',
    avatar: '',
    courses: 2,
    rating: 4.5,
    status: 'Active'
  },
  { 
    id: 3, 
    name: 'Laura Martínez', 
    email: 'laura.martinez@ejemplo.com',
    bio: 'Diseñadora UX/UI y experta en experiencia de usuario',
    phone: '+34600555666',
    isActive: true,
    expertise: 'Diseño UX/UI, Figma, Adobe XD',
    website: 'https://lauramartinez.design',
    avatar: '',
    courses: 4,
    rating: 4.9,
    status: 'Active'
  }
];

// @desc    Obtener todos los instructores
// @route   GET /api/instructors
// @access  Public
router.get('/', (req, res) => {
  let filteredInstructors = [...dummyInstructors];
  
  // Filtrado por estado activo
  if (req.query.active !== undefined) {
    const isActive = req.query.active === 'true';
    filteredInstructors = filteredInstructors.filter(instructor => instructor.isActive === isActive);
  }
  
  res.status(200).json({
    success: true,
    count: filteredInstructors.length,
    data: filteredInstructors
  });
});

// @desc    Obtener un instructor
// @route   GET /api/instructors/:id
// @access  Public
router.get('/:id', (req, res) => {
  const instructor = dummyInstructors.find(i => i.id === parseInt(req.params.id));
  
  if (!instructor) {
    return res.status(404).json({
      success: false,
      message: 'Instructor no encontrado'
    });
  }
  
  // Cursos de ejemplo para el instructor
  const courses = [
    {
      id: 1,
      title: 'Curso de ejemplo 1',
      description: 'Descripción del curso 1'
    },
    {
      id: 2,
      title: 'Curso de ejemplo 2',
      description: 'Descripción del curso 2'
    }
  ];
  
  const instructorData = {
    ...instructor,
    coursesList: courses
  };
  
  res.status(200).json({
    success: true,
    data: instructorData
  });
});

// @desc    Crear un instructor
// @route   POST /api/instructors
// @access  Private (Admin)
router.post('/', (req, res) => {
  // Verificar si el email ya existe (simulado)
  const emailExists = dummyInstructors.some(i => i.email === req.body.email);
  
  if (emailExists) {
    return res.status(400).json({
      success: false,
      message: 'El email ya está registrado'
    });
  }
  
  // Simulación de creación
  const newInstructor = {
    id: dummyInstructors.length + 1,
    ...req.body,
    courses: 0,
    rating: 0,
    status: req.body.isActive ? 'Active' : 'Inactive',
    createdAt: new Date().toISOString()
  };
  
  res.status(201).json({
    success: true,
    data: newInstructor
  });
});

// @desc    Actualizar un instructor
// @route   PUT /api/instructors/:id
// @access  Private (Admin, Instructor)
router.put('/:id', (req, res) => {
  const instructor = dummyInstructors.find(i => i.id === parseInt(req.params.id));
  
  if (!instructor) {
    return res.status(404).json({
      success: false,
      message: 'Instructor no encontrado'
    });
  }
  
  // Simulación de actualización
  const updatedInstructor = {
    ...instructor,
    ...req.body,
    status: req.body.isActive !== undefined ? (req.body.isActive ? 'Active' : 'Inactive') : instructor.status
  };
  
  res.status(200).json({
    success: true,
    data: updatedInstructor
  });
});

// @desc    Eliminar un instructor
// @route   DELETE /api/instructors/:id
// @access  Private (Admin)
router.delete('/:id', (req, res) => {
  const instructor = dummyInstructors.find(i => i.id === parseInt(req.params.id));
  
  if (!instructor) {
    return res.status(404).json({
      success: false,
      message: 'Instructor no encontrado'
    });
  }
  
  // Simulación de verificación de cursos
  if (instructor.courses > 0) {
    return res.status(400).json({
      success: false,
      message: `No se puede eliminar porque este instructor tiene ${instructor.courses} cursos asociados`
    });
  }
  
  // En una implementación real, aquí eliminaríamos el instructor
  
  res.status(200).json({
    success: true,
    data: {}
  });
});

// @desc    Subir avatar del instructor
// @route   PUT /api/instructors/:id/avatar
// @access  Private (Admin, Instructor)
router.put('/:id/avatar', (req, res) => {
  const instructor = dummyInstructors.find(i => i.id === parseInt(req.params.id));
  
  if (!instructor) {
    return res.status(404).json({
      success: false,
      message: 'Instructor no encontrado'
    });
  }
  
  // En una implementación real, aquí procesaríamos el archivo subido
  const updatedInstructor = {
    ...instructor,
    avatar: 'avatar-example.jpg'
  };
  
  res.status(200).json({
    success: true,
    data: updatedInstructor
  });
});

module.exports = router;
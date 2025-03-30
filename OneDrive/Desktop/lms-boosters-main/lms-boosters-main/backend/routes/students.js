const express = require('express');
const router = express.Router();

// Datos de ejemplo para pruebas
const dummyStudents = [
  { 
    id: 1, 
    name: 'Juan Pérez', 
    email: 'juan.perez@ejemplo.com',
    courses: 3,
    isActive: true,
    status: 'Active'
  },
  { 
    id: 2, 
    name: 'María López', 
    email: 'maria.lopez@ejemplo.com',
    courses: 2,
    isActive: true,
    status: 'Active'
  },
  { 
    id: 3, 
    name: 'Pedro Sánchez', 
    email: 'pedro.sanchez@ejemplo.com',
    courses: 1,
    isActive: false,
    status: 'Inactive'
  },
  { 
    id: 4, 
    name: 'Ana Martínez', 
    email: 'ana.martinez@ejemplo.com',
    courses: 4,
    isActive: true,
    status: 'Active'
  },
  { 
    id: 5, 
    name: 'Carlos Rodríguez', 
    email: 'carlos.rodriguez@ejemplo.com',
    courses: 0,
    isActive: false,
    status: 'Inactive'
  }
];

// Datos de ejemplo de matrículas
const dummyEnrollments = [
  {
    id: 1,
    student: 1,
    course: 1,
    progress: 75,
    completed: false,
    enrollmentDate: '2023-01-10T00:00:00Z'
  },
  {
    id: 2,
    student: 1,
    course: 2,
    progress: 30,
    completed: false,
    enrollmentDate: '2023-02-15T00:00:00Z'
  },
  {
    id: 3,
    student: 2,
    course: 1,
    progress: 100,
    completed: true,
    enrollmentDate: '2023-01-05T00:00:00Z'
  },
  {
    id: 4,
    student: 3,
    course: 3,
    progress: 10,
    completed: false,
    enrollmentDate: '2023-03-20T00:00:00Z'
  },
  {
    id: 5,
    student: 4,
    course: 1,
    progress: 50,
    completed: false,
    enrollmentDate: '2023-02-10T00:00:00Z'
  }
];

// Referencia a los datos de ejemplo (en producción esto sería reemplazado por operaciones de base de datos)
let students = [...dummyStudents];

/**
 * GET /api/students
 * Obtiene todos los estudiantes
 */
router.get('/', (req, res) => {
  // Opción para filtrar por estado activo/inactivo
  const { active } = req.query;
  
  let result = [...students];
  
  if (active !== undefined) {
    const isActive = active === 'true';
    result = result.filter(student => student.isActive === isActive);
  }
  
  res.status(200).json({
    success: true,
    count: result.length,
    data: result
  });
});

/**
 * GET /api/students/:id
 * Obtiene un estudiante por ID
 */
router.get('/:id', (req, res) => {
  const id = parseInt(req.params.id);
  const student = students.find(s => s.id === id);
  
  if (!student) {
    return res.status(404).json({
      success: false,
      error: `No se encontró el estudiante con ID ${id}`
    });
  }
  
  res.status(200).json({
    success: true,
    data: student
  });
});

/**
 * POST /api/students
 * Crea un nuevo estudiante
 */
router.post('/', (req, res) => {
  const { name, email, courses = 0, isActive = true } = req.body;
  
  // Validación básica
  if (!name || !email) {
    return res.status(400).json({
      success: false,
      error: 'Por favor proporcione nombre y email'
    });
  }
  
  // Verificar si el email ya existe
  if (students.some(s => s.email === email)) {
    return res.status(400).json({
      success: false,
      error: 'Ya existe un estudiante con ese email'
    });
  }
  
  // Crear el nuevo estudiante
  const newStudent = {
    id: students.length > 0 ? Math.max(...students.map(s => s.id)) + 1 : 1,
    name,
    email,
    courses,
    isActive,
    status: isActive ? 'Active' : 'Inactive'
  };
  
  students.push(newStudent);
  
  res.status(201).json({
    success: true,
    data: newStudent
  });
});

/**
 * PUT /api/students/:id
 * Actualiza un estudiante existente
 */
router.put('/:id', (req, res) => {
  const id = parseInt(req.params.id);
  const studentIndex = students.findIndex(s => s.id === id);
  
  if (studentIndex === -1) {
    return res.status(404).json({
      success: false,
      error: `No se encontró el estudiante con ID ${id}`
    });
  }
  
  const { name, email, courses, isActive } = req.body;
  const updatedStudent = {
    ...students[studentIndex],
    name: name || students[studentIndex].name,
    email: email || students[studentIndex].email,
    courses: courses !== undefined ? courses : students[studentIndex].courses,
    isActive: isActive !== undefined ? isActive : students[studentIndex].isActive
  };
  
  // Actualizar el estado basado en isActive
  updatedStudent.status = updatedStudent.isActive ? 'Active' : 'Inactive';
  
  // Verificar si el email actualizado ya existe en otro estudiante
  if (email && email !== students[studentIndex].email &&
      students.some(s => s.email === email && s.id !== id)) {
    return res.status(400).json({
      success: false,
      error: 'Ya existe otro estudiante con ese email'
    });
  }
  
  students[studentIndex] = updatedStudent;
  
  res.status(200).json({
    success: true,
    data: updatedStudent
  });
});

/**
 * DELETE /api/students/:id
 * Elimina un estudiante
 */
router.delete('/:id', (req, res) => {
  const id = parseInt(req.params.id);
  const studentIndex = students.findIndex(s => s.id === id);
  
  if (studentIndex === -1) {
    return res.status(404).json({
      success: false,
      error: `No se encontró el estudiante con ID ${id}`
    });
  }
  
  const deletedStudent = students[studentIndex];
  students = students.filter(s => s.id !== id);
  
  res.status(200).json({
    success: true,
    data: deletedStudent,
    message: `Estudiante con ID ${id} eliminado correctamente`
  });
});

/**
 * GET /api/students/:id/enrollments
 * Obtiene las matrículas de un estudiante
 */
router.get('/:id/enrollments', (req, res) => {
  const id = parseInt(req.params.id);
  
  // Verificar si el estudiante existe
  if (!students.some(s => s.id === id)) {
    return res.status(404).json({
      success: false,
      error: `No se encontró el estudiante con ID ${id}`
    });
  }
  
  // Obtener matrículas del estudiante
  const studentEnrollments = dummyEnrollments.filter(e => e.student === id);
  
  res.status(200).json({
    success: true,
    count: studentEnrollments.length,
    data: studentEnrollments
  });
});

module.exports = router;
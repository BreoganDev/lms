// controllers/students.js
const User = require('../models/User');
const Course = require('../models/Course');
const Enrollment = require('../models/Enrollment');

// @desc    Obtener todos los estudiantes
// @route   GET /api/students
// @access  Private (Admin, Instructor)
exports.getStudents = async (req, res, next) => {
  try {
    let query = User.find({ role: 'student' });

    // Filtrado
    if (req.query.active !== undefined) {
      query = query.where('isActive').equals(req.query.active === 'true');
    }

    // Para instructores, filtrar solo sus estudiantes
    if (req.user.role === 'instructor') {
      // Obtener los IDs de los cursos del instructor
      const courses = await Course.find({ instructor: req.user.id }).select('_id');
      const courseIds = courses.map(course => course._id);
      
      // Obtener IDs de estudiantes inscritos en esos cursos
      const enrollments = await Enrollment.find({ course: { $in: courseIds } }).select('student');
      const studentIds = [...new Set(enrollments.map(enrollment => enrollment.student.toString()))];
      
      // Filtrar estudiantes por esos IDs
      query = query.where('_id').in(studentIds);
    }

    // Ejecutar consulta
    const students = await query.select('-password');

    // Obtener información adicional para cada estudiante
    const enhancedStudents = await Promise.all(
      students.map(async (student) => {
        // Contar matrículas
        const enrollmentCount = await Enrollment.countDocuments({ student: student._id });
        
        // Obtener estado
        const status = student.isActive ? 'Active' : 'Inactive';
        
        return {
          id: student._id,
          name: student.name,
          email: student.email,
          courses: enrollmentCount,
          status,
          createdAt: student.createdAt
        };
      })
    );

    res.status(200).json({
      success: true,
      count: enhancedStudents.length,
      data: enhancedStudents
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Obtener un estudiante
// @route   GET /api/students/:id
// @access  Private (Admin, Instructor si es su estudiante)
exports.getStudent = async (req, res, next) => {
  try {
    const student = await User.findOne({ 
      _id: req.params.id,
      role: 'student' 
    }).select('-password');

    if (!student) {
      return res.status(404).json({
        success: false,
        message: 'Estudiante no encontrado'
      });
    }

    // Para instructores, verificar si es su estudiante
    if (req.user.role === 'instructor') {
      // Obtener los IDs de los cursos del instructor
      const courses = await Course.find({ instructor: req.user.id }).select('_id');
      const courseIds = courses.map(course => course._id);
      
      // Verificar si el estudiante está inscrito en alguno de esos cursos
      const enrollment = await Enrollment.findOne({
        student: req.params.id,
        course: { $in: courseIds }
      });
      
      if (!enrollment) {
        return res.status(403).json({
          success: false,
          message: 'No autorizado para ver este estudiante'
        });
      }
    }

    // Obtener cursos en los que está matriculado
    const enrollments = await Enrollment.find({ student: req.params.id })
      .populate({
        path: 'course',
        select: 'title'
      });
      
    const enrolledCourses = enrollments.map(enrollment => ({
      id: enrollment.course._id,
      title: enrollment.course.title,
      progress: enrollment.progress,
      completed: enrollment.completed,
      enrollmentDate: enrollment.enrollmentDate
    }));

    const studentData = {
      id: student._id,
      name: student.name,
      email: student.email,
      isActive: student.isActive,
      enrolledCourses,
      createdAt: student.createdAt
    };

    res.status(200).json({
      success: true,
      data: studentData
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Crear un estudiante
// @route   POST /api/students
// @access  Private (Admin)
exports.createStudent = async (req, res, next) => {
  try {
    // Verificar si el email ya existe
    const existingUser = await User.findOne({ email: req.body.email });
    
    if (existingUser) {
      return res.status(400).json({
        success: false,
        message: 'El email ya está registrado'
      });
    }

    // Asegurarse de que el rol sea estudiante
    req.body.role = 'student';

    // Crear estudiante
    const student = await User.create(req.body);

    res.status(201).json({
      success: true,
      data: {
        id: student._id,
        name: student.name,
        email: student.email,
        isActive: student.isActive
      }
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Actualizar un estudiante
// @route   PUT /api/students/:id
// @access  Private (Admin)
exports.updateStudent = async (req, res, next) => {
  try {
    // No permitir cambiar el rol
    delete req.body.role;
    
    // No permitir cambiar la contraseña por esta ruta
    delete req.body.password;

    const student = await User.findOneAndUpdate(
      { _id: req.params.id, role: 'student' },
      req.body,
      { new: true, runValidators: true }
    ).select('-password');

    if (!student) {
      return res.status(404).json({
        success: false,
        message: 'Estudiante no encontrado'
      });
    }

    res.status(200).json({
      success: true,
      data: student
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Eliminar un estudiante
// @route   DELETE /api/students/:id
// @access  Private (Admin)
exports.deleteStudent = async (req, res, next) => {
  try {
    const student = await User.findOneAndDelete({
      _id: req.params.id,
      role: 'student'
    });

    if (!student) {
      return res.status(404).json({
        success: false,
        message: 'Estudiante no encontrado'
      });
    }

    // Eliminar todas las matrículas del estudiante
    await Enrollment.deleteMany({ student: req.params.id });

    res.status(200).json({
      success: true,
      data: {}
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Matricular estudiante en curso
// @route   POST /api/students/:id/enroll
// @access  Private (Admin, Student si es él mismo)
exports.enrollStudent = async (req, res, next) => {
  try {
    // Verificar estudiante
    const student = await User.findOne({
      _id: req.params.id,
      role: 'student'
    });

    if (!student) {
      return res.status(404).json({
        success: false,
        message: 'Estudiante no encontrado'
      });
    }

    // Si no es admin, verificar que sea el propio estudiante
    if (req.user.role !== 'admin' && req.params.id !== req.user.id) {
      return res.status(403).json({
        success: false,
        message: 'No autorizado para matricular a este estudiante'
      });
    }

    // Verificar curso
    const course = await Course.findById(req.body.courseId);
    
    if (!course) {
      return res.status(404).json({
        success: false,
        message: 'Curso no encontrado'
      });
    }

    // Verificar si ya está matriculado
    const existingEnrollment = await Enrollment.findOne({
      student: req.params.id,
      course: req.body.courseId
    });

    if (existingEnrollment) {
      return res.status(400).json({
        success: false,
        message: 'El estudiante ya está matriculado en este curso'
      });
    }

    // Crear matrícula
    const enrollment = await Enrollment.create({
      student: req.params.id,
      course: req.body.courseId
    });

    res.status(201).json({
      success: true,
      data: enrollment
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Dar de baja a estudiante de curso
// @route   DELETE /api/students/:id/courses/:courseId
// @access  Private (Admin, Student si es él mismo)
exports.unenrollStudent = async (req, res, next) => {
  try {
    // Verificar estudiante
    const student = await User.findOne({
      _id: req.params.id,
      role: 'student'
    });

    if (!student) {
      return res.status(404).json({
        success: false,
        message: 'Estudiante no encontrado'
      });
    }

    // Si no es admin, verificar que sea el propio estudiante
    if (req.user.role !== 'admin' && req.params.id !== req.user.id) {
      return res.status(403).json({
        success: false,
        message: 'No autorizado para dar de baja a este estudiante'
      });
    }

    // Verificar matrícula
    const enrollment = await Enrollment.findOne({
      student: req.params.id,
      course: req.params.courseId
    });

    if (!enrollment) {
      return res.status(404).json({
        success: false,
        message: 'Matrícula no encontrada'
      });
    }

    // Eliminar matrícula
    await enrollment.deleteOne();

    res.status(200).json({
      success: true,
      data: {}
    });
  } catch (err) {
    next(err);
  }
};

// routes/students.js
const express = require('express');
const router = express.Router();
const {
  getStudents,
  getStudent,
  createStudent,
  updateStudent,
  deleteStudent,
  enrollStudent,
  unenrollStudent
} = require('../controllers/students');

const { protect, authorize } = require('../middleware/auth');

router
  .route('/')
  .get(protect, authorize('admin', 'instructor'), getStudents)
  .post(protect, authorize('admin'), createStudent);

router
  .route('/:id')
  .get(protect, authorize('admin', 'instructor', 'student'), getStudent)
  .put(protect, authorize('admin'), updateStudent)
  .delete(protect, authorize('admin'), deleteStudent);

router
  .route('/:id/enroll')
  .post(protect, authorize('admin', 'student'), enrollStudent);

router
  .route('/:id/courses/:courseId')
  .delete(protect, authorize('admin', 'student'), unenrollStudent);

module.exports = router;
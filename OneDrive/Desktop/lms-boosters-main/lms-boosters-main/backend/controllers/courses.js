// controllers/courses.js
const Course = require('../models/Course');
const Category = require('../models/Category');
const User = require('../models/User');
const Enrollment = require('../models/Enrollment');

// @desc    Obtener todos los cursos
// @route   GET /api/courses
// @access  Public
exports.getCourses = async (req, res, next) => {
  try {
    let query = Course.find().populate({
      path: 'category',
      select: 'name'
    }).populate({
      path: 'instructor',
      select: 'name'
    });

    // Filtrado
    if (req.query.status) {
      query = query.where('status').equals(req.query.status);
    }

    if (req.query.category) {
      query = query.where('category').equals(req.query.category);
    }

    if (req.query.instructor) {
      query = query.where('instructor').equals(req.query.instructor);
    }

    if (req.query.isPremium) {
      query = query.where('isPremium').equals(req.query.isPremium === 'true');
    }

    if (req.query.isPublished) {
      query = query.where('isPublished').equals(req.query.isPublished === 'true');
    }

    // Ejecutar consulta
    const courses = await query;

    res.status(200).json({
      success: true,
      count: courses.length,
      data: courses
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Obtener un curso
// @route   GET /api/courses/:id
// @access  Public
exports.getCourse = async (req, res, next) => {
  try {
    const course = await Course.findById(req.params.id)
      .populate({
        path: 'category',
        select: 'name'
      })
      .populate({
        path: 'instructor',
        select: 'name email'
      });

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
  } catch (err) {
    next(err);
  }
};

// @desc    Crear un curso
// @route   POST /api/courses
// @access  Private (Instructor, Admin)
exports.createCourse = async (req, res, next) => {
  try {
    // Agregar usuario a req.body
    req.body.instructor = req.user.id;

    // Validar categoria
    const category = await Category.findById(req.body.category);
    if (!category) {
      return res.status(400).json({
        success: false,
        message: 'Categoría no válida'
      });
    }

    // Crear curso
    const course = await Course.create(req.body);

    res.status(201).json({
      success: true,
      data: course
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Actualizar un curso
// @route   PUT /api/courses/:id
// @access  Private (Instructor del curso, Admin)
exports.updateCourse = async (req, res, next) => {
  try {
    let course = await Course.findById(req.params.id);

    if (!course) {
      return res.status(404).json({
        success: false,
        message: 'Curso no encontrado'
      });
    }

    // Verificar propiedad del curso
    if (course.instructor.toString() !== req.user.id && req.user.role !== 'admin') {
      return res.status(403).json({
        success: false,
        message: 'No autorizado para actualizar este curso'
      });
    }

    course = await Course.findByIdAndUpdate(req.params.id, req.body, {
      new: true,
      runValidators: true
    });

    res.status(200).json({
      success: true,
      data: course
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Eliminar un curso
// @route   DELETE /api/courses/:id
// @access  Private (Instructor del curso, Admin)
exports.deleteCourse = async (req, res, next) => {
  try {
    const course = await Course.findById(req.params.id);

    if (!course) {
      return res.status(404).json({
        success: false,
        message: 'Curso no encontrado'
      });
    }

    // Verificar propiedad del curso
    if (course.instructor.toString() !== req.user.id && req.user.role !== 'admin') {
      return res.status(403).json({
        success: false,
        message: 'No autorizado para eliminar este curso'
      });
    }

    await course.deleteOne();

    res.status(200).json({
      success: true,
      data: {}
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Obtener cursos por instructor
// @route   GET /api/courses/instructor/:instructorId
// @access  Public
exports.getInstructorCourses = async (req, res, next) => {
  try {
    const courses = await Course.find({ instructor: req.params.instructorId })
      .populate({
        path: 'category',
        select: 'name'
      });

    res.status(200).json({
      success: true,
      count: courses.length,
      data: courses
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Obtener lecciones de un curso
// @route   GET /api/courses/:id/lessons
// @access  Public/Private (depende si el curso es premium)
exports.getCourseLessons = async (req, res, next) => {
  try {
    const course = await Course.findById(req.params.id);

    if (!course) {
      return res.status(404).json({
        success: false,
        message: 'Curso no encontrado'
      });
    }

    // Verificar si el curso es premium y el usuario está matriculado o es el instructor
    if (course.isPremium) {
      // Si no hay usuario autenticado
      if (!req.user) {
        return res.status(401).json({
          success: false,
          message: 'Debe iniciar sesión para acceder a este contenido premium'
        });
      }

      // Si no es el instructor o admin
      if (course.instructor.toString() !== req.user.id && req.user.role !== 'admin') {
        // Verificar si está matriculado
        const enrollment = await Enrollment.findOne({
          student: req.user.id,
          course: req.params.id
        });

        if (!enrollment) {
          return res.status(403).json({
            success: false,
            message: 'Debe estar matriculado para acceder a este contenido premium'
          });
        }
      }
    }

    // Obtener lecciones
    const lessons = await Lesson.find({ course: req.params.id }).sort('order');

    res.status(200).json({
      success: true,
      count: lessons.length,
      data: lessons
    });
  } catch (err) {
    next(err);
  }
};

// routes/courses.js
const express = require('express');
const router = express.Router();
const {
  getCourses,
  getCourse,
  createCourse,
  updateCourse,
  deleteCourse,
  getInstructorCourses,
  getCourseLessons
} = require('../controllers/courses');

const { protect, authorize } = require('../middleware/auth');
const upload = require('../middleware/upload');

// Rutas principales
router
  .route('/')
  .get(getCourses)
  .post(protect, authorize('instructor', 'admin'), createCourse);

router
  .route('/:id')
  .get(getCourse)
  .put(protect, authorize('instructor', 'admin'), updateCourse)
  .delete(protect, authorize('instructor', 'admin'), deleteCourse);

// Rutas adicionales
router.get('/instructor/:instructorId', getInstructorCourses);
router.get('/:id/lessons', getCourseLessons);

// Ruta para subir imagen
router.put(
  '/:id/photo',
  protect,
  authorize('instructor', 'admin'),
  upload.single('image'),
  (req, res, next) => {
    if (!req.file) {
      return res.status(400).json({
        success: false,
        message: 'Por favor suba una imagen'
      });
    }
    
    // Actualizar curso con la ruta de la imagen
    Course.findByIdAndUpdate(
      req.params.id,
      { featuredImage: req.file.filename },
      { new: true }
    )
    .then(course => {
      res.status(200).json({
        success: true,
        data: course
      });
    })
    .catch(err => next(err));
  }
);

module.exports = router;
// controllers/instructors.js
const User = require('../models/User');
const Course = require('../models/Course');
const Enrollment = require('../models/Enrollment');

// @desc    Obtener todos los instructores
// @route   GET /api/instructors
// @access  Public
exports.getInstructors = async (req, res, next) => {
  try {
    let query = User.find({ role: 'instructor' });

    // Filtrado
    if (req.query.active !== undefined) {
      query = query.where('isActive').equals(req.query.active === 'true');
    }

    // Ejecutar consulta
    const instructors = await query.select('-password');

    // Obtener información adicional para cada instructor
    const enhancedInstructors = await Promise.all(
      instructors.map(async (instructor) => {
        // Contar cursos
        const courseCount = await Course.countDocuments({ instructor: instructor._id });
        
        // Calcular valoración media
        const courses = await Course.find({ instructor: instructor._id }).select('_id');
        const courseIds = courses.map(course => course._id);
        
        // Aquí se debería calcular la valoración real, pero para el ejemplo usamos un valor fijo
        let rating = 4.5;
        
        // Obtener estado
        const status = instructor.isActive ? 'Active' : 'Inactive';
        
        return {
          id: instructor._id,
          name: instructor.name,
          email: instructor.email,
          bio: instructor.bio || '',
          expertise: instructor.expertise || '',
          avatar: instructor.avatar || '',
          courses: courseCount,
          rating,
          status,
          createdAt: instructor.createdAt
        };
      })
    );

    res.status(200).json({
      success: true,
      count: enhancedInstructors.length,
      data: enhancedInstructors
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Obtener un instructor
// @route   GET /api/instructors/:id
// @access  Public
exports.getInstructor = async (req, res, next) => {
  try {
    const instructor = await User.findOne({ 
      _id: req.params.id,
      role: 'instructor' 
    }).select('-password');

    if (!instructor) {
      return res.status(404).json({
        success: false,
        message: 'Instructor no encontrado'
      });
    }

    // Contar cursos
    const courseCount = await Course.countDocuments({ instructor: instructor._id });
    
    // Obtener cursos
    const courses = await Course.find({ 
      instructor: instructor._id,
      isPublished: true,
      showInCatalog: true
    }).select('title description');
    
    // Calcular valoración media (ejemplo)
    const rating = 4.5;
    
    const instructorData = {
      id: instructor._id,
      name: instructor.name,
      email: instructor.email,
      bio: instructor.bio || '',
      expertise: instructor.expertise || '',
      website: instructor.website || '',
      avatar: instructor.avatar || '',
      isActive: instructor.isActive,
      courses: courseCount,
      coursesList: courses,
      rating,
      createdAt: instructor.createdAt
    };

    res.status(200).json({
      success: true,
      data: instructorData
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Crear un instructor
// @route   POST /api/instructors
// @access  Private (Admin)
exports.createInstructor = async (req, res, next) => {
  try {
    // Verificar si el email ya existe
    const existingUser = await User.findOne({ email: req.body.email });
    
    if (existingUser) {
      return res.status(400).json({
        success: false,
        message: 'El email ya está registrado'
      });
    }

    // Asegurarse de que el rol sea instructor
    req.body.role = 'instructor';

    // Crear instructor
    const instructor = await User.create(req.body);

    res.status(201).json({
      success: true,
      data: {
        id: instructor._id,
        name: instructor.name,
        email: instructor.email,
        bio: instructor.bio || '',
        expertise: instructor.expertise || '',
        isActive: instructor.isActive
      }
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Actualizar un instructor
// @route   PUT /api/instructors/:id
// @access  Private (Admin, Instructor si es él mismo)
exports.updateInstructor = async (req, res, next) => {
  try {
    // No permitir cambiar el rol
    delete req.body.role;
    
    // No permitir cambiar la contraseña por esta ruta
    delete req.body.password;

    // Verificar si es el propio instructor o un admin
    if (req.user.role !== 'admin' && req.params.id !== req.user.id) {
      return res.status(403).json({
        success: false,
        message: 'No autorizado para actualizar este instructor'
      });
    }

    const instructor = await User.findOneAndUpdate(
      { _id: req.params.id, role: 'instructor' },
      req.body,
      { new: true, runValidators: true }
    ).select('-password');

    if (!instructor) {
      return res.status(404).json({
        success: false,
        message: 'Instructor no encontrado'
      });
    }

    res.status(200).json({
      success: true,
      data: instructor
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Eliminar un instructor
// @route   DELETE /api/instructors/:id
// @access  Private (Admin)
exports.deleteInstructor = async (req, res, next) => {
  try {
    // Verificar si hay cursos asociados
    const courseCount = await Course.countDocuments({ instructor: req.params.id });
    
    if (courseCount > 0) {
      return res.status(400).json({
        success: false,
        message: `No se puede eliminar porque este instructor tiene ${courseCount} cursos asociados`
      });
    }

    const instructor = await User.findOneAndDelete({
      _id: req.params.id,
      role: 'instructor'
    });

    if (!instructor) {
      return res.status(404).json({
        success: false,
        message: 'Instructor no encontrado'
      });
    }

    res.status(200).json({
      success: true,
      data: {}
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Subir avatar del instructor
// @route   PUT /api/instructors/:id/avatar
// @access  Private (Admin, Instructor si es él mismo)
exports.uploadAvatar = async (req, res, next) => {
  try {
    // Verificar si es el propio instructor o un admin
    if (req.user.role !== 'admin' && req.params.id !== req.user.id) {
      return res.status(403).json({
        success: false,
        message: 'No autorizado para actualizar este instructor'
      });
    }

    if (!req.file) {
      return res.status(400).json({
        success: false,
        message: 'Por favor suba una imagen'
      });
    }

    // Actualizar avatar
    const instructor = await User.findOneAndUpdate(
      { _id: req.params.id, role: 'instructor' },
      { avatar: req.file.filename },
      { new: true }
    ).select('-password');

    if (!instructor) {
      return res.status(404).json({
        success: false,
        message: 'Instructor no encontrado'
      });
    }

    res.status(200).json({
      success: true,
      data: instructor
    });
  } catch (err) {
    next(err);
  }
};

// routes/instructors.js
const express = require('express');
const router = express.Router();
const {
  getInstructors,
  getInstructor,
  createInstructor,
  updateInstructor,
  deleteInstructor,
  uploadAvatar
} = require('../controllers/instructors');

const { protect, authorize } = require('../middleware/auth');
const upload = require('../middleware/upload');

router
  .route('/')
  .get(getInstructors)
  .post(protect, authorize('admin'), createInstructor);

router
  .route('/:id')
  .get(getInstructor)
  .put(protect, authorize('admin', 'instructor'), updateInstructor)
  .delete(protect, authorize('admin'), deleteInstructor);

router
  .route('/:id/avatar')
  .put(
    protect,
    authorize('admin', 'instructor'),
    upload.single('avatar'),
    uploadAvatar
  );

module.exports = router;
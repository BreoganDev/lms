// controllers/quizzes.js
const Quiz = require('../models/Quiz');
const QuizSubmission = require('../models/QuizSubmission');
const Course = require('../models/Course');
const Enrollment = require('../models/Enrollment');

// @desc    Obtener todos los cuestionarios
// @route   GET /api/quizzes
// @access  Private (Admin, Instructor)
exports.getQuizzes = async (req, res, next) => {
  try {
    let query;

    // Si es admin, puede ver todos los cuestionarios
    if (req.user.role === 'admin') {
      query = Quiz.find();
    } 
    // Si es instructor, solo ve sus propios cuestionarios
    else if (req.user.role === 'instructor') {
      // Primero obtener los IDs de los cursos del instructor
      const courses = await Course.find({ instructor: req.user.id }).select('_id');
      const courseIds = courses.map(course => course._id);
      
      // Luego buscar los cuestionarios de esos cursos
      query = Quiz.find({ course: { $in: courseIds } });
    } else {
      return res.status(403).json({
        success: false,
        message: 'No autorizado para ver cuestionarios'
      });
    }

    // Filtrado
    if (req.query.course) {
      query = query.where('course').equals(req.query.course);
    }

    if (req.query.status) {
      query = query.where('status').equals(req.query.status);
    }

    // Agregar referencias
    query = query.populate({
      path: 'course',
      select: 'title'
    });

    // Ejecutar consulta
    const quizzes = await query;

    res.status(200).json({
      success: true,
      count: quizzes.length,
      data: quizzes
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Obtener un cuestionario
// @route   GET /api/quizzes/:id
// @access  Public/Private (depende si está matriculado)
exports.getQuiz = async (req, res, next) => {
  try {
    const quiz = await Quiz.findById(req.params.id).populate({
      path: 'course',
      select: 'title isPremium instructor'
    });

    if (!quiz) {
      return res.status(404).json({
        success: false,
        message: 'Cuestionario no encontrado'
      });
    }

    // Verificar si el curso es premium y el usuario está matriculado o es el instructor
    if (quiz.course.isPremium) {
      // Si no hay usuario autenticado
      if (!req.user) {
        return res.status(401).json({
          success: false,
          message: 'Debe iniciar sesión para acceder a este cuestionario'
        });
      }

      // Si no es el instructor o admin
      if (quiz.course.instructor.toString() !== req.user.id && req.user.role !== 'admin') {
        // Verificar si está matriculado
        const enrollment = await Enrollment.findOne({
          student: req.user.id,
          course: quiz.course._id
        });

        if (!enrollment) {
          return res.status(403).json({
            success: false,
            message: 'Debe estar matriculado para acceder a este cuestionario'
          });
        }
      }
    }

    // Para estudiantes, no mostrar cuáles son las respuestas correctas
    if (req.user && req.user.role === 'student') {
      // Crear una copia del objeto para modificarlo
      const quizForStudent = JSON.parse(JSON.stringify(quiz));
      
      // Quitar las respuestas correctas
      quizForStudent.questions = quizForStudent.questions.map(question => {
        question.options = question.options.map(option => {
          const { isCorrect, ...rest } = option;
          return rest;
        });
        return question;
      });
      
      return res.status(200).json({
        success: true,
        data: quizForStudent
      });
    }

    // Para instructor y admin, mostrar todo
    res.status(200).json({
      success: true,
      data: quiz
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Crear un cuestionario
// @route   POST /api/quizzes
// @access  Private (Instructor, Admin)
exports.createQuiz = async (req, res, next) => {
  try {
    // Verificar curso
    const course = await Course.findById(req.body.course);

    if (!course) {
      return res.status(404).json({
        success: false,
        message: 'Curso no encontrado'
      });
    }

    // Verificar si el usuario es el instructor del curso o un admin
    if (course.instructor.toString() !== req.user.id && req.user.role !== 'admin') {
      return res.status(403).json({
        success: false,
        message: 'No autorizado para agregar cuestionarios a este curso'
      });
    }

    // Crear cuestionario
    const quiz = await Quiz.create(req.body);

    res.status(201).json({
      success: true,
      data: quiz
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Actualizar un cuestionario
// @route   PUT /api/quizzes/:id
// @access  Private (Instructor del curso, Admin)
exports.updateQuiz = async (req, res, next) => {
  try {
    let quiz = await Quiz.findById(req.params.id).populate({
      path: 'course',
      select: 'instructor'
    });

    if (!quiz) {
      return res.status(404).json({
        success: false,
        message: 'Cuestionario no encontrado'
      });
    }

    // Verificar si el usuario es el instructor del curso o un admin
    if (quiz.course.instructor.toString() !== req.user.id && req.user.role !== 'admin') {
      return res.status(403).json({
        success: false,
        message: 'No autorizado para actualizar este cuestionario'
      });
    }

    quiz = await Quiz.findByIdAndUpdate(req.params.id, req.body, {
      new: true,
      runValidators: true
    });

    res.status(200).json({
      success: true,
      data: quiz
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Eliminar un cuestionario
// @route   DELETE /api/quizzes/:id
// @access  Private (Instructor del curso, Admin)
exports.deleteQuiz = async (req, res, next) => {
  try {
    const quiz = await Quiz.findById(req.params.id).populate({
      path: 'course',
      select: 'instructor'
    });

    if (!quiz) {
      return res.status(404).json({
        success: false,
        message: 'Cuestionario no encontrado'
      });
    }

    // Verificar si el usuario es el instructor del curso o un admin
    if (quiz.course.instructor.toString() !== req.user.id && req.user.role !== 'admin') {
      return res.status(403).json({
        success: false,
        message: 'No autorizado para eliminar este cuestionario'
      });
    }

    await quiz.deleteOne();

    res.status(200).json({
      success: true,
      data: {}
    });
  } catch (err) {
    next(err);
  }
};

// @desc    Enviar respuestas de un cuestionario
// @route   POST /api/quizzes/:id/submit
// @access  Private (Student)
exports.submitQuiz = async (req, res, next) => {
  try {
    const quiz = await Quiz.findById(req.params.id);

    if (!quiz) {
      return res.status(404).json({
        success: false,
        message: 'Cuestionario no encontrado'
      });
    }

    // Verificar que es un estudiante
    if (req.user.role !== 'student') {
      return res.status(403).json({
        success: false,
        message: 'Solo los estudiantes pueden enviar respuestas'
      });
    }

    // Verificar si está matriculado en el curso
    const enrollment = await Enrollment.findOne({
      student: req.user.id,
      course: quiz.course
    });

    if (!enrollment) {
      return res.status(403).json({
        success: false,
        message: 'Debe estar matriculado para enviar respuestas'
      });
    }

    // Verificar formato de las respuestas
    if (!req.body.answers || !Array.isArray(req.body.answers)) {
      return res.status(400).json({
        success: false,
        message: 'Formato de respuestas inválido'
      });
    }

    // Calcular puntuación
    let correctAnswers = 0;
    const totalQuestions = quiz.questions.length;

    // Verificar cada respuesta
    for (const answer of req.body.answers) {
      // Buscar la pregunta correspondiente
      const question = quiz.questions.find(q => q._id.toString() === answer.questionId);
      
      if (!question) continue;

      // Para preguntas de opción múltiple
      if (question.type === 'multiple_choice') {
        // Obtener las opciones correctas
        const correctOptions = question.options
          .filter(option => option.isCorrect)
          .map(option => option._id.toString());

        // Verificar si las respuestas seleccionadas coinciden con las correctas
        const selectedOptions = answer.selectedOptionIds || [];
        
        // Si la cantidad de opciones seleccionadas es igual a las correctas
        // Y todas las opciones seleccionadas son correctas
        if (
          selectedOptions.length === correctOptions.length &&
          selectedOptions.every(opt => correctOptions.includes(opt))
        ) {
          correctAnswers++;
        }
      } 
      // Para preguntas de verdadero/falso
      else if (question.type === 'true_false') {
        // Obtener la opción correcta (generalmente hay solo una correcta en V/F)
        const correctOption = question.options.find(option => option.isCorrect);
        
        if (
          correctOption && 
          answer.selectedOptionIds && 
          answer.selectedOptionIds.includes(correctOption._id.toString())
        ) {
          correctAnswers++;
        }
      }
    }

    // Calcular porcentaje
    const score = Math.round((correctAnswers / totalQuestions) * 100);
    const passed = score >= quiz.passingScore;

    // Guardar envío
    const submission = await QuizSubmission.create({
      student: req.user.id,
      quiz: req.params.id,
      answers: req.body.answers,
      score,
      passed
    });

    // Actualizar progreso en el curso si aprobó el quiz
    if (passed) {
      enrollment.progress += 10; // Aumentar progreso un 10%
      
      // Asegurarse de que no supere el 100%
      if (enrollment.progress > 100) {
        enrollment.progress = 100;
      }
      
      // Si llegó al 100%, marcar como completado
      if (enrollment.progress >= 100) {
        enrollment.completed = true;
        enrollment.completedDate = Date.now();
      }
      
      await enrollment.save();
    }

    res.status(200).json({
      success: true,
      data: {
        score,
        passed,
        correctAnswers,
        totalQuestions
      }
    });
  } catch (err) {
    next(err);
  }
};

// routes/quizzes.js
const express = require('express');
const router = express.Router();
const {
  getQuizzes,
  getQuiz,
  createQuiz,
  updateQuiz,
  deleteQuiz,
  submitQuiz
} = require('../controllers/quizzes');

const { protect, authorize } = require('../middleware/auth');

router
  .route('/')
  .get(protect, authorize('instructor', 'admin'), getQuizzes)
  .post(protect, authorize('instructor', 'admin'), createQuiz);

router
  .route('/:id')
  .get(getQuiz)
  .put(protect, authorize('instructor', 'admin'), updateQuiz)
  .delete(protect, authorize('instructor', 'admin'), deleteQuiz);

router
  .route('/:id/submit')
  .post(protect, authorize('student'), submitQuiz);

module.exports = router;
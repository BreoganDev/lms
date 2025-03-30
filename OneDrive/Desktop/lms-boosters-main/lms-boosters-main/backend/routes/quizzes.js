const express = require('express');
const router = express.Router();

// Datos de ejemplo para cuestionarios
const dummyQuizzes = [
  {
    id: 1,
    title: 'Cuestionario sobre HTML',
    description: 'Evalúa tus conocimientos de HTML',
    lessonId: 2,
    timeLimit: 15, // minutos
    passingScore: 70, // porcentaje necesario para aprobar
    isPublished: true,
    questions: [
      {
        id: 1,
        text: '¿Qué significa HTML?',
        type: 'multiple-choice',
        options: [
          { id: 1, text: 'Hyper Text Markup Language', isCorrect: true },
          { id: 2, text: 'High Technology Modern Language', isCorrect: false },
          { id: 3, text: 'Hyperlink and Text Markup Language', isCorrect: false },
          { id: 4, text: 'Home Tool Markup Language', isCorrect: false }
        ]
      },
      {
        id: 2,
        text: '¿Cuál de estos elementos HTML define un encabezado de nivel 1?',
        type: 'multiple-choice',
        options: [
          { id: 1, text: '<heading>', isCorrect: false },
          { id: 2, text: '<h1>', isCorrect: true },
          { id: 3, text: '<head>', isCorrect: false },
          { id: 4, text: '<header>', isCorrect: false }
        ]
      }
    ]
  },
  {
    id: 2,
    title: 'Cuestionario sobre CSS',
    description: 'Evalúa tus conocimientos de CSS',
    lessonId: 3,
    timeLimit: 10,
    passingScore: 60,
    isPublished: true,
    questions: [
      {
        id: 1,
        text: '¿Qué significa CSS?',
        type: 'multiple-choice',
        options: [
          { id: 1, text: 'Creative Style Sheets', isCorrect: false },
          { id: 2, text: 'Computer Style Sheets', isCorrect: false },
          { id: 3, text: 'Cascading Style Sheets', isCorrect: true },
          { id: 4, text: 'Colorful Style Sheets', isCorrect: false }
        ]
      }
    ]
  }
];

// @desc    Obtener todos los cuestionarios
// @route   GET /api/quizzes
// @access  Public
router.get('/', (req, res) => {
  try {
    const { lessonId, published } = req.query;
    
    let result = [...dummyQuizzes];
    
    // Filtrar por lección
    if (lessonId) {
      const id = parseInt(lessonId);
      result = result.filter(quiz => quiz.lessonId === id);
    }
    
    // Filtrar por estado de publicación
    if (published !== undefined) {
      const isPublished = published === 'true';
      result = result.filter(quiz => quiz.isPublished === isPublished);
    }
    
    res.status(200).json({
      success: true,
      count: result.length,
      data: result
    });
  } catch (err) {
    res.status(500).json({
      success: false,
      message: err.message || 'Error en el servidor'
    });
  }
});

// @desc    Obtener un cuestionario por ID
// @route   GET /api/quizzes/:id
// @access  Public/Private (dependiendo de si está publicado)
router.get('/:id', (req, res) => {
  try {
    const id = parseInt(req.params.id);
    const quiz = dummyQuizzes.find(q => q.id === id);
    
    if (!quiz) {
      return res.status(404).json({
        success: false,
        message: `No se encontró el cuestionario con ID ${id}`
      });
    }
    
    res.status(200).json({
      success: true,
      data: quiz
    });
  } catch (err) {
    res.status(500).json({
      success: false,
      message: err.message || 'Error en el servidor'
    });
  }
});

// @desc    Crear un nuevo cuestionario
// @route   POST /api/quizzes
// @access  Private/Instructor
router.post('/', (req, res) => {
  try {
    const { 
      title, 
      description, 
      lessonId, 
      timeLimit, 
      passingScore,
      isPublished = false,
      questions = []
    } = req.body;
    
    // Validación básica
    if (!title || !lessonId) {
      return res.status(400).json({
        success: false,
        message: 'Por favor proporcione título y lessonId'
      });
    }
    
    // Crear el nuevo cuestionario
    const newQuiz = {
      id: dummyQuizzes.length > 0 ? Math.max(...dummyQuizzes.map(q => q.id)) + 1 : 1,
      title,
      description: description || '',
      lessonId: parseInt(lessonId),
      timeLimit: timeLimit ? parseInt(timeLimit) : 0,
      passingScore: passingScore ? parseInt(passingScore) : 60,
      isPublished,
      questions
    };
    
    dummyQuizzes.push(newQuiz);
    
    res.status(201).json({
      success: true,
      data: newQuiz
    });
  } catch (err) {
    res.status(500).json({
      success: false,
      message: err.message || 'Error en el servidor'
    });
  }
});

// @desc    Actualizar un cuestionario
// @route   PUT /api/quizzes/:id
// @access  Private/Instructor
router.put('/:id', (req, res) => {
  try {
    const id = parseInt(req.params.id);
    const quizIndex = dummyQuizzes.findIndex(q => q.id === id);
    
    if (quizIndex === -1) {
      return res.status(404).json({
        success: false,
        message: `No se encontró el cuestionario con ID ${id}`
      });
    }
    
    const { 
      title, 
      description, 
      lessonId, 
      timeLimit, 
      passingScore,
      isPublished,
      questions
    } = req.body;
    
    const updatedQuiz = {
      ...dummyQuizzes[quizIndex],
      title: title || dummyQuizzes[quizIndex].title,
      description: description !== undefined ? description : dummyQuizzes[quizIndex].description,
      lessonId: lessonId ? parseInt(lessonId) : dummyQuizzes[quizIndex].lessonId,
      timeLimit: timeLimit !== undefined ? parseInt(timeLimit) : dummyQuizzes[quizIndex].timeLimit,
      passingScore: passingScore !== undefined ? parseInt(passingScore) : dummyQuizzes[quizIndex].passingScore,
      isPublished: isPublished !== undefined ? isPublished : dummyQuizzes[quizIndex].isPublished,
      questions: questions || dummyQuizzes[quizIndex].questions
    };
    
    dummyQuizzes[quizIndex] = updatedQuiz;
    
    res.status(200).json({
      success: true,
      data: updatedQuiz
    });
  } catch (err) {
    res.status(500).json({
      success: false,
      message: err.message || 'Error en el servidor'
    });
  }
});

// @desc    Eliminar un cuestionario
// @route   DELETE /api/quizzes/:id
// @access  Private/Instructor
router.delete('/:id', (req, res) => {
  try {
    const id = parseInt(req.params.id);
    const quizIndex = dummyQuizzes.findIndex(q => q.id === id);
    
    if (quizIndex === -1) {
      return res.status(404).json({
        success: false,
        message: `No se encontró el cuestionario con ID ${id}`
      });
    }
    
    const deletedQuiz = dummyQuizzes[quizIndex];
    const updatedQuizzes = dummyQuizzes.filter(q => q.id !== id);
    
    // En una implementación real, actualizaríamos la base de datos
    
    res.status(200).json({
      success: true,
      data: deletedQuiz,
      message: `Cuestionario con ID ${id} eliminado correctamente`
    });
  } catch (err) {
    res.status(500).json({
      success: false,
      message: err.message || 'Error en el servidor'
    });
  }
});

// @desc    Agregar pregunta a un cuestionario
// @route   POST /api/quizzes/:id/questions
// @access  Private/Instructor
router.post('/:id/questions', (req, res) => {
  try {
    const id = parseInt(req.params.id);
    const quizIndex = dummyQuizzes.findIndex(q => q.id === id);
    
    if (quizIndex === -1) {
      return res.status(404).json({
        success: false,
        message: `No se encontró el cuestionario con ID ${id}`
      });
    }
    
    const { text, type, options } = req.body;
    
    if (!text || !type) {
      return res.status(400).json({
        success: false,
        message: 'Por favor proporcione texto y tipo de pregunta'
      });
    }
    
    const newQuestion = {
      id: dummyQuizzes[quizIndex].questions.length > 0 
        ? Math.max(...dummyQuizzes[quizIndex].questions.map(q => q.id)) + 1 
        : 1,
      text,
      type,
      options: options || []
    };
    
    dummyQuizzes[quizIndex].questions.push(newQuestion);
    
    res.status(201).json({
      success: true,
      data: newQuestion
    });
  } catch (err) {
    res.status(500).json({
      success: false,
      message: err.message || 'Error en el servidor'
    });
  }
});

module.exports = router;
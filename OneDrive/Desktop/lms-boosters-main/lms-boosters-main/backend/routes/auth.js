const express = require('express');
const router = express.Router();

// @desc    Registrar un usuario
// @route   POST /api/auth/register
// @access  Public
router.post('/register', (req, res) => {
  try {
    // En una implementación completa, aquí se llamaría al controlador
    // Por ahora, simulamos una respuesta exitosa
    res.status(201).json({
      success: true,
      user: {
        id: 1,
        name: req.body.name || 'Usuario Demo',
        email: req.body.email || 'demo@example.com',
        role: req.body.role || 'student'
      },
      token: 'demo-token-for-testing'
    });
  } catch (err) {
    res.status(500).json({
      success: false,
      message: err.message || 'Error en el servidor'
    });
  }
});

// @desc    Iniciar sesión
// @route   POST /api/auth/login
// @access  Public
router.post('/login', (req, res) => {
  try {
    // En una implementación completa, aquí se llamaría al controlador
    // Por ahora, simulamos una respuesta exitosa
    res.status(200).json({
      success: true,
      user: {
        id: 1,
        name: 'Usuario Demo',
        email: req.body.email || 'demo@example.com',
        role: 'admin' // Por defecto, damos acceso de admin para pruebas
      },
      token: 'demo-token-for-testing'
    });
  } catch (err) {
    res.status(500).json({
      success: false,
      message: err.message || 'Error en el servidor'
    });
  }
});

// @desc    Obtener usuario actual
// @route   GET /api/auth/me
// @access  Private
router.get('/me', (req, res) => {
  try {
    // En una implementación completa, aquí se verificaría el token
    // Por ahora, simulamos una respuesta exitosa
    res.status(200).json({
      success: true,
      data: {
        _id: 1,
        name: 'Usuario Demo',
        email: 'demo@example.com',
        role: 'admin'
      }
    });
  } catch (err) {
    res.status(500).json({
      success: false,
      message: err.message || 'Error en el servidor'
    });
  }
});

module.exports = router;
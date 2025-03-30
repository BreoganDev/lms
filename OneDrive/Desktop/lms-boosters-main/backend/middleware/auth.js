// middleware/auth.js
const jwt = require('jsonwebtoken');
const User = require('../models/User');

// Proteger rutas
exports.protect = async (req, res, next) => {
  let token;

  // Verificar si hay token en los headers
  if (req.headers.authorization && req.headers.authorization.startsWith('Bearer')) {
    token = req.headers.authorization.split(' ')[1];
  }

  // Verificar si el token existe
  if (!token) {
    return res.status(401).json({
      success: false,
      message: 'No está autorizado para acceder a esta ruta'
    });
  }

  try {
    // Verificar token
    const decoded = jwt.verify(token, process.env.JWT_SECRET);

    // Buscar usuario por id
    req.user = await User.findById(decoded.id);

    next();
  } catch (err) {
    return res.status(401).json({
      success: false,
      message: 'No está autorizado para acceder a esta ruta'
    });
  }
};

// Autorizar roles
exports.authorize = (...roles) => {
  return (req, res, next) => {
    if (!roles.includes(req.user.role)) {
      return res.status(403).json({
        success: false,
        message: `El rol ${req.user.role} no está autorizado para acceder a esta ruta`
      });
    }
    next();
  };
};

// middleware/error.js
const errorHandler = (err, req, res, next) => {
  // Log para desarrollo
  console.error(err);

  // Error de MongoDB por ID no válido
  if (err.name === 'CastError') {
    return res.status(400).json({
      success: false,
      message: 'Recurso no encontrado'
    });
  }

  // Error de validación de Mongoose
  if (err.name === 'ValidationError') {
    const message = Object.values(err.errors).map(val => val.message).join(', ');
    return res.status(400).json({
      success: false,
      message
    });
  }

  // Error de clave duplicada
  if (err.code === 11000) {
    return res.status(400).json({
      success: false,
      message: 'El valor ingresado ya existe'
    });
  }

  // Error por defecto
  res.status(err.statusCode || 500).json({
    success: false,
    message: err.message || 'Error del servidor'
  });
};

module.exports = errorHandler;

// middleware/upload.js
const multer = require('multer');
const path = require('path');

// Configurar almacenamiento
const storage = multer.diskStorage({
  destination: function(req, file, cb) {
    cb(null, './uploads/');
  },
  filename: function(req, file, cb) {
    cb(null, `${Date.now()}-${file.originalname}`);
  }
});

// Filtrar archivos
const fileFilter = (req, file, cb) => {
  // Aceptar imágenes solamente
  if (file.mimetype.startsWith('image')) {
    cb(null, true);
  } else {
    cb(new Error('Solo se permiten imágenes'), false);
  }
};

const upload = multer({
  storage,
  fileFilter,
  limits: { fileSize: 1024 * 1024 * 5 } // 5MB
});

module.exports = upload;
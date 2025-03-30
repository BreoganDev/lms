const express = require('express');
const cors = require('cors');
const dotenv = require('dotenv');
const path = require('path');
const swaggerUi = require('swagger-ui-express');
const swaggerJsDoc = require('swagger-jsdoc');

// Cargar variables de entorno
dotenv.config();

// Swagger configuration
const swaggerOptions = {
  definition: {
    openapi: '3.0.0',
    info: {
      title: 'LMS Boosters API',
      version: '1.0.0',
      description: 'API para plataforma de Learning Management System',
      contact: {
        name: 'Developer'
      },
      servers: [{
        url: 'http://localhost:3001'
      }]
    }
  },
  apis: ['./routes/*.js'] // Path to the API routes
};

const swaggerDocs = swaggerJsDoc(swaggerOptions);

// Importar modelos
const db = require('./models');

// Importar rutas
const authRoutes = require('./routes/auth');
const courseRoutes = require('./routes/courses');
const lessonRoutes = require('./routes/lessons');
const studentRoutes = require('./routes/students');
const categoryRoutes = require('./routes/categories');
const instructorRoutes = require('./routes/instructors');
const quizRoutes = require('./routes/quizzes');

// Inicializar app express
const app = express();
const PORT = 3001;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Swagger docs route
app.use('/api-docs', swaggerUi.serve, swaggerUi.setup(swaggerDocs));

// Directorio para archivos estáticos
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

// Definir rutas
app.use('/api/auth', authRoutes);
app.use('/api/courses', courseRoutes);
app.use('/api/lessons', lessonRoutes);
app.use('/api/students', studentRoutes);
app.use('/api/categories', categoryRoutes);
app.use('/api/instructors', instructorRoutes);
app.use('/api/quizzes', quizRoutes);

// Ruta principal
app.get('/', (req, res) => {
  res.send('API de LMS Boosters funcionando correctamente. Visita <a href="/api-docs">Documentación API</a> para probar los endpoints.');
});

// Iniciar el servidor
app.listen(PORT, () => {
  console.log(`Servidor ejecutándose en http://localhost:${PORT}`);
  console.log(`Documentación Swagger disponible en http://localhost:${PORT}/api-docs`);
});

// Exportar app para tests
module.exports = app;
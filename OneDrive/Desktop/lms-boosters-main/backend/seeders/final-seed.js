const bcrypt = require('bcryptjs');
const db = require('../models');

// Función para sincronizar y poblar la base de datos con datos iniciales
const seedDatabase = async () => {
  try {
    // Eliminar y recrear todas las tablas
    await db.sequelize.sync({ force: true });
    console.log('Base de datos sincronizada correctamente (tablas recreadas)');

    // Crear usuarios
    const salt = await bcrypt.genSalt(10);
    const hashedPassword = await bcrypt.hash('password123', salt);

    const admin = await db.User.create({
      name: 'Admin User',
      email: 'admin@ejemplo.com',
      password: hashedPassword,
      role: 'admin',
      isActive: true
    });

    const instructor = await db.User.create({
      name: 'Roberto Gómez',
      email: 'instructor@ejemplo.com',
      password: hashedPassword,
      role: 'instructor',
      isActive: true
    });

    const student = await db.User.create({
      name: 'Juan Pérez',
      email: 'student@ejemplo.com',
      password: hashedPassword,
      role: 'student',
      isActive: true
    });

    console.log('Usuarios creados correctamente');

    // Crear categorías
    const webCategory = await db.Category.create({
      name: 'Desarrollo Web',
      slug: 'desarrollo-web',
      description: 'Cursos relacionados con el desarrollo web',
      icon: 'fa-code',
      coursesCount: 1
    });

    const designCategory = await db.Category.create({
      name: 'Diseño',
      slug: 'diseno',
      description: 'Cursos de diseño gráfico y UX/UI',
      icon: 'fa-pencil-ruler',
      coursesCount: 0
    });

    console.log('Categorías creadas correctamente');

    // Crear cursos
    const course = await db.Course.create({
      title: 'Introducción a HTML y CSS',
      description: 'Aprende los fundamentos del desarrollo web con HTML y CSS',
      instructorId: instructor.id,
      categoryId: webCategory.id,
      image: 'html-css.jpg',
      price: 29.99,
      duration: '10 horas',
      rating: 4.7,
      studentsCount: 10,
      isPublished: true,
      slug: 'introduccion-html-css'
    });

    console.log('Curso creado correctamente');

    // Crear una matrícula (enrollment)
    const enrollment = await db.Enrollment.create({
      studentId: student.id,
      courseId: course.id,
      progress: 30,
      enrollmentDate: new Date()
    });

    console.log('Matrícula creada correctamente');
    console.log('Base de datos poblada exitosamente');

    process.exit(0);
  } catch (error) {
    console.error('Error al poblar la base de datos:', error);
    process.exit(1);
  }
};

// Ejecutar la función
seedDatabase();
const { Sequelize, DataTypes } = require('sequelize');
const dotenv = require('dotenv');

dotenv.config();

// Configuración de la conexión a la base de datos
const sequelize = new Sequelize(
  process.env.DB_NAME || 'lms_boosters',
  process.env.DB_USER || 'root',
  process.env.DB_PASSWORD || '',
  {
    host: process.env.DB_HOST || 'localhost',
    dialect: 'mysql',
    port: process.env.DB_PORT || 3306,
    logging: console.log // Para ver las consultas SQL
  }
);

// Definir el modelo User directamente aquí
const User = sequelize.define('User', {
  id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  name: {
    type: DataTypes.STRING,
    allowNull: false
  },
  email: {
    type: DataTypes.STRING,
    allowNull: false,
    unique: true,
    validate: {
      isEmail: true
    }
  },
  password: {
    type: DataTypes.STRING,
    allowNull: false
  },
  role: {
    type: DataTypes.ENUM('admin', 'instructor', 'student'),
    defaultValue: 'student'
  },
  isActive: {
    type: DataTypes.BOOLEAN,
    defaultValue: true
  }
}, {
  timestamps: true,
  tableName: 'users'
});

// Función para inicializar la BD y crear un usuario admin
const initDB = async () => {
  try {
    // Autenticar la conexión
    await sequelize.authenticate();
    console.log('Conexión a MySQL establecida correctamente');

    // Sincronizar el modelo con la BD (crear la tabla si no existe)
    await sequelize.sync({ force: true }); // force: true eliminará la tabla si ya existe
    console.log('Tabla de usuarios creada correctamente');

    // Crear un usuario admin básico
    const adminUser = await User.create({
      name: 'Admin User',
      email: 'admin@ejemplo.com',
      password: 'admin123', // En producción, esto debería estar encriptado
      role: 'admin'
    });

    console.log('Usuario admin creado:', adminUser.toJSON());
    console.log('Base de datos inicializada correctamente');
    
    process.exit(0);
  } catch (error) {
    console.error('Error al inicializar la base de datos:', error);
    process.exit(1);
  }
};

// Ejecutar la función
initDB();
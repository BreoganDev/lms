const { Sequelize, DataTypes } = require('sequelize');
const bcrypt = require('bcryptjs');
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
    logging: console.log
  }
);

// Función para inicializar la BD
const initDB = async () => {
  try {
    // Autenticar la conexión
    await sequelize.authenticate();
    console.log('Conexión a MySQL establecida correctamente');

    // Definir el modelo User
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
        unique: true
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

    // Sincronizar el modelo con la BD (crear la tabla si no existe)
    await sequelize.sync({ force: true }); // force: true eliminará la tabla si ya existe
    console.log('Tabla de usuarios creada correctamente');

    // Crear un usuario admin básico
    const salt = await bcrypt.genSalt(10);
    const hashedPassword = await bcrypt.hash('admin123', salt);
    
    const adminUser = await User.create({
      name: 'Admin User',
      email: 'admin@ejemplo.com',
      password: hashedPassword,
      role: 'admin',
      isActive: true
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
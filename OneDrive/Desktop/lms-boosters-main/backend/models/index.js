const Sequelize = require('sequelize');
const dotenv = require('dotenv');

dotenv.config();

const db = {};

// Configuración de la conexión a la base de datos
const sequelize = new Sequelize(
  process.env.DB_NAME || 'lms_boosters',
  process.env.DB_USER || 'root',
  process.env.DB_PASSWORD || 'root',
  {
    host: process.env.DB_HOST || 'localhost',
    dialect: 'mysql',
    port: process.env.DB_PORT || 3306,
    logging: false // Cambiar a console.log para ver las consultas SQL
  }
);

// Definir los modelos (sin asociaciones)
db.User = require('./User')(sequelize, Sequelize.DataTypes);
db.Category = require('./Category')(sequelize, Sequelize.DataTypes);
db.Course = require('./Course')(sequelize, Sequelize.DataTypes);
db.Enrollment = require('./Enrollment')(sequelize, Sequelize.DataTypes);

// Añadir algunas asociaciones básicas manualmente después
// (lo haremos en el futuro cuando todo funcione)

db.sequelize = sequelize;
db.Sequelize = Sequelize;

module.exports = db;
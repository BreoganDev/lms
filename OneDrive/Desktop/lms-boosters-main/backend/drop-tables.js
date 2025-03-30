const { Sequelize } = require('sequelize');
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

// Función para eliminar todas las tablas
const dropAllTables = async () => {
  try {
    // Autenticar conexión
    await sequelize.authenticate();
    console.log('Conexión a MySQL establecida correctamente');

    // Eliminar tablas (ajusta los nombres según sea necesario)
    const tablesToDrop = ['enrollments', 'courses', 'categories', 'users', 'SequelizeMeta'];
    
    for (const table of tablesToDrop) {
      try {
        await sequelize.query(`DROP TABLE IF EXISTS ${table}`);
        console.log(`Tabla ${table} eliminada correctamente`);
      } catch (error) {
        console.error(`Error al eliminar la tabla ${table}:`, error.message);
      }
    }

    console.log('Todas las tablas han sido eliminadas');
    process.exit(0);
  } catch (error) {
    console.error('Error al limpiar la base de datos:', error);
    process.exit(1);
  }
};

// Ejecutar la función
dropAllTables();
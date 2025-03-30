module.exports = (sequelize, DataTypes) => {
    const Course = sequelize.define('Course', {
      id: {
        type: DataTypes.INTEGER,
        primaryKey: true,
        autoIncrement: true
      },
      title: {
        type: DataTypes.STRING,
        allowNull: false
      },
      description: {
        type: DataTypes.TEXT,
        allowNull: false
      },
      instructorId: {
        type: DataTypes.INTEGER,
        allowNull: false
      },
      categoryId: {
        type: DataTypes.INTEGER,
        allowNull: false
      },
      image: {
        type: DataTypes.STRING,
        defaultValue: 'default-course.jpg'
      },
      price: {
        type: DataTypes.DECIMAL(10, 2),
        defaultValue: 0.00
      },
      duration: {
        type: DataTypes.STRING,
        defaultValue: '0 horas'
      },
      rating: {
        type: DataTypes.DECIMAL(3, 2),
        defaultValue: 0.0
      },
      studentsCount: {
        type: DataTypes.INTEGER,
        defaultValue: 0
      },
      isPublished: {
        type: DataTypes.BOOLEAN,
        defaultValue: false
      },
      slug: {
        type: DataTypes.STRING,
        unique: true
      }
    }, {
      timestamps: true,
      tableName: 'courses'
    });
  
    return Course;
  };
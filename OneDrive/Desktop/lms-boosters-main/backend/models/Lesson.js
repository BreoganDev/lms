module.exports = (sequelize, DataTypes) => {
    const Lesson = sequelize.define('Lesson', {
      id: {
        type: DataTypes.INTEGER,
        primaryKey: true,
        autoIncrement: true
      },
      courseId: {
        type: DataTypes.INTEGER,
        allowNull: false
      },
      title: {
        type: DataTypes.STRING,
        allowNull: false
      },
      description: {
        type: DataTypes.TEXT
      },
      content: {
        type: DataTypes.TEXT
      },
      videoUrl: {
        type: DataTypes.STRING
      },
      duration: {
        type: DataTypes.STRING
      },
      order: {
        type: DataTypes.INTEGER,
        defaultValue: 0
      },
      isPublished: {
        type: DataTypes.BOOLEAN,
        defaultValue: false
      }
    }, {
      timestamps: true,
      tableName: 'lessons'
    });
  
    Lesson.associate = function(models) {
      // Una lección pertenece a un curso
      Lesson.belongsTo(models.Course, {
        foreignKey: 'courseId',
        as: 'course'
      });
  
      // Una lección puede tener muchos quizzes
      Lesson.hasMany(models.Quiz, {
        foreignKey: 'lessonId',
        as: 'quizzes'
      });
    };
  
    return Lesson;
  };
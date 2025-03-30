module.exports = (sequelize, DataTypes) => {
    const Quiz = sequelize.define('Quiz', {
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
        type: DataTypes.TEXT
      },
      lessonId: {
        type: DataTypes.INTEGER,
        allowNull: false
      },
      timeLimit: {
        type: DataTypes.INTEGER, // en minutos
        defaultValue: 0
      },
      passingScore: {
        type: DataTypes.INTEGER, // porcentaje necesario para aprobar
        defaultValue: 70
      },
      isPublished: {
        type: DataTypes.BOOLEAN,
        defaultValue: false
      }
    }, {
      timestamps: true,
      tableName: 'quizzes'
    });
  
    Quiz.associate = function(models) {
      // Un quiz pertenece a una lección
      Quiz.belongsTo(models.Lesson, {
        foreignKey: 'lessonId',
        as: 'lesson'
      });
  
      // Un quiz tiene muchas preguntas
      Quiz.hasMany(models.Question, {
        foreignKey: 'quizId',
        as: 'questions'
      });
    };
  
    return Quiz;
  };
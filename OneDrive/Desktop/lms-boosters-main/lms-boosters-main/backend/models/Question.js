module.exports = (sequelize, DataTypes) => {
    const Question = sequelize.define('Question', {
      id: {
        type: DataTypes.INTEGER,
        primaryKey: true,
        autoIncrement: true
      },
      quizId: {
        type: DataTypes.INTEGER,
        allowNull: false
      },
      text: {
        type: DataTypes.TEXT,
        allowNull: false
      },
      type: {
        type: DataTypes.ENUM('multiple-choice', 'true-false', 'short-answer', 'matching'),
        defaultValue: 'multiple-choice'
      },
      points: {
        type: DataTypes.INTEGER,
        defaultValue: 1
      },
      order: {
        type: DataTypes.INTEGER,
        defaultValue: 0
      }
    }, {
      timestamps: true,
      tableName: 'questions'
    });
  
    Question.associate = function(models) {
      // Una pregunta pertenece a un quiz
      Question.belongsTo(models.Quiz, {
        foreignKey: 'quizId',
        as: 'quiz'
      });
  
      // Una pregunta tiene muchas opciones
      Question.hasMany(models.Option, {
        foreignKey: 'questionId',
        as: 'options'
      });
    };
  
    return Question;
  };
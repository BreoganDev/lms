module.exports = (sequelize, DataTypes) => {
    const Option = sequelize.define('Option', {
      id: {
        type: DataTypes.INTEGER,
        primaryKey: true,
        autoIncrement: true
      },
      questionId: {
        type: DataTypes.INTEGER,
        allowNull: false
      },
      text: {
        type: DataTypes.TEXT,
        allowNull: false
      },
      isCorrect: {
        type: DataTypes.BOOLEAN,
        defaultValue: false
      },
      order: {
        type: DataTypes.INTEGER,
        defaultValue: 0
      }
    }, {
      timestamps: true,
      tableName: 'options'
    });
  
    Option.associate = function(models) {
      // Una opción pertenece a una pregunta
      Option.belongsTo(models.Question, {
        foreignKey: 'questionId',
        as: 'question'
      });
    };
  
    return Option;
  };
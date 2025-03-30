module.exports = (sequelize, DataTypes) => {
    const Enrollment = sequelize.define('Enrollment', {
      id: {
        type: DataTypes.INTEGER,
        primaryKey: true,
        autoIncrement: true
      },
      studentId: {
        type: DataTypes.INTEGER,
        allowNull: false
      },
      courseId: {
        type: DataTypes.INTEGER,
        allowNull: false
      },
      progress: {
        type: DataTypes.INTEGER,
        defaultValue: 0
      },
      completed: {
        type: DataTypes.BOOLEAN,
        defaultValue: false
      },
      enrollmentDate: {
        type: DataTypes.DATE,
        defaultValue: DataTypes.NOW
      },
      completionDate: {
        type: DataTypes.DATE
      }
    }, {
      timestamps: true,
      tableName: 'enrollments'
    });
  
    return Enrollment;
  };
const { spawn } = require('child_process');
const path = require('path');

// Inicia el servidor de desarrollo de React
const frontend = spawn('npx', ['react-scripts', 'start'], {
  stdio: 'inherit',
  shell: true
});

frontend.on('error', (error) => {
  console.error('Error al iniciar el frontend:', error);
});
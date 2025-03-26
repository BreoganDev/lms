<<<<<<< HEAD
document.addEventListener('DOMContentLoaded', function() {
    console.log("Theme Switcher Initialized");
    
    const savedTheme = localStorage.getItem('blms-theme') || 'light';
    
    console.log('Saved Theme:', savedTheme);
    console.log('Current Body Classes:', document.body.classList.toString());
    
    function applyTheme() {
        if (savedTheme === 'light') {
            console.log('Applying Light Mode');
            document.body.classList.remove('dark-mode');
            document.body.classList.add('light-mode');
            document.documentElement.removeAttribute('data-theme');
        } else {
            console.log('Applying Dark Mode');
            document.body.classList.add('dark-mode');
            document.body.classList.remove('light-mode');
            document.documentElement.setAttribute('data-theme', 'dark');
        }
        
        console.log('Body Classes After Apply:', document.body.classList.toString());
    }
    
    // Forzar aplicación inmediata
    applyTheme();
    
    // Escuchar cambios en almacenamiento local
    window.addEventListener('storage', function(e) {
        if (e.key === 'blms-theme') {
            console.log('Theme changed in storage');
            applyTheme();
        }
    });

    // Añadir manejador para el botón de cambio de tema
    const themeToggleButtons = document.querySelectorAll('.theme-toggle');
    themeToggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const currentTheme = localStorage.getItem('blms-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            localStorage.setItem('blms-theme', newTheme);
            applyTheme();
=======
/**
 * Esta función debe colocarse en un archivo JavaScript que se cargue en todas las páginas
 * Proporciona compatibilidad con el cambio de tema independiente del plugin WordPress
 */

// Esperar a que el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
    // Función para detectar el modo de color preferido del sistema
    function detectPreferredColorScheme() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }
        return 'light';
    }

    // Función para establecer el tema
    function setTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            document.body.classList.add('dark-mode');
            localStorage.setItem('blms-theme', 'dark');
        } else {
            document.documentElement.removeAttribute('data-theme');
            document.body.classList.remove('dark-mode');
            localStorage.setItem('blms-theme', 'light');
        }
    }

    // Obtener tema almacenado o usar preferencia del sistema
    const savedTheme = localStorage.getItem('blms-theme');
    if (savedTheme) {
        setTheme(savedTheme);
    } else {
        setTheme(detectPreferredColorScheme());
    }

    // Buscar cualquier botón de cambio de tema y añadir funcionalidad
    const themeToggleButtons = document.querySelectorAll('.theme-toggle, .dark-mode-toggle, #dark-mode-btn, .wp-dark-mode-toggle, [data-theme-toggle]');
    
    themeToggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const currentTheme = localStorage.getItem('blms-theme') || detectPreferredColorScheme();
            if (currentTheme === 'light') {
                setTheme('dark');
            } else {
                setTheme('light');
            }
>>>>>>> 3304e421caae91f58c934cbba7438d218e5a9df1
        });
    });
});
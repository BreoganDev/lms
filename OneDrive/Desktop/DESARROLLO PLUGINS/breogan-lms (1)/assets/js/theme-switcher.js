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
        });
    });
});
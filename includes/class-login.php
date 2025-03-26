<?php
// Evitar acceso directo
if (!defined('ABSPATH')) exit;

class Breogan_LMS_Login {
    
    /**
     * Constructor de la clase
     */
    public function __construct() {
        // Añadir shortcode de login
        add_shortcode('breogan_login', array($this, 'login_shortcode'));
        
        // Manejar inicio de sesión AJAX
        add_action('wp_ajax_breogan_custom_login', array($this, 'handle_custom_login'));
        add_action('wp_ajax_nopriv_breogan_custom_login', array($this, 'handle_custom_login'));
        
        // Añadir estilos
        add_action('wp_head', array($this, 'login_styles'));
        
        // Personalizar login
        add_filter('login_url', array($this, 'custom_login_page'), 10, 1);
        add_action('init', array($this, 'login_redirect'));
    }
    
    /**
     * Shortcode para formulario de login personalizado
     */
    public function login_shortcode() {
        // Si el usuario ya está logueado, redirigir
        if (is_user_logged_in() && !is_admin()) {
        // Si está logueado y NO está en el área de administración
        wp_redirect(home_url('/mi-perfil'));
        exit;
        }

        ob_start();
        ?>
        <div class="breogan-login-container">
            <form id="breogan-login-form" method="post">
                <div class="form-group">
                    <label for="breogan-username">Usuario o Email</label>
                    <input type="text" id="breogan-username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="breogan-password">Contraseña</label>
                    <input type="password" id="breogan-password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="breogan-login-btn">Iniciar Sesión</button>
                </div>
                <div class="breogan-login-messages"></div>
            </form>
            <div class="breogan-login-links">
                <a href="<?php echo wp_lostpassword_url(); ?>">¿Olvidaste tu contraseña?</a>
                <a href="<?php echo wp_registration_url(); ?>">Registrarse</a>
            </div>
        </div>

        <script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('breogan-login-form');
    const messagesContainer = document.querySelector('.breogan-login-messages');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('breogan-username').value;
            const password = document.getElementById('breogan-password').value;

            const formData = new FormData();
            formData.append('action', 'breogan_custom_login');
            formData.append('username', username);
            formData.append('password', password);
            formData.append('nonce', '<?php echo wp_create_nonce('breogan-login-nonce'); ?>');

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messagesContainer.innerHTML = '<div class="success-message">' + data.data.message + '</div>';
                    window.location.href = data.data.redirect;
                } else {
                    messagesContainer.innerHTML = '<div class="error-message">' + data.data.message + '</div>';
                }
            })
            .catch(error => {
                messagesContainer.innerHTML = '<div class="error-message">Error de conexión</div>';
                console.error('Error:', error);
            });
        });
    }
});
</script>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Manejar la autenticación AJAX
     */
    public function handle_custom_login() {
        // Verificar nonce
        if (!wp_verify_nonce($_POST['nonce'], 'breogan-login-nonce')) {
            wp_send_json_error(['message' => 'Error de seguridad']);
        }

        // Intentar iniciar sesión
        $credentials = [
            'user_login'    => sanitize_user($_POST['username']),
            'user_password' => $_POST['password'],
            'remember'      => true
        ];

        // Intentar inicio de sesión
        $user = wp_signon($credentials, false);

        if (is_wp_error($user)) {
            // Error de inicio de sesión
            wp_send_json_error([
                'message' => 'Credenciales incorrectas. Por favor, inténtalo de nuevo.'
            ]);
        }

        // Inicio de sesión exitoso
        wp_send_json_success([
            'message' => 'Inicio de sesión exitoso',
            'redirect' => home_url('/mi-perfil') // Redirigir a página de perfil
        ]);
    }
    
    /**
     * Personalizar página de login
     */
    public function custom_login_page($login_url) {
        return home_url('/iniciar-sesion');
    }
    
    /**
     * Redirigir intentos de acceso a wp-login.php
     */
    public function login_redirect() {
        $login_page = home_url('/iniciar-sesion');
        $current_page = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

        // Redirigir wp-login.php a la página de login personalizada
        if (strpos($current_page, 'wp-login.php') !== false) {
            wp_redirect($login_page);
            exit;
        }
    }
    
    /**
     * Añadir estilos para el formulario de login
     */
   public function login_styles() {
    // Obtener el contenido del archivo CSS
    $css_path = plugin_dir_path(__FILE__) . 'login-styles.css';
    
    // Si aún no existe el archivo, créalo
    if (!file_exists($css_path)) {
        file_put_contents($css_path, $this->get_login_css());
    }
    
    // Encolar los estilos
    wp_enqueue_style('breogan-login-styles', 
        plugin_dir_url(__FILE__) . 'login-styles.css', 
        array(), 
        '1.0', 
        'all'
    );
}

// Método para obtener el CSS (opcional, pero útil)
private function get_login_css() {
    return ':root {
        /* Light Mode Colors */
        --login-bg-light: #f4f6f9;
        --login-card-bg-light: #ffffff;
        --login-text-light: #2c3e50;
        --login-input-bg-light: #ecf0f1;
        --login-input-border-light: #bdc3c7;
        --login-btn-bg-light: #3498db;
        --login-btn-hover-light: #2980b9;

        /* Dark Mode Colors */
        --login-bg-dark: #121212;
        --login-card-bg-dark: #1e1e1e;
        --login-text-dark: #e0e0e0;
        --login-input-bg-dark: #2c2c2c;
        --login-input-border-dark: #444;
        --login-btn-bg-dark: #4a90e2;
        --login-btn-hover-dark: #3a7bd5;
    }

    /* Global Styles */
    .breogan-login-container {
        max-width: 450px;
        margin: 50px auto;
        padding: 40px;
        background-color: var(--login-card-bg-light);
        border-radius: 16px;
        box-shadow: 
            0 10px 25px rgba(0,0,0,0.1), 
            0 6px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    /* Login Container Decoration */
.breogan-login-container::before {
    content: "";
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg, 
        #3498db 0%, 
        #2ecc71 50%, 
        #e74c3c 100%
    );
    transform: rotate(-45deg);
    z-index: -1;
    opacity: 0.1;
}

/* Form Styles */
.breogan-login-container .form-group {
    margin-bottom: 20px;
    position: relative;
}

.breogan-login-container label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #000000 !important;
    transition: color 0.3s ease;
}

@media (prefers-color-scheme: dark) {
    .breogan-login-container label {
        color: var(--login-text-dark);
    }
}

.breogan-login-container input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid var(--login-input-border-light);
    border-radius: 8px;
    border-color: #4a90e247 !important;
    background-color: #fbf7f7;
    color: #000000 !important;
    font-size: 16px;
    transition: all 0.3s ease;
}

@media (prefers-color-scheme: dark) {
    .breogan-login-container input {
        background-color: var(--login-input-bg-dark);
        border-color: var(--login-input-border-dark);
        color: var(--login-text-dark);
    }
}

.breogan-login-container input:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
}

.breogan-login-btn {
    width: 100%;
    padding: 15px;
    background-color: var(--login-btn-bg-light);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 18px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.breogan-login-btn::before {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        120deg, 
        transparent, 
        rgba(255,255,255,0.3), 
        transparent
    );
    transition: all 0.3s ease;
}

/* Estilos de labels */
    .breogan-login-container label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: black !important; /* Forzar color negro */
        transition: color 0.3s ease;
    }

    /* Asegurar color negro en diferentes modos y dispositivos */
    body .breogan-login-container label,
    body.dark-mode .breogan-login-container label,
    .breogan-login-container label:not(.dark-mode) {
        color: black !important;
    }

    /* Modo oscuro forzado */
    body.dark-mode .breogan-login-container label {
        color: white !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .breogan-login-container label {
            color: black !important;
        }
    };
}

.page-title {
    display:none;
}
}

.breogan-login-btn:hover::before {
    left: 100%;
}

@media (prefers-color-scheme: dark) {
    .breogan-login-btn {
        background-color: var(--login-btn-bg-dark);
    }
}

.breogan-login-btn:hover {
    background-color: var(--login-btn-hover-light);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
}

/* Messages */
.breogan-login-messages {
    margin-top: 20px;
    text-align: center;
}

.success-message {
    color: #2ecc71;
    font-weight: 600;
}

.error-message {
    color: #e74c3c;
    font-weight: 600;
}

/* Login Links */
.breogan-login-links {
    margin-top: 25px;
    text-align: center;
    display: flex;
    justify-content: center;
    gap: 20px;
}

.breogan-login-links a {
    color: #3498db;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
    position: relative;
}

.breogan-login-links a::after {
    content: "";
    position: absolute;
    width: 100%;
    height: 2px;
    bottom: -4px;
    left: 0;
    background-color: #3498db;
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.breogan-login-links a:hover::after {
    transform: scaleX(1);
}

@media (prefers-color-scheme: dark) {
    .breogan-login-links a {
        color: #4a90e2;
    }
    .breogan-login-links a::after {
        background-color: #4a90e2;
    }
}
    
    /* Responsive Adjustments */
    @media (max-width: 480px) {
        .breogan-login-container {
            width: 95%;
            margin: 30px auto;
            padding: 25px;
        }
    }';
}
}
// Inicializar la clase
new Breogan_LMS_Login();
<?php
/**
 * Plantilla para mostrar un instructor individual
 *
 * @package Breogan LMS
 */

get_header();

// Obtener metadatos del instructor
$instructor_id = get_the_ID();
$job_title = get_post_meta($instructor_id, '_instructor_job_title', true);
$experience = get_post_meta($instructor_id, '_instructor_experience', true);
$specialties = get_post_meta($instructor_id, '_instructor_specialties', true);
$education = get_post_meta($instructor_id, '_instructor_education', true);
$email = get_post_meta($instructor_id, '_instructor_email', true);
$phone = get_post_meta($instructor_id, '_instructor_phone', true);
$website = get_post_meta($instructor_id, '_instructor_website', true);

// Redes sociales
$facebook = get_post_meta($instructor_id, '_instructor_facebook', true);
$twitter = get_post_meta($instructor_id, '_instructor_twitter', true);
$instagram = get_post_meta($instructor_id, '_instructor_instagram', true);
$linkedin = get_post_meta($instructor_id, '_instructor_linkedin', true);
$youtube = get_post_meta($instructor_id, '_instructor_youtube', true);

// Cursos impartidos
$cursos_impartidos = get_post_meta($instructor_id, '_instructor_courses', true);
if (!is_array($cursos_impartidos)) {
    $cursos_impartidos = array();
}
?>

<main class="breogan-contenedor instructor-single">
    <div class="instructor-header">
        <div class="instructor-imagen-container">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('large', array('class' => 'instructor-imagen')); ?>
            <?php else : ?>
                <div class="instructor-imagen-placeholder">
                    <span class="iniciales"><?php echo substr(get_the_title(), 0, 1); ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="instructor-info-header">
            <h1 class="instructor-nombre"><?php the_title(); ?></h1>
            
            <?php if (!empty($job_title)) : ?>
                <p class="instructor-titulo"><?php echo esc_html($job_title); ?></p>
            <?php endif; ?>
            
            <?php if (!empty($specialties)) : ?>
                <div class="instructor-especialidades">
                    <?php 
                    $specialties_array = explode(',', $specialties);
                    foreach ($specialties_array as $specialty) {
                        echo '<span class="especialidad">' . esc_html(trim($specialty)) . '</span>';
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($facebook) || !empty($twitter) || !empty($instagram) || !empty($linkedin) || !empty($youtube)) : ?>
                <div class="instructor-social">
                    <?php if (!empty($facebook)) : ?>
                        <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener noreferrer" class="social-icon facebook" title="Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                            </svg>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($twitter)) : ?>
                        <a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="noopener noreferrer" class="social-icon twitter" title="Twitter">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path>
                            </svg>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($instagram)) : ?>
                        <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener noreferrer" class="social-icon instagram" title="Instagram">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($linkedin)) : ?>
                        <a href="<?php echo esc_url($linkedin); ?>" target="_blank" rel="noopener noreferrer" class="social-icon linkedin" title="LinkedIn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                                <rect x="2" y="9" width="4" height="12"></rect>
                                <circle cx="4" cy="4" r="2"></circle>
                            </svg>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($youtube)) : ?>
                        <a href="<?php echo esc_url($youtube); ?>" target="_blank" rel="noopener noreferrer" class="social-icon youtube" title="YouTube">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path>
                                <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon>
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="instructor-content-wrapper">
        <div class="instructor-main-content">
            <div class="instructor-biografia">
                <h2><?php _e('Biografía', 'breogan-lms'); ?></h2>
                <?php the_content(); ?>
            </div>
            
            <?php if (!empty($education)) : ?>
                <div class="instructor-formacion">
                    <h2><?php _e('Formación Académica', 'breogan-lms'); ?></h2>
                    <?php echo wpautop(esc_html($education)); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($cursos_impartidos)) : ?>
                <div class="instructor-cursos">
                    <h2><?php _e('Cursos impartidos por este instructor', 'breogan-lms'); ?></h2>
                    <div class="cursos-grid">
                        <?php foreach ($cursos_impartidos as $curso_id) : 
                            $curso = get_post($curso_id);
                            if ($curso && $curso->post_status == 'publish') : 
                                $precio = get_post_meta($curso_id, '_blms_precio_curso', true);
                                $es_gratuito = get_post_meta($curso_id, '_blms_curso_gratuito', true);
                        ?>
                            <div class="curso-card">
                                <a href="<?php echo get_permalink($curso_id); ?>" class="curso-link">
                                    <div class="curso-imagen">
                                        <?php if (has_post_thumbnail($curso_id)) : ?>
                                            <?php echo get_the_post_thumbnail($curso_id, 'medium'); ?>
                                        <?php else : ?>
                                            <div class="curso-imagen-placeholder"></div>
                                        <?php endif; ?>
                                        
                                        <?php if ($es_gratuito == '1') : ?>
                                            <span class="curso-badge gratuito"><?php _e('Gratis', 'breogan-lms'); ?></span>
                                        <?php elseif (!empty($precio)) : ?>
                                            <span class="curso-badge precio"><?php echo esc_html($precio); ?> €</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="curso-info">
                                        <h3 class="curso-titulo"><?php echo get_the_title($curso_id); ?></h3>
                                        
                                        <?php 
                                        // Obtener el extracto o generar uno
                                        $extracto = get_the_excerpt($curso_id);
                                        if (empty($extracto)) {
                                            $extracto = wp_trim_words(get_post_field('post_content', $curso_id), 15, '...');
                                        }
                                        
                                        if (!empty($extracto)) : ?>
                                            <div class="curso-extracto"><?php echo esc_html($extracto); ?></div>
                                        <?php endif; ?>
                                        
                                        <span class="ver-curso"><?php _e('Ver detalles del curso', 'breogan-lms'); ?></span>
                                    </div>
                                </a>
                            </div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="instructor-sidebar">
            <?php if (!empty($experience) || !empty($specialties)) : ?>
                <div class="instructor-card info-profesional">
                    <h3><?php _e('Información Profesional', 'breogan-lms'); ?></h3>
                    
                    <?php if (!empty($experience)) : ?>
                        <div class="info-item">
                            <span class="info-label"><?php _e('Años de Experiencia:', 'breogan-lms'); ?></span>
                            <span class="info-value"><?php echo esc_html($experience); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($specialties)) : ?>
                        <div class="info-item">
                            <span class="info-label"><?php _e('Especialidades:', 'breogan-lms'); ?></span>
                            <div class="info-value especialidades">
                                <?php 
                                $specialties_array = explode(',', $specialties);
                                foreach ($specialties_array as $specialty) {
                                    echo '<span class="especialidad-tag">' . esc_html(trim($specialty)) . '</span>';
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($email) || !empty($phone) || !empty($website)) : ?>
                <div class="instructor-card contacto">
                    <h3><?php _e('Contacto', 'breogan-lms'); ?></h3>
                    
                    <?php if (!empty($email)) : ?>
                        <div class="info-item">
                            <span class="info-label"><?php _e('Email:', 'breogan-lms'); ?></span>
                            <a href="mailto:<?php echo esc_attr($email); ?>" class="info-value email">
                                <?php echo esc_html($email); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($phone)) : ?>
                        <div class="info-item">
                            <span class="info-label"><?php _e('Teléfono:', 'breogan-lms'); ?></span>
                            <a href="tel:<?php echo esc_attr($phone); ?>" class="info-value phone">
                                <?php echo esc_html($phone); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($website)) : ?>
                        <div class="info-item">
                            <span class="info-label"><?php _e('Sitio Web:', 'breogan-lms'); ?></span>
                            <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer" class="info-value website">
                                <?php echo esc_html(preg_replace('#^https?://#', '', $website)); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="instructor-card cta">
                <a href="<?php echo get_post_type_archive_link('blms_instructor'); ?>" class="btn-cta back">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <?php _e('Ver todos los instructores', 'breogan-lms'); ?>
                </a>
            </div>
        </div>
    </div>
</main>

<style>
/* Estilos para perfil de instructor */
:root {
    --instructor-primary: #6366F1;
    --instructor-primary-hover: #4F46E5;
    --instructor-light-bg: #F3F4F6;
    --instructor-dark-text: #333;
    --instructor-mid-text: #555;
    --instructor-light-text: #777;
    --instructor-border: #E5E7EB;
    --instructor-success: #10B981;
}

/* Modo oscuro */
@media (prefers-color-scheme: dark) {
    :root {
        --instructor-primary: #818cf8;
        --instructor-primary-hover: #6366F1;
        --instructor-light-bg: #2d3748;
        --instructor-dark-text: #ffffff;
        --instructor-mid-text: #e0e0e0;
        --instructor-light-text: #a0aec0;
        --instructor-border: #4a5568;
        --instructor-success: #34d399;
    }
}

.instructor-single {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    color: var(--instructor-dark-text);
}

.instructor-header {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    margin-bottom: 3rem;
    align-items: center;
}

.instructor-imagen-container {
    flex: 0 0 200px;
}

.instructor-imagen {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}

.instructor-imagen-placeholder {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--instructor-light-bg);
    color: var(--instructor-primary);
    font-size: 5rem;
    font-weight: bold;
}

.instructor-info-header {
    flex: 1;
}

.instructor-nombre {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    color: var(--instructor-dark-text);
}

.instructor-titulo {
    font-size: 1.5rem;
    color: var(--instructor-primary);
    margin-bottom: 1rem;
}

.instructor-especialidades {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

.especialidad {
    background-color: var(--instructor-light-bg);
    color: var(--instructor-mid-text);
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.9rem;
}

.instructor-social {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.social-icon {
    color: var(--instructor-light-text);
    transition: color 0.3s ease;
}

.social-icon:hover {
    color: var(--instructor-primary);
}

.instructor-content-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
}

.instructor-main-content {
    flex: 1;
    min-width: 0;
}

.instructor-sidebar {
    flex: 0 0 300px;
}

.instructor-biografia, 
.instructor-formacion {
    margin-bottom: 2.5rem;
}

.instructor-biografia h2, 
.instructor-formacion h2,
.instructor-cursos h2 {
    font-size: 1.75rem;
    margin-bottom: 1rem;
    color: var(--instructor-dark-text);
    border-bottom: 2px solid var(--instructor-border);
    padding-bottom: 0.5rem;
}

.instructor-biografia p, 
.instructor-formacion p {
    margin-bottom: 1rem;
    line-height: 1.7;
    color: var(--instructor-mid-text);
}

.instructor-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.instructor-card h3 {
    font-size: 1.25rem;
    margin-bottom: 1rem;
    color: var(--instructor-dark-text);
    border-bottom: 1px solid var(--instructor-border);
    padding-bottom: 0.5rem;
}

.info-item {
    margin-bottom: 1rem;
}

.info-label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--instructor-dark-text);
}

.info-value {
    color: var(--instructor-mid-text);
}

.especialidades {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.especialidad-tag {
    background-color: var(--instructor-light-bg);
    color: var(--instructor-mid-text);
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.85rem;
}

.info-value.email, 
.info-value.phone, 
.info-value.website {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    color: var(--instructor-primary);
}

.info-value.email:hover, 
.info-value.phone:hover, 
.info-value.website:hover {
    color: var(--instructor-primary-hover);
    text-decoration: underline;
}

.btn-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background-color: var(--instructor-primary);
    color: white;
    padding: 0.75rem 1.25rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.3s ease;
    width: 100%;
}

.btn-cta:hover {
    background-color: var(--instructor-primary-hover);
}

.btn-cta svg {
    width: 20px;
    height: 20px;
}

/* Estilo para los cursos del instructor */
.cursos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 1.5rem;
}

.curso-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.curso-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
}

.curso-link {
    display: block;
    text-decoration: none;
    color: inherit;
}

.curso-imagen {
    height: 160px;
    position: relative;
    overflow: hidden;
}

.curso-imagen img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.curso-card:hover .curso-imagen img {
    transform: scale(1.05);
}

.curso-imagen-placeholder {
    width: 100%;
    height: 100%;
    background-color: var(--instructor-light-bg);
}

.curso-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.8rem;
    font-weight: 600;
}

.curso-badge.gratuito {
    background-color: var(--instructor-success);
    color: white;
}

.curso-badge.precio {
    background-color: var(--instructor-primary);
    color: white;
}

.curso-info {
    padding: 1.25rem;
}

.curso-titulo {
    font-size: 1.1rem;
    margin-bottom: 0.75rem;
    color: var(--instructor-dark-text);
    line-height: 1.4;
}

.curso-extracto {
    font-size: 0.9rem;
    color: var(--instructor-light-text);
    margin-bottom: 1rem;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.ver-curso {
    display: inline-block;
    color: var(--instructor-primary);
    font-weight: 600;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.curso-card:hover .ver-curso {
    color: var(--instructor-primary-hover);
}

/* Responsive */
@media (max-width: 768px) {
    .instructor-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .instructor-imagen-container {
        flex: 0 0 auto;
    }
    
    .instructor-social {
        justify-content: center;
    }
    
    .instructor-sidebar {
        flex: 1 1 100%;
        order: -1; /* Mostrar sidebar primero en móviles */
    }
    
    .cursos-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
}

@media (max-width: 480px) {
    .instructor-nombre {
        font-size: 2rem;
    }
    
    .instructor-titulo {
        font-size: 1.2rem;
    }
    
    .cursos-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php get_footer(); ?>
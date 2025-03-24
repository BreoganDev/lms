<?php
get_header();

$curso_id = get_the_ID();
$user_id = get_current_user_id();
$ha_comprado = get_user_meta($user_id, 'breogan_curso_' . $curso_id, true);
$precio = get_post_meta($curso_id, '_breogan_precio_curso', true);
?>

<main class="contenedor seccion">
    <h1 class="texto-center texto-primary"><?php the_title(); ?></h1>

    <div class="contenido-curso">
        <?php the_content(); ?>
    </div>

    <?php if ($ha_comprado) : ?>
        <p class="mensaje-exito">✅ Ya has comprado este curso. Accede a los temas y lecciones:</p>

        <!-- Mostrar los Temas del Curso -->
        <h2>Temas del Curso</h2>
        <ul class="lista-temas">
            <?php
            $temas = get_posts([
                'post_type'   => 'temas',
                'meta_key'    => '_curso_relacionado',
                'meta_value'  => $curso_id,
                'numberposts' => -1,
                'orderby'     => 'menu_order',
                'order'       => 'ASC'
            ]);

            if ($temas) {
                foreach ($temas as $tema) {
                    echo '<li><a href="' . get_permalink($tema->ID) . '">' . get_the_title($tema->ID) . '</a></li>';
                }
            } else {
                echo '<p>No hay temas disponibles en este curso.</p>';
            }
            ?>
        </ul>

    <?php else : ?>
        <p class="alert-compra-curso">Para acceder al contenido de este curso, debes comprarlo.</p>

        <!-- Botón de Stripe -->
        <form id="breogan-pago-stripe" method="POST">
            <input type="hidden" name="action" value="breogan_procesar_pago_stripe_ajax">
            <input type="hidden" name="curso_id" value="<?php echo esc_attr($curso_id); ?>">
            <input type="hidden" name="precio" value="<?php echo esc_attr($precio); ?>">
            <button type="submit" class="btn-pago stripe-btn">Pagar con Stripe</button>
        </form>

        <!-- Botón de PayPal -->
        <form id="breogan-pago-paypal" method="POST">
            <input type="hidden" name="action" value="breogan_procesar_pago_paypal_ajax">
            <input type="hidden" name="curso_id" value="<?php echo esc_attr($curso_id); ?>">
            <input type="hidden" name="precio" value="<?php echo esc_attr($precio); ?>">
            <button type="submit" class="btn-pago paypal-btn">Pagar con PayPal</button>
        </form>
    <?php endif; ?>
</main>

<script>
function procesarPago(formId) {
    let form = document.getElementById(formId);
    let formData = new FormData(form);

    console.log("🔹 Enviando datos de pago:", Object.fromEntries(formData));

    fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
        method: "POST",
        body: formData,
        credentials: "same-origin"
    })
    .then(response => response.json())
    .then(data => {
        console.log("🔹 Respuesta AJAX:", data);
        
        // Corregir acceso a la URL de redirección
        let redirectUrl = data.redirect_url || data.data?.redirect_url || null;

        if (data.success && redirectUrl) {
            console.log("✅ Redirigiendo a:", redirectUrl);
            setTimeout(() => {
                window.location.href = redirectUrl;
            }, 500);
        } else {
            console.error("❌ Error en el pago:", data.error || "URL de redirección vacía.");
            alert("Error en el pago: " + (data.error || "No se pudo procesar la solicitud."));
        }
    })
    .catch(error => {
        console.error("❌ Error en la solicitud AJAX:", error);
        alert("Hubo un error en la solicitud. Por favor, intenta de nuevo.");
    });
}

document.getElementById("breogan-pago-stripe").addEventListener("submit", function(event) {
    event.preventDefault();
    procesarPago("breogan-pago-stripe");
});

document.getElementById("breogan-pago-paypal").addEventListener("submit", function(event) {
    event.preventDefault();
    procesarPago("breogan-pago-paypal");
});
</script>


<?php get_footer(); ?>

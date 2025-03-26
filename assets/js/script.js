jQuery(document).ready(function($) {
    // Ejemplo de script para Breogan LMS
    $('.breogan-lms-courses-list li').click(function() {
        alert('Curso seleccionado: ' + $(this).text());
    });
});

document.addEventListener("DOMContentLoaded", function() {
    let boton = document.getElementById("marcar-completada");

    if (boton) {
        boton.addEventListener("click", function() {
            let leccion_id = this.getAttribute("data-leccion");

            fetch(breoganLMS.ajaxurl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `action=marcar_leccion_completada&leccion_id=${leccion_id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    boton.outerHTML = '<p class="completado">✅ Has completado esta lección.</p>';
                } else {
                    alert("Hubo un error. Intenta de nuevo.");
                }
            });
        });
    }
});

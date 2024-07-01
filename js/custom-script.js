document.addEventListener('DOMContentLoaded', function() {
    // Seleciona o formulário
    const form = document.querySelector('.elementor-form');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        // Captura os dados do formulário
        const firstName = form.querySelector('input[name="first_name"]').value;
        const birthDate = form.querySelector('input[name="birth_date"]').value;

        // Envia os dados via AJAX para armazenar na sessão
        jQuery.ajax({
            url: my_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'store_user_data',
                first_name: firstName,
                birth_date: birthDate
            },
            success: function(response) {
                // Redireciona para a próxima página
                window.location.href = '/form-02'; // Substitua pela URL real da próxima página
            }
        });
    });
});

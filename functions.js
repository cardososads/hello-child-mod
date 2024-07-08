jQuery(document).ready(function($){
    $('#form1').on('submit', function(e){
        e.preventDefault();

        var formData = $(this).serializeArray();
        var numeroDestino = calcularNumeroDestino(formData); // Função para calcular o número de destino

        window.location.href = '/form2?numero_destino=' + numeroDestino;
    });
    
    // Similar logic for form2 and form3 submissions
});
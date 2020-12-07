class EntradaClienteController {
    
    pesquisar(e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: `${BASE_URL}/entrada-cliente/pesquisar`,
            data: $('#form').serialize(),
            dataType: 'json',
            success: function(json) {

            }
        })
    }
    
}
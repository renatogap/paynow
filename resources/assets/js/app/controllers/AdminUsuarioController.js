class AdminUsuarioController {
    constructor() {
        this._tabela = document.getElementById('grid');
        //this._reativar = document.getElementById('reativar');
        //this._reativarTooltip = this._reativar.Tooltip;
    }
    
    pesquisar(e) {
        e.preventDefault();
        oTable.draw();
    }  
    
    criar() {
        window.location = `${BASE_URL}admin/usuario/criar`;
    }

    /*editar(e) {
        if (oTable.rows('.selected').data()[0] != null) {
            window.location = `${e.dataset.url}/admin/usuario/editar/` + oTable.rows('.selected').data()[0].id;
        } else {
            alert('Selecione um item');
            return false;
        }
    }*/
        
    excluir(id) {
        //let linha = oTable.rows('.selected').data()[0];
        
        //if (linha !== null) {
            if (confirm('Deseja excluir este item?')){
                        
                $.ajax({
                    url: `${BASE_URL}/admin/usuario/excluir/`+id,
                    type: 'post',
                    data: {
                        id: id,
                        _token: document.getElementsByName('_token')[0].value
                    },
                    success: (response) => {
                        alert('Usuário removido com sucesso.');
                        window.location.reload();
                    },
                    error: (response) => {
                        alert(response.message);
                        //this._excluir = false;
                    }
                });
            }
        //} else
        //    alert('Selecione uma item para excluir');

        return false;
    }

    reativar(e) {
        let linha = oTable.rows('.selected').data()[0];
        Ajax.ajax({
            url: `${e.dataset.url}/admin/usuario/reativar/`+[linha.id],
            method: 'post',
            data: { usuario: linha.id },
            beforeSend: () => {
                this._reativar = true;
            },
            success: (response) => {
                alert(response.message);
                this._reativarTooltip.hide();
                oTable.draw(); 
            },
            error: function (request) {
                alert(request.message);
            }
        });
    }
}
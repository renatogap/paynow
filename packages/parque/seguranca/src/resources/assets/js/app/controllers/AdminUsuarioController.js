class AdminUsuarioController {
    constructor() {
        this._tabela = document.getElementById('grid');
        this._reativar = document.getElementById('reativar');
        this._reativarTooltip = this._reativar.Tooltip;
    }
    
    pesquisar(e) {
        e.preventDefault();
        oTable.draw();
    }  
    
    criar() {
        window.location = `${BASE_URL}admin/usuario/criar`;
    }

    editar(e) {
        if (oTable.rows('.selected').data()[0] != null) {
            window.location = `${e.dataset.url}/admin/usuario/editar/` + oTable.rows('.selected').data()[0].id;
        } else {
            alert('Selecione um item');
            return false;
        }
    }
        
    excluir(e) {
        let linha = oTable.rows('.selected').data()[0];
        
        if (linha !== null) {
            if (confirm('Deseja excluir este item?'))
                        
            Ajax.ajax({
                url: `${e.dataset.url}/admin/usuario/excluir/`+[linha.id],
                method: 'post',
                data: {id: linha.id},
                success: (response) => {
                    alert(response.message);
                    this._excluir = true;
                    this._excluir.Tooltip.hide();
                    oTable.draw();
                },
                error: (response) => {
                    alert(response.message);
                    this._excluir = false;
                }
            });
        } else
            alert('Selecione uma item para excluir');

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
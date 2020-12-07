class PesquisarMenuController {
    
    pesquisar(e) {
        e.preventDefault();
        oTable.draw();
    }
    criar(e) {
        window.location = `${e.dataset.url}/seguranca/menu/novo`;
    }
    editar(e) {
        if (oTable.rows('.selected').data()[0] != null) {
            window.location = `${e.dataset.url}/seguranca/menu/editar/` + oTable.rows('.selected').data()[0].id;
        } else
            alert('Selecione um item');
        return false;
    }
    excluir(e) {
        let linha = oTable.rows('.selected').data()[0];

        if (linha !== null) {

            if (confirm('Deseja excluir este item?'))

                Ajax.ajax({
                    url: `${e.dataset.url}`,
                    method: 'POST',
                    data: {id: linha.id},
                    beforeSend: () => {
                        this._excluir.disabled = true;
                    },
                    success: (json) => {
                        alert(json.msg);
                        this._excluir.Tooltip.hide();
                        oTable.draw();
                    },
                    error: (json) => {
                        alert(json.msg);
                        this._excluir.disabled = false;
                    }
                });
        } else
            alert('Selecione uma item para excluir');

        return false;
    }
}
oController = new PesquisarMenuController();
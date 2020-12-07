class PesquisarUsuarioController {

    pesquisar(e) {
        e.preventDefault();
        oTable.draw();
    }
    criar(e) {
        window.location = `${e.dataset.url}/seguranca/usuario/novo`;
    }
    editar(e) {
        if (oTable.rows('.selected').data()[0] != null) {
            window.location = `${e.dataset.url}/seguranca/usuario/editar/` + oTable.rows('.selected').data()[0].id;
        } else
            alert('Selecione um item');
        return false;
    }
    excluir(e) {
        let linha = oTable.rows('.selected').data()[0];

        if (linha !== null) {

            if (confirm('Deseja excluir este item?'))

                Ajax.ajax({
                    url: `${e.dataset.url}/seguranca/usuario/excluir`+[linha.id],
                    method: 'post',
                    data: {id: linha.id},
                    beforeSend: () => {
                        this._excluir = true;
                    },
                    success: (json) => {
                        alert(json.msg);
                        this._excluir.Tooltip.hide();
                        oTable.draw();
                    },
                    error: (json) => {
                        alert(json.msg);
                        this._excluir = false;
                    }
                });
        } else
            alert('Selecione uma item para excluir');

        return false;
    }
}

/**
 * Esta classe cria uma lista html dos erros que podem acontecer nas validações feitas pelo Laravel
 */
class ListaDeErros {

    // constructor(json) {
    //
    // }

    static gerarListaErro(json) {

        this._listaErros = typeof json.errors !== 'undefined' ? json.errors : [];

        if(this._listaErros.length === 1) {
            alert(this._listaErros.message);
            return `<ul><li>${this._listaErros.message}</li></ul>`;
        }

        let li = [];
        for (let er in this._listaErros) {

            li.push(`<li>${this._listaErros[er]}</li>`);
        }

        return `<ul>${li.join('')}</ul>`;
    }
}
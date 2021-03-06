<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SegAcaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('seg_acao')->delete();

        $items = [
            [
                'nome' => 'seguranca/usuario/home',
                'descricao' => 'Tela inicial do usuário logado',
                'destaque' => false,
                'nome_amigavel' => 'Home do usuário logado',
                'obrigatorio' => true,
                'grupo' => 'Segurança obrigatório',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/usuario/login',
                'descricao' => 'Ação de logar no sistema',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => true,
                'grupo' => 'Segurança obrigatório',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/usuario/logout',
                'descricao' => 'Ação de sair do sistema',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => true,
                'grupo' => 'Segurança obrigatório',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/usuario/alterar-senha',
                'descricao' => 'Tela de mudança de senha do usuário',
                'destaque' => false,
                'nome_amigavel' => 'Alterar senha',
                'obrigatorio' => true,
                'grupo' => 'Segurança obrigatório',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/usuario/atualizar-senha',
                'descricao' => 'Ajax que atualiza senha de um usuário',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => true,
                'grupo' => 'Segurança obrigatório',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/usuario/atualizar-dados',
                'descricao' => 'Obriga o usuário a ter sempre cpf e data de nascimento atualizados',
                'destaque' => false,
                'nome_amigavel' => 'CPF e Nascimento obrigatórios',
                'obrigatorio' => true,
                'grupo' => 'Segurança obrigatório',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/acao',
                'descricao' => 'Tela de administração de ações',
                'destaque' => true,
                'nome_amigavel' => 'Pesquisar ações',
                'obrigatorio' => false,
                'grupo' => 'Segurança ação',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/acao/grid',
                'descricao' => 'Ajax que atualiza grid na tela de pesquisa de ações',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Segurança ação',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/acao/novo',
                'descricao' => 'Tela de cadastro de ações',
                'destaque' => true,
                'nome_amigavel' => 'Cadastrar ações',
                'obrigatorio' => false,
                'grupo' => 'Segurança ação',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/acao/store',
                'descricao' => 'Ajax que cria uma ação no banco de dados',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Segurança ação',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/acao/editar/{id}',
                'descricao' => 'Tela de alteração de ação',
                'destaque' => true,
                'nome_amigavel' => 'Alterar ações',
                'obrigatorio' => false,
                'grupo' => 'Segurança ação',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/acao/update',
                'descricao' => 'Ajax que atualiza uma ação no banco de dados',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Segurança ação',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/acao/excluir',
                'descricao' => 'Ajax que remove uma ação no banco de dados',
                'destaque' => true,
                'nome_amigavel' => 'Excluir ações',
                'obrigatorio' => false,
                'grupo' => 'Segurança ação',
                'created_at' => date('Y-m-d H:i:s'),
            ],

            //menu
            [
                'nome' => 'seguranca/menu',
                'descricao' => 'Tela de administração de menus',
                'destaque' => true,
                'nome_amigavel' => 'Pesquisar menus',
                'obrigatorio' => false,
                'grupo' => 'Segurança menu',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/menu/grid',
                'descricao' => 'Ajax que atualiza grid na tela de pesquisa de menus',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Segurança menu',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/menu/novo',
                'descricao' => 'Tela de cadastro de menus',
                'destaque' => true,
                'nome_amigavel' => 'Cadastrar menus',
                'obrigatorio' => false,
                'grupo' => 'Segurança menu',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/menu/store',
                'descricao' => 'Ajax que cria um menu no banco de dados',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Segurança menu',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/menu/editar/{id}',
                'descricao' => 'Tela de alteração de menu',
                'destaque' => true,
                'nome_amigavel' => 'Alterar menus',
                'obrigatorio' => false,
                'grupo' => 'Segurança menu',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/menu/update',
                'descricao' => 'Ajax que atualiza um menu no banco de dados',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Segurança menu',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/menu/excluir',
                'descricao' => 'Ajax que remove um menu no banco de dados',
                'destaque' => true,
                'nome_amigavel' => 'Excluir menu',
                'obrigatorio' => false,
                'grupo' => 'Segurança menu',
                'created_at' => date('Y-m-d H:i:s'),
            ],

            //usuario
            [
                'nome' => 'seguranca/usuario/admin',
                'descricao' => 'Tela de administração de usuários',
                'destaque' => true,
                'nome_amigavel' => 'Pesquisar usuários',
                'obrigatorio' => false,
                'grupo' => 'Segurança usuário',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/usuario/grid',
                'descricao' => 'Ajax que atualiza grid na tela de pesquisa de usuários',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Segurança usuário',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/usuario/novo',
                'descricao' => 'Tela de cadastro de usuários',
                'destaque' => true,
                'nome_amigavel' => 'Cadastrar usuários',
                'obrigatorio' => false,
                'grupo' => 'Segurança usuário',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/usuario/store',
                'descricao' => 'Ajax que cria um usuário no banco de dados',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Segurança usuário',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/usuario/editar/{id}',
                'descricao' => 'Tela de alteração de usuário',
                'destaque' => true,
                'nome_amigavel' => 'Alterar usuários',
                'obrigatorio' => false,
                'grupo' => 'Segurança usuário',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/usuario/update',
                'descricao' => 'Ajax que atualiza um usuário no banco de dados',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Segurança usuário',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/usuario/excluir',
                'descricao' => 'Ajax que remove um usuário no banco de dados (apenas marca como excluído)',
                'destaque' => true,
                'nome_amigavel' => 'Excluir menu',
                'obrigatorio' => false,
                'grupo' => 'Segurança usuário',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/usuario',
                'descricao' => 'Tela de administração local de usuários',
                'destaque' => true,
                'nome_amigavel' => 'Pesquisar Usuários',
                'obrigatorio' => false,
                'grupo' => 'Usuário',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/usuario/grid',
                'descricao' => 'Ajax que popula o grid',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Usuário',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/usuario/criar',
                'descricao' => 'Tela de cadastro de usuário',
                'destaque' => true,
                'nome_amigavel' => 'Cadastrar Usuário',
                'obrigatorio' => false,
                'grupo' => 'Usuário',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/usuario/store',
                'descricao' => 'Ajax que cadastra um usuário local',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Usuário',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/usuario/editar/{usuario}',
                'descricao' => 'Tela de edição de usuário',
                'destaque' => true,
                'nome_amigavel' => 'Alterar Usuário',
                'obrigatorio' => false,
                'grupo' => 'Usuário',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/usuario/update',
                'descricao' => 'Ajax que atualiza um usuário',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Usuário',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/usuario/excluir/{usuario}',
                'descricao' => 'Remove o acesso de um detemrinado usuário a este sistema',
                'destaque' => true,
                'nome_amigavel' => 'Excluir de usuários',
                'obrigatorio' => false,
                'grupo' => 'Usuário',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/perfil',
                'descricao' => 'Tela de administração de perfis',
                'destaque' => true,
                'nome_amigavel' => 'Pesquisar Perfis',
                'obrigatorio' => false,
                'grupo' => 'Perfil',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/perfil/grid',
                'descricao' => 'Ajax que popula o grid de pesquisa de perfil',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Perfil',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/perfil/novo',
                'descricao' => 'Tela de cadastro de perfil',
                'destaque' => true,
                'nome_amigavel' => 'Cadastrar Perfil',
                'obrigatorio' => false,
                'grupo' => 'Perfil',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/perfil/store',
                'descricao' => 'Ajax que salva um perfil no banco',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Perfil',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/perfil/editar/{perfil}',
                'descricao' => 'Tela de edição de perfil',
                'destaque' => true,
                'nome_amigavel' => 'Alterar Perfil',
                'obrigatorio' => false,
                'grupo' => 'Perfil',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/perfil/update',
                'descricao' => 'Ajax que salva um perfil no banco',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Perfil',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/perfil/excluir/{perfil}',
                'descricao' => 'Exclui um perfil cadastrado. Isto remove automaticamente as permissões dos usuários',
                'destaque' => true,
                'nome_amigavel' => 'Excluir Perfil',
                'obrigatorio' => false,
                'grupo' => 'Perfil',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/usuario/info',
                'descricao' => 'Baixa informações do RH sobre um determinado cpf',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Usuário',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/solicitar-acesso',
                'descricao' => 'Tela de administração de requisição de login',
                'destaque' => true,
                'nome_amigavel' => 'Requisição de Login',
                'obrigatorio' => false,
                'grupo' => 'Solicitação Acesso',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/solicitar-acesso/grid',
                'descricao' => 'Ajax que popula o grid de solicitação de acesso',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Solicitação Acesso',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/solicitar-acesso/editar/{id}',
                'descricao' => 'Tela de edição de solicitação de acesso',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Solicitação Acesso',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/solicitar-acesso/store',
                'descricao' => 'Ajax que efetiva o usuário que solicitou novo usuário usando pré-cadastro',
                'destaque' => false,
                'nome_amigavel' => null,
                'obrigatorio' => false,
                'grupo' => 'Solicitação Acesso',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'admin/solicitar-acesso/excluir',
                'descricao' => 'Ajax que exclui um pré-cadastro',
                'destaque' => true,
                'nome_amigavel' => 'Excluir uma solicitação de acesso',
                'obrigatorio' => false,
                'grupo' => 'Solicitação Acesso',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'seguranca/usuario/dashboard',
                'descricao' => 'Ajax que exclui um pré-cadastro',
                'destaque' => true,
                'nome_amigavel' => 'Tela inicial modificada do usuário logado',
                'obrigatorio' => true,
                'grupo' => 'Solicitação Acesso',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        DB::table('seg_acao')->insert($items);

        //pulando sequence para o número mil para deixar reservado para o segurança
        //DB::statement("select setval('seg_acao_id_seq', 1000, true);");
    }
}

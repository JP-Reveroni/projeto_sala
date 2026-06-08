<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Sistema Mapa de Sala</title>
    <link rel="icon" href="../../projeto_sala/assets/img/icone_fatecSR.ico" type="image/x-icon">
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-kfkfwYDsLkIlwQp6LFn18zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXge10g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />-->
    <link rel="stylesheet" href="../../projeto_sala/assets/css/reset.css">
    <link rel="stylesheet" href="../../projeto_sala/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../projeto_sala/assets/css/styleCadastro.css">
    <link rel="stylesheet" href="../../projeto_sala/assets/css/stylePassword.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

</head>

<body>
    <div class="container">
        <!-- Logo acima do formulário -->
        <div class="text-center">
            <img src="../../projeto_sala/assets/img/logo_fatecSR.png" alt="Logo da Empresa" style="max-width: 200px;
                    margin-bottom: 20px;">
        </div>
        <div class="panel-body">
            <form autocomplete="off" id="login">
                <fieldset>
                    <div class="form-group">
                        <input class="form-control" placeholder="Usuario" id="txtUsuario" name="txtUsuario"
                            type="text" autofocus required>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input class="form-control" placeholder="Senha" id="txtSenha" name="txtSenha" type="password" required>
                            <div class="input-group-append">
                                <i id="togglePassword" class="fas fa-eye"></i>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="btnEntrar" class="btn btn-block btnAcao" onclick="validaLogin()">Entrar</button>
                </fieldset>
            </form>
        </div>
    </div>
    <script src="../../projeto_sala/assets/js/jquery-3.6.0.min.js"></script>
    <script src="../../projeto_sala/assets/js/bootstrap.min.js"></script>
    <script src="../../projeto_sala/assets/js/sweetalert2.all.min.js"></script>

    <script>
        async function validaLogin() {
            event.preventDefault();
            try {
                const usuario = document.getElementById('txtUsuario').value;
                const senha = document.getElementById('txtSenha').value;
                const base_url = function(url = '') {
                    return "<?= base_url() ?>" + url
                }

                const response = await fetch('Usuario/logar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        usuario: usuario,
                        senha: senha
                    })
                });

                const result = await response.json();

                if (result.codigo == 1) {
                    //Mostrar uma mensagem de sucesso
                    Swal.fire('Sucesso!', result.msg, 'success');

                    //Atualizar a tabela
                    window.location.href = base_url("/Funcoes/indexPagina")
                } else {
                    // 1. Mapeia e junta as mensagens de erro em um bloco HTML
                    const mensagensDeErro = result.erros.map(erro => {
                        return `<p><strong>[${erro.campo ?? erro.codigo}]</strong> ${erro.msg}</p>`;
                    }).join('');

                    //2. chama o Swal.fire usandoa propriedade 'html'
                    Swal.fire({
                        title: 'Houve(ram) erro(s) de validação:',
                        html: mensagensDeErro, // 'html' exibi as tags <p> e <strong>
                        icon: 'error',
                        confirmButtonText: 'Fechar'
                    });
                }
            } catch (error) {
                console.error('Errou', error);
                Swal.fire('Erro', error.message, 'error');
            }
        }

        // Script para alternar a visibilidade da senha
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('txtSenha');

        togglePassword.addEventListener('click', function() {
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;

            //Alterna o icone do olho
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>
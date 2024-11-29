<!-- Modal de Usuário -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="userForm" method="post" class="needs-validation" novalidate>
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="user_id" id="userId" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Novo Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuário</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div class="invalid-feedback">
                            Por favor, insira um nome de usuário.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">
                            Por favor, insira um email válido.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                        <div class="invalid-feedback">
                            Por favor, insira o nome completo.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="6">
                        <div class="form-text" id="passwordHelp">
                            Mínimo de 6 caracteres. Deixe em branco para manter a senha atual ao editar.
                        </div>
                        <div class="invalid-feedback">
                            A senha deve ter no mínimo 6 caracteres.
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin">
                            <label class="form-check-label" for="is_admin">Administrador</label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="user_id" id="deleteUserId">

                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Tem certeza que deseja excluir o usuário <strong id="deleteUserName"></strong>?</p>
                    <p class="text-danger">Esta ação não pode ser desfeita!</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Validação do formulário
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()

// Funções do modal
function editUser(userId) {
    fetch(`get_user.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showAlert('error', data.error);
                return;
            }
            
            document.getElementById('formAction').value = 'update';
            document.getElementById('userId').value = data.id;
            document.getElementById('username').value = data.username;
            document.getElementById('email').value = data.email;
            document.getElementById('full_name').value = data.full_name;
            document.getElementById('is_admin').checked = data.is_admin == 1;
            document.getElementById('password').required = false;
            document.getElementById('modalTitle').textContent = 'Editar Usuário';
            
            const modal = new bootstrap.Modal(document.getElementById('addUserModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Erro ao carregar dados do usuário:', error);
            showAlert('error', 'Erro ao carregar dados do usuário');
        });
}

function deleteUser(userId) {
    fetch(`get_user.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showAlert('error', data.error);
                return;
            }
            
            document.getElementById('deleteUserId').value = userId;
            document.getElementById('deleteUserName').textContent = data.username;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Erro ao carregar dados do usuário:', error);
            showAlert('error', 'Erro ao carregar dados do usuário');
        });
}

// Reset form on modal close
document.getElementById('addUserModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('userForm').reset();
    document.getElementById('formAction').value = 'create';
    document.getElementById('userId').value = '';
    document.getElementById('password').required = true;
    document.getElementById('modalTitle').textContent = 'Novo Usuário';
    document.getElementById('userForm').classList.remove('was-validated');
});
</script>

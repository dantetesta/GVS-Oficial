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
// Funções do modal
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
</script>

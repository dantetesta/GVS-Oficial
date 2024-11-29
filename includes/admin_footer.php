            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">Meu Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="profileForm" action="profile.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="user_id" value="<?php echo $user->getCurrentUserId(); ?>">
                        <input type="hidden" name="action" value="update">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nome de Usuário</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($userData['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?php echo htmlspecialchars($userData['full_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nova Senha (opcional)</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" 
                                   minlength="6">
                            <div class="form-text">Deixe em branco para manter a senha atual</div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   minlength="6">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer" style="position: fixed; top: 20px; right: 20px; z-index: 1050;"></div>

    <footer class="footer mt-auto py-3 bg-dark text-white">
        <div class="container text-center">
            <p class="mb-0">Desenvolvido por: <a href="https://www.dantetesta.com.br" class="text-white" target="_blank">Dante Testa</a></p>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
            
            const wrapper = document.getElementById('wrapper');
            wrapper.classList.toggle('toggled');
            
            // Ajusta o layout após a transição
            setTimeout(() => {
                window.dispatchEvent(new Event('resize'));
            }, 250);
        });

        // Restaura o estado do sidebar
        if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
            document.body.classList.add('sb-sidenav-toggled');
            document.getElementById('wrapper').classList.add('toggled');
        }

        // Profile Form Submit
        $(document).ready(function() {
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validar senha se foi preenchida
                const newPassword = $('#new_password').val();
                const confirmPassword = $('#confirm_password').val();
                
                if (newPassword && newPassword !== confirmPassword) {
                    showAlert('danger', 'As senhas não coincidem!');
                    return;
                }
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#profileModal').modal('hide');
                            $('#currentUsername').text($('#username').val());
                            showAlert('success', response.message);
                        } else {
                            showAlert('danger', response.message);
                        }
                    },
                    error: function() {
                        showAlert('danger', 'Erro ao atualizar perfil. Tente novamente.');
                    }
                });
            });
        });

        // Função para mostrar alertas
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            $('#alertContainer').append(alertHtml);
            
            // Remove o alerta após 5 segundos
            setTimeout(function() {
                $('#alertContainer .alert').first().alert('close');
            }, 5000);
        }
    </script>
</body>
</html>

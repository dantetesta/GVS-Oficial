            </div> <!-- Fechamento do container-fluid -->
        </div> <!-- Fechamento do page-content-wrapper -->
    </div> <!-- Fechamento do wrapper -->

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom Scripts -->
    <script>
    // Toggle Sidebar
    document.getElementById('sidebarToggle').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('wrapper').classList.toggle('toggled');
    });

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

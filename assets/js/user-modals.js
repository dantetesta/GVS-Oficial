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

// Inicialização dos modais
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar todos os modais
    var modals = document.querySelectorAll('.modal')
    modals.forEach(function(modal) {
        new bootstrap.Modal(modal)
    })

    // Event listeners para botões de edição
    document.querySelectorAll('.edit-user-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            const userId = this.dataset.id
            editUser(userId)
        })
    })

    // Event listeners para botões de exclusão
    document.querySelectorAll('.delete-user-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            const userId = this.dataset.id
            const userName = this.dataset.name
            confirmDelete(userId, userName)
        })
    })
})

// Função para editar usuário
function editUser(userId) {
    fetch(BASE_URL + '/api/users/get.php?id=' + userId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.data
                document.getElementById('userId').value = user.id
                document.getElementById('username').value = user.username
                document.getElementById('email').value = user.email
                document.getElementById('full_name').value = user.full_name
                document.getElementById('is_admin').checked = user.is_admin == 1
                document.getElementById('password').value = ''
                document.getElementById('formAction').value = 'update'
                document.getElementById('modalTitle').textContent = 'Editar Usuário'
                
                // Abrir modal
                const modal = new bootstrap.Modal(document.getElementById('addUserModal'))
                modal.show()
            } else {
                showAlert(data.message || 'Erro ao carregar usuário', 'danger')
            }
        })
        .catch(error => {
            console.error('Error:', error)
            showAlert('Erro ao carregar usuário', 'danger')
        })
}

// Função para confirmar exclusão
function confirmDelete(userId, userName) {
    document.getElementById('deleteUserId').value = userId
    document.getElementById('deleteUserName').textContent = userName
    const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'))
    modal.show()
}

// Função para mostrar alertas
function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `
    document.getElementById('alertContainer').innerHTML = alertHtml
}

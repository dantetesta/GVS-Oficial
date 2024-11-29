$(document).ready(function() {
    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap-5'
    });

    // Carregar lista de clientes
    loadCustomers();

    // Carregar lista de produtos
    loadProducts();

    // Carregar vendas
    loadSales();

    // Event Listeners
    $('#addProduct').click(addProductRow);
    $('#productsList').on('click', '.remove-product', removeProductRow);
    $('#productsList').on('change', '.select2-products', updateProductPrice);
    $('#productsList').on('input', '.product-quantity', updateTotals);
    $('#discount').on('input', updateTotals);
    $('#saveSale').click(saveSale);
    $('#updateSale').click(updateSale);
    $('#refreshSales').click(loadSales);
    $('#clearFilter').click(clearFilters);
    $('#applyFilter').click(applyFilters);
    $('#printSale').click(printSale);
});

// Funções AJAX
function loadCustomers() {
    $.get(BASE_URL + '/api/customers/get.php', function(response) {
        if (response.success) {
            let options = '<option value="">Selecione um cliente</option>';
            response.data.forEach(function(customer) {
                options += `<option value="${customer.id}">${customer.name}</option>`;
            });
            $('#customer, #filter_customer').html(options);
        }
    });
}

function loadProducts() {
    $.get(BASE_URL + '/api/products/get.php', function(response) {
        if (response.success) {
            let options = '<option value="">Selecione um produto</option>';
            response.data.forEach(function(product) {
                options += `<option value="${product.id}" data-price="${product.price}">${product.name}</option>`;
            });
            $('.select2-products').html(options);
        }
    });
}

function loadSales(page = 1) {
    const filters = getFilters();
    $.get(BASE_URL + '/api/sales/get.php', { page, ...filters }, function(response) {
        if (response.success) {
            updateSalesTable(response.data);
            updatePagination(response);
        }
    });
}

// Funções de UI
function addProductRow() {
    const newRow = `
        <div class="row product-item mb-2">
            <div class="col-md-5">
                <select class="form-select select2-products" name="products[]" required>
                    <option value="">Selecione um produto</option>
                    ${$('.select2-products').first().html()}
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control product-quantity" name="quantities[]" placeholder="Qtd" min="1" required>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control product-price" name="prices[]" placeholder="Preço" step="0.01" required readonly>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-product">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    $('#productsList').append(newRow);
    $('.select2-products').last().select2({
        theme: 'bootstrap-5'
    });
}

function removeProductRow() {
    $(this).closest('.product-item').remove();
    updateTotals();
}

function updateProductPrice() {
    const row = $(this).closest('.product-item');
    const price = $(this).find(':selected').data('price') || '';
    row.find('.product-price').val(price);
    updateTotals();
}

function updateTotals() {
    let subtotal = 0;
    $('.product-item').each(function() {
        const price = parseFloat($(this).find('.product-price').val()) || 0;
        const quantity = parseInt($(this).find('.product-quantity').val()) || 0;
        subtotal += price * quantity;
    });

    const discount = parseFloat($('#discount').val()) || 0;
    const total = subtotal - discount;

    $('#subtotal').val(formatCurrency(subtotal));
    $('#total').val(formatCurrency(total));
}

function updateSalesTable(sales) {
    let html = '';
    sales.forEach(function(sale) {
        html += `
            <tr>
                <td>${sale.id}</td>
                <td>${sale.customer_name}</td>
                <td>${formatDate(sale.created_at)}</td>
                <td>${formatCurrency(sale.total_amount)}</td>
                <td><span class="badge bg-${getStatusBadge(sale.status)}">${sale.status}</span></td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info" onclick="viewSale(${sale.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="editSale(${sale.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="cancelSale(${sale.id})">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    $('#salesTable tbody').html(html);
}

function updatePagination(response) {
    let html = '';
    const totalPages = response.total_pages;
    const currentPage = response.page;

    // Previous
    html += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadSales(${currentPage - 1})">Anterior</a>
        </li>
    `;

    // Pages
    for (let i = 1; i <= totalPages; i++) {
        html += `
            <li class="page-item ${currentPage === i ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadSales(${i})">${i}</a>
            </li>
        `;
    }

    // Next
    html += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadSales(${currentPage + 1})">Próxima</a>
        </li>
    `;

    $('#salesTablePagination').html(html);
    $('#salesTableInfo').text(`Mostrando ${response.data.length} de ${response.total} registros`);
}

// Funções de Ação
function saveSale() {
    const formData = new FormData($('#newSaleForm')[0]);
    const data = {
        customer_id: formData.get('customer_id'),
        products: [],
        quantities: [],
        prices: [],
        payment_method: formData.get('payment_method'),
        discount: formData.get('discount'),
        notes: formData.get('notes')
    };

    // Coletar produtos
    $('.product-item').each(function() {
        const product = $(this).find('.select2-products').val();
        const quantity = $(this).find('.product-quantity').val();
        const price = $(this).find('.product-price').val();

        if (product && quantity && price) {
            data.products.push(product);
            data.quantities.push(quantity);
            data.prices.push(price);
        }
    });

    $.post(BASE_URL + '/api/sales/create.php', data, function(response) {
        if (response.success) {
            showAlert('Venda registrada com sucesso!', 'success');
            $('#newSaleModal').modal('hide');
            loadSales();
        } else {
            showAlert(response.message || 'Erro ao registrar venda', 'danger');
        }
    });
}

function updateSale() {
    const formData = new FormData($('#editSaleForm')[0]);
    const data = {
        sale_id: formData.get('sale_id'),
        status: formData.get('status'),
        payment_method: formData.get('payment_method'),
        discount: formData.get('discount'),
        notes: formData.get('notes')
    };

    $.post(BASE_URL + '/api/sales/update.php', data, function(response) {
        if (response.success) {
            showAlert('Venda atualizada com sucesso!', 'success');
            $('#editSaleModal').modal('hide');
            loadSales();
        } else {
            showAlert(response.message || 'Erro ao atualizar venda', 'danger');
        }
    });
}

function viewSale(id) {
    $.get(BASE_URL + '/api/sales/get.php', { id }, function(response) {
        if (response.success) {
            const sale = response.data;
            
            // Preencher informações básicas
            $('#view_sale_id').text(sale.id);
            $('#view_sale_date').text(formatDate(sale.created_at));
            $('#view_sale_status').html(`<span class="badge bg-${getStatusBadge(sale.status)}">${sale.status}</span>`);
            $('#view_payment_method').text(sale.payment_method);
            $('#view_customer_name').text(sale.customer_name);
            $('#view_customer_email').text(sale.customer_email);
            $('#view_customer_phone').text(sale.customer_phone);
            
            // Preencher produtos
            let productsHtml = '';
            let subtotal = 0;
            sale.items.forEach(function(item) {
                const total = item.price * item.quantity;
                subtotal += total;
                productsHtml += `
                    <tr>
                        <td>${item.product_name}</td>
                        <td>${item.quantity}</td>
                        <td>${formatCurrency(item.price)}</td>
                        <td>${formatCurrency(total)}</td>
                    </tr>
                `;
            });
            $('#view_products_list').html(productsHtml);
            
            // Preencher totais
            $('#view_subtotal').text(formatCurrency(subtotal));
            $('#view_discount').text(formatCurrency(sale.discount));
            $('#view_total').text(formatCurrency(sale.total_amount));
            
            // Preencher observações
            $('#view_notes').text(sale.notes || '-');
            
            $('#viewSaleModal').modal('show');
        }
    });
}

function editSale(id) {
    $.get(BASE_URL + '/api/sales/get.php', { id }, function(response) {
        if (response.success) {
            const sale = response.data;
            
            $('#edit_sale_id').val(sale.id);
            $('#edit_status').val(sale.status);
            $('#edit_payment_method').val(sale.payment_method);
            $('#edit_discount').val(sale.discount);
            $('#edit_notes').val(sale.notes);
            
            $('#editSaleModal').modal('show');
        }
    });
}

function cancelSale(id) {
    if (confirm('Tem certeza que deseja cancelar esta venda?')) {
        $.post(BASE_URL + '/api/sales/cancel.php', { id }, function(response) {
            if (response.success) {
                showAlert('Venda cancelada com sucesso!', 'success');
                loadSales();
            } else {
                showAlert(response.message || 'Erro ao cancelar venda', 'danger');
            }
        });
    }
}

function printSale() {
    window.print();
}

// Funções de Filtro
function getFilters() {
    return {
        customer_id: $('#filter_customer').val(),
        status: $('#filter_status').val(),
        payment_method: $('#filter_payment_method').val(),
        date_start: $('#date_start').val(),
        date_end: $('#date_end').val(),
        min_value: $('#min_value').val(),
        max_value: $('#max_value').val()
    };
}

function clearFilters() {
    $('#filterForm')[0].reset();
    $('#filter_customer').val('').trigger('change');
    loadSales();
    $('#filterModal').modal('hide');
}

function applyFilters() {
    loadSales();
    $('#filterModal').modal('hide');
}

// Funções Utilitárias
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getStatusBadge(status) {
    const badges = {
        'pendente': 'warning',
        'pago': 'success',
        'cancelado': 'danger'
    };
    return badges[status] || 'secondary';
}

function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    $('#alertContainer').html(alertHtml);
}

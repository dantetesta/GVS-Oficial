<!-- View Sale Modal -->
<div class="modal fade" id="viewSaleModal" tabindex="-1" aria-labelledby="viewSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSaleModalLabel">Detalhes da Venda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Informações da Venda</h6>
                        <p><strong>ID:</strong> <span id="view_sale_id"></span></p>
                        <p><strong>Data:</strong> <span id="view_sale_date"></span></p>
                        <p><strong>Status:</strong> <span id="view_sale_status"></span></p>
                        <p><strong>Método de Pagamento:</strong> <span id="view_payment_method"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Informações do Cliente</h6>
                        <p><strong>Nome:</strong> <span id="view_customer_name"></span></p>
                        <p><strong>Email:</strong> <span id="view_customer_email"></span></p>
                        <p><strong>Telefone:</strong> <span id="view_customer_phone"></span></p>
                    </div>
                </div>

                <h6 class="text-muted mb-3">Produtos</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Preço Unit.</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="view_products_list">
                            <!-- Preenchido via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Subtotal:</strong></td>
                                <td class="text-end" id="view_subtotal"></td>
                            </tr>
                            <tr>
                                <td><strong>Desconto:</strong></td>
                                <td class="text-end" id="view_discount"></td>
                            </tr>
                            <tr>
                                <td><strong>Total:</strong></td>
                                <td class="text-end" id="view_total"></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-3">
                    <h6 class="text-muted">Observações</h6>
                    <p id="view_notes"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="printSale">
                    <i class="bi bi-printer"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

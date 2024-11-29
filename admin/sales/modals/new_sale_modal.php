<!-- New Sale Modal -->
<div class="modal fade" id="newSaleModal" tabindex="-1" aria-labelledby="newSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newSaleModalLabel">Nova Venda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newSaleForm">
                    <!-- Cliente -->
                    <div class="mb-3">
                        <label for="customer" class="form-label">Cliente</label>
                        <select class="form-select select2" id="customer" name="customer_id" required>
                            <option value="">Selecione um cliente</option>
                        </select>
                    </div>

                    <!-- Produtos -->
                    <div class="mb-3">
                        <label class="form-label">Produtos</label>
                        <div id="productsList">
                            <div class="row product-item mb-2">
                                <div class="col-md-5">
                                    <select class="form-select select2-products" name="products[]" required>
                                        <option value="">Selecione um produto</option>
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
                        </div>
                        <button type="button" class="btn btn-secondary mt-2" id="addProduct">
                            <i class="bi bi-plus-circle"></i> Adicionar Produto
                        </button>
                    </div>

                    <!-- Totais -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="subtotal" class="form-label">Subtotal</label>
                                <input type="text" class="form-control" id="subtotal" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount" class="form-label">Desconto</label>
                                <input type="number" class="form-control" id="discount" name="discount" min="0" step="0.01" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="total" class="form-label">Total</label>
                        <input type="text" class="form-control" id="total" name="total" readonly>
                    </div>

                    <!-- Método de Pagamento -->
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Método de Pagamento</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Selecione o método</option>
                            <option value="dinheiro">Dinheiro</option>
                            <option value="cartao">Cartão</option>
                            <option value="pix">PIX</option>
                            <option value="boleto">Boleto</option>
                        </select>
                    </div>

                    <!-- Observações -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Observações</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveSale">Salvar Venda</button>
            </div>
        </div>
    </div>
</div>

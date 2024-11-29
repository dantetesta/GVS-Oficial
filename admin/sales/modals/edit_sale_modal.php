<!-- Edit Sale Modal -->
<div class="modal fade" id="editSaleModal" tabindex="-1" aria-labelledby="editSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSaleModalLabel">Editar Venda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSaleForm">
                    <input type="hidden" id="edit_sale_id" name="sale_id">
                    
                    <!-- Status -->
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="pendente">Pendente</option>
                            <option value="pago">Pago</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>

                    <!-- Método de Pagamento -->
                    <div class="mb-3">
                        <label for="edit_payment_method" class="form-label">Método de Pagamento</label>
                        <select class="form-select" id="edit_payment_method" name="payment_method" required>
                            <option value="dinheiro">Dinheiro</option>
                            <option value="cartao">Cartão</option>
                            <option value="pix">PIX</option>
                            <option value="boleto">Boleto</option>
                        </select>
                    </div>

                    <!-- Desconto -->
                    <div class="mb-3">
                        <label for="edit_discount" class="form-label">Desconto</label>
                        <input type="number" class="form-control" id="edit_discount" name="discount" min="0" step="0.01">
                    </div>

                    <!-- Observações -->
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Observações</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="updateSale">Atualizar Venda</button>
            </div>
        </div>
    </div>
</div>

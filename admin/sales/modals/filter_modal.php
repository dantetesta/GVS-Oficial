<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filtrar Vendas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm">
                    <!-- Data Range -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date_start" class="form-label">Data Inicial</label>
                            <input type="date" class="form-control" id="date_start" name="date_start">
                        </div>
                        <div class="col-md-6">
                            <label for="date_end" class="form-label">Data Final</label>
                            <input type="date" class="form-control" id="date_end" name="date_end">
                        </div>
                    </div>

                    <!-- Cliente -->
                    <div class="mb-3">
                        <label for="filter_customer" class="form-label">Cliente</label>
                        <select class="form-select select2" id="filter_customer" name="customer_id">
                            <option value="">Todos os clientes</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="filter_status" class="form-label">Status</label>
                        <select class="form-select" id="filter_status" name="status">
                            <option value="">Todos os status</option>
                            <option value="pendente">Pendente</option>
                            <option value="pago">Pago</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>

                    <!-- Método de Pagamento -->
                    <div class="mb-3">
                        <label for="filter_payment_method" class="form-label">Método de Pagamento</label>
                        <select class="form-select" id="filter_payment_method" name="payment_method">
                            <option value="">Todos os métodos</option>
                            <option value="dinheiro">Dinheiro</option>
                            <option value="cartao">Cartão</option>
                            <option value="pix">PIX</option>
                            <option value="boleto">Boleto</option>
                        </select>
                    </div>

                    <!-- Valor Range -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="min_value" class="form-label">Valor Mínimo</label>
                            <input type="number" class="form-control" id="min_value" name="min_value" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label for="max_value" class="form-label">Valor Máximo</label>
                            <input type="number" class="form-control" id="max_value" name="max_value" min="0" step="0.01">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="clearFilter">Limpar Filtros</button>
                <button type="button" class="btn btn-primary" id="applyFilter">Aplicar Filtros</button>
            </div>
        </div>
    </div>
</div>

<!-- Alert Container -->
<div id="alertContainer"></div>

<!-- Cards -->
<div class="row g-4 mb-4">
    <!-- Vendas Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-4">
                        <div class="icon-circle bg-primary">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                    <div class="col-8 text-end">
                        <h5 class="text-uppercase text-muted mb-1 small">Vendas (Mensal)</h5>
                        <h2 class="mb-0 h4">R$ 40.000</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Produtos Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-4">
                        <div class="icon-circle bg-success">
                            <i class="bi bi-box"></i>
                        </div>
                    </div>
                    <div class="col-8 text-end">
                        <h5 class="text-uppercase text-muted mb-1 small">Produtos</h5>
                        <h2 class="mb-0 h4">215</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clientes Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-4">
                        <div class="icon-circle bg-info">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                    <div class="col-8 text-end">
                        <h5 class="text-uppercase text-muted mb-1 small">Clientes</h5>
                        <h2 class="mb-0 h4">98</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pedidos Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-4">
                        <div class="icon-circle bg-warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                    <div class="col-8 text-end">
                        <h5 class="text-uppercase text-muted mb-1 small">Pedidos Pendentes</h5>
                        <h2 class="mb-0 h4">18</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row g-4">
    <!-- Últimas Vendas -->
    <div class="col-12 col-lg-6">
        <div class="card table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Últimas Vendas</h5>
                <a href="<?php echo BASE_URL; ?>/admin/sales.php" class="btn btn-primary btn-sm">Ver Todas</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#1234</td>
                                <td>João Silva</td>
                                <td>R$ 1.500,00</td>
                                <td><span class="badge bg-success">Concluído</span></td>
                            </tr>
                            <tr>
                                <td>#1233</td>
                                <td>Maria Santos</td>
                                <td>R$ 2.300,00</td>
                                <td><span class="badge bg-warning">Pendente</span></td>
                            </tr>
                            <tr>
                                <td>#1232</td>
                                <td>Pedro Souza</td>
                                <td>R$ 800,00</td>
                                <td><span class="badge bg-success">Concluído</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Produtos Mais Vendidos -->
    <div class="col-12 col-lg-6">
        <div class="card table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Produtos Mais Vendidos</h5>
                <a href="<?php echo BASE_URL; ?>/admin/products.php" class="btn btn-primary btn-sm">Ver Todos</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Vendas</th>
                                <th>Estoque</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Produto A</td>
                                <td>150</td>
                                <td>48</td>
                                <td>R$ 99,90</td>
                            </tr>
                            <tr>
                                <td>Produto B</td>
                                <td>120</td>
                                <td>32</td>
                                <td>R$ 149,90</td>
                            </tr>
                            <tr>
                                <td>Produto C</td>
                                <td>90</td>
                                <td>25</td>
                                <td>R$ 79,90</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

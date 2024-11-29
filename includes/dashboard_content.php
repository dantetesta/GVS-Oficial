<!-- Alert Container -->
<div id="alertContainer" class="mb-4"></div>

<!-- Cards -->
<div class="row g-3 mb-4">
    <!-- Vendas Card -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="icon-circle bg-primary">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h6 class="text-uppercase text-muted mb-2 small">Vendas (Mensal)</h6>
                        <h2 class="mb-0 h4">R$ 40.000</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Produtos Card -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="icon-circle bg-success">
                            <i class="bi bi-box"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h6 class="text-uppercase text-muted mb-2 small">Produtos</h6>
                        <h2 class="mb-0 h4">215</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clientes Card -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="icon-circle bg-info">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h6 class="text-uppercase text-muted mb-2 small">Clientes</h6>
                        <h2 class="mb-0 h4">98</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pedidos Card -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="icon-circle bg-warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h6 class="text-uppercase text-muted mb-2 small">Pedidos Pendentes</h6>
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
        <div class="card table-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Últimas Vendas</h5>
                <a href="<?php echo BASE_URL; ?>/admin/sales.php" class="btn btn-primary btn-sm">Ver Todas</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
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
                                <td class="text-nowrap">João Silva</td>
                                <td>R$ 1.500</td>
                                <td><span class="badge bg-success">Concluída</span></td>
                            </tr>
                            <tr>
                                <td>#1233</td>
                                <td class="text-nowrap">Maria Santos</td>
                                <td>R$ 890</td>
                                <td><span class="badge bg-warning">Pendente</span></td>
                            </tr>
                            <tr>
                                <td>#1232</td>
                                <td class="text-nowrap">Pedro Oliveira</td>
                                <td>R$ 2.100</td>
                                <td><span class="badge bg-success">Concluída</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Produtos Populares -->
    <div class="col-12 col-lg-6">
        <div class="card table-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Produtos Populares</h5>
                <a href="<?php echo BASE_URL; ?>/admin/products.php" class="btn btn-primary btn-sm">Ver Todos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Vendas</th>
                                <th>Estoque</th>
                                <th>Preço</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-nowrap">Produto A</td>
                                <td>45</td>
                                <td>12</td>
                                <td>R$ 99,90</td>
                            </tr>
                            <tr>
                                <td class="text-nowrap">Produto B</td>
                                <td>38</td>
                                <td>8</td>
                                <td>R$ 149,90</td>
                            </tr>
                            <tr>
                                <td class="text-nowrap">Produto C</td>
                                <td>32</td>
                                <td>15</td>
                                <td>R$ 79,90</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$controller = $data['controller'];
?>

<style>
    .customer-container {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding: 20px 0;
    }

    .summary-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 25px;
        margin-bottom: 25px;
        border: none;
        transition: all 0.3s ease;
    }

    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .summary-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .summary-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .data-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 25px;
        margin-bottom: 25px;
        border: none;
        transition: all 0.3s ease;
    }

    .data-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }

    .table-modern {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .table-modern th {
        background: #0b6fc7;
        color: white;
        font-weight: 600;
        border: none;
        padding: 15px;
    }

    .table-modern td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }

    .table-modern tbody tr:hover {
        background-color: #f8f9fa;
    }

    .filter-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 25px;
        margin-bottom: 25px;
    }

    .btn-modern {
        background: #0b6fc7;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .btn-reset {
        background: #6c757d;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-reset:hover {
        background: #5a6268;
        transform: translateY(-2px);
        color: white;
    }

    .breadcrumb-modern {
        background: white;
        border-radius: 8px;
        padding: 15px 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 25px;
    }

    .member-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
        margin-bottom: 15px;
    }

    .member-card:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
    }

    .scrollable-section {
        max-height: 700px;
        overflow-y: auto;
        padding-right: 10px;
    }

    .scrollable-section::-webkit-scrollbar {
        width: 8px;
    }

    .scrollable-section::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .scrollable-section::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .scrollable-section::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    .load-more-btn {
        width: 100%;
        margin-top: 15px;
        padding: 10px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        color: #495057;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .load-more-btn:hover {
        background: #e9ecef;
        border-color: #adb5bd;
    }

    .data-info {
        font-size: 0.85rem;
        color: #6c757d;
        text-align: center;
        margin-top: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    /* Salesman Box Styles */
    .salesman-box-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .salesman-box {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 3px solid transparent;
    }

    .salesman-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .salesman-box.active {
        border-color: #667eea;
        background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
    }

    .salesman-name {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
        font-size: 1rem;
    }

    .salesman-count {
        font-size: 0.85rem;
        color: #6c757d;
    }

    /* Pagination Styles */
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 30px;
    }

    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 5px;
    }

    .page-item {
        margin: 0;
    }

    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 15px;
        border: 1px solid #dee2e6;
        background: white;
        color: #667eea;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .page-link:hover {
        background: #f8f9fa;
        border-color: #667eea;
    }

    .page-item.active .page-link {
        background: #0b6fc7;
        border-color: #667eea;
        color: white;
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        background: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
    }
</style>

<div class="container-fluid customer-container">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h1 class="text-gradient mb-0">Data Customer</h1>
                <p class="text-muted mb-0">Monitoring member dan data salesman</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-inline-block bg-light rounded-pill px-3 py-2">
                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                    <span><?php echo $controller->formatDate(date('Y-m-d'), 'd F Y'); ?></span>
                </div>
            </div>
        </div>

        <!-- Breadcrumb -->
        <div class="breadcrumb-modern">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php?page=customer" class="text-decoration-none">Customer</a></li>
                    <?php if ($data['selectedSalesman']): ?>
                        <li class="breadcrumb-item active">Salesman: <?php echo $data['selectedSalesman']; ?></li>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>

        <!-- Summary Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="summary-card text-center">
                    <div class="summary-value"><?php echo $controller->formatNumber($data['summaryData']['total_member'] ?? 0); ?></div>
                    <div class="summary-label">Total Member</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card text-center">
                    <div class="summary-value"><?php echo $controller->formatNumber($data['summaryData']['total_salesman'] ?? 0); ?></div>
                    <div class="summary-label">Total MR</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card text-center">
                    <div class="summary-value"><?php echo $controller->formatNumber($data['summaryData']['member_baru_bulan_ini'] ?? 0); ?></div>
                    <div class="summary-label">Member Baru Bulan Ini</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card text-center">
                    <div class="summary-value"><?php echo $controller->formatNumber($data['summaryData']['member_aktif_transaksi'] ?? 0); ?></div>
                    <div class="summary-label">Member Aktif Transaksi</div>
                </div>
            </div>
        </div>

        <!-- Salesman Box Filter -->
        <div class="filter-section">
            <h5 class="mb-3">
                <i class="fas fa-users me-2"></i>
                Pilih Salesman
            </h5>

            <div class="salesman-box-container">
                <!-- All Salesman Box -->
                <div class="salesman-box <?php echo empty($data['selectedSalesman']) ? 'active' : ''; ?>"
                    onclick="window.location.href='index.php?page=customer'">
                    <div class="salesman-name">Semua Salesman</div>
                    <div class="salesman-count"><?php echo $controller->formatNumber($data['summaryData']['total_member'] ?? 0); ?> Member</div>
                </div>

                <!-- Individual Salesman Boxes -->
                <?php foreach ($data['salesmanList'] as $salesman): ?>
                    <div class="salesman-box <?php echo ($data['selectedSalesman'] == $salesman['salesman']) ? 'active' : ''; ?>"
                        onclick="window.location.href='index.php?page=customer&salesman=<?php echo urlencode($salesman['salesman']); ?>'">
                        <div class="salesman-name"><?php echo $salesman['salesman']; ?></div>
                        <div class="salesman-count"><?php echo $controller->formatNumber($salesman['total_member']); ?> Member</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Data Member -->
        <div class="data-card">
            <h4 class="mb-4">
                <i class="fas fa-user-friends me-2"></i>
                <?php echo $data['selectedSalesman'] ? 'Member: ' . $data['selectedSalesman'] : 'Semua Member'; ?>
            </h4>

            <div class="scrollable-section" id="memberScrollSection">
                <?php if (!empty($data['memberData'])): ?>
                    <?php foreach ($data['memberData'] as $member): ?>
                        <div class="member-card card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <h6 class="card-title mb-1"><?php echo $member['nama_member']; ?></h6>
                                        <small class="text-muted d-block"><?php echo $member['kode_member']; ?></small>
                                        <small class="text-muted d-block">MR: <?php echo $member['salesman'] ?? '-'; ?></small>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="row text-center small">
                                            <div class="col-4">
                                                <div class="text-muted">Total Pesanan</div>
                                                <div class="fw-bold text-primary"><?php echo $controller->formatNumber($member['total_pesanan']); ?></div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-muted">Total Pembelian</div>
                                                <div class="fw-bold text-success"><?php echo $controller->formatCurrency($member['total_pembelian']); ?></div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-muted">Last Order</div>
                                                <div class="fw-bold text-info"><?php echo $controller->formatDate($member['last_order'], 'd M Y'); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Tidak ada data member</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($data['totalMemberPages'] > 1): ?>
                <div class="pagination-container">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <!-- Previous Page -->
                            <li class="page-item <?php echo $data['memberPage'] <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo $data['memberPage'] > 1 ? $controller->getPaginationUrl($data['memberPage'] - 1) : '#'; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <!-- Page Numbers -->
                            <?php for ($i = 1; $i <= $data['totalMemberPages']; $i++): ?>
                                <?php if ($i == 1 || $i == $data['totalMemberPages'] || ($i >= $data['memberPage'] - 2 && $i <= $data['memberPage'] + 2)): ?>
                                    <li class="page-item <?php echo $i == $data['memberPage'] ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo $controller->getPaginationUrl($i); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php elseif ($i == $data['memberPage'] - 3 || $i == $data['memberPage'] + 3): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <!-- Next Page -->
                            <li class="page-item <?php echo $data['memberPage'] >= $data['totalMemberPages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo $data['memberPage'] < $data['totalMemberPages'] ? $controller->getPaginationUrl($data['memberPage'] + 1) : '#'; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>

            <!-- Info Jumlah Data -->
            <?php if (!empty($data['memberData'])): ?>
                <div class="data-info">
                    Menampilkan <?php echo min(count($data['memberData']) + (($data['memberPage'] - 1) * $data['perPage']), $data['totalMembers']); ?> dari <?php echo $controller->formatNumber($data['totalMembers']); ?> member
                    (Halaman <?php echo $data['memberPage']; ?> dari <?php echo $data['totalMemberPages']; ?>)
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
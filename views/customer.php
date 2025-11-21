<?php $controller = $data['controller']; ?>

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
                <div class="summary-card">
                    <div class="summary-value"><?php echo $controller->formatNumber($data['summaryData']['total_member'] ?? 0); ?></div>
                    <div class="summary-label">Total Member</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-value"><?php echo $controller->formatNumber($data['summaryData']['total_salesman'] ?? 0); ?></div>
                    <div class="summary-label">Total MR</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-value"><?php echo $controller->formatNumber($data['summaryData']['member_baru_bulan_ini'] ?? 0); ?></div>
                    <div class="summary-label">Member Baru Bulan Ini</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
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
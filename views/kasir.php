<?php
$controller = $data['controller'];
$amt = $data['amt'] ?? [];
$pbData = $data['pbData'] ?? [];

// Debug
error_log('VIEW: AMT count = ' . count($amt));
error_log('VIEW: PB count = ' . count($pbData));
?>

<div class="content">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="text-gradient mb-4 mt-4">Dashboard Kasir</h1>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-inline-block bg-light rounded-pill px-3 py-2">
                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                    <span id="currentDate"><?php echo date('d F Y'); ?></span>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="">
                <input type="hidden" name="page" value="kasir">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label fw-semibold">Tanggal Mulai</label>
                        <input type="date" class="form-control form-control-modern" id="start_date" name="start_date"
                            value="<?php echo $data['startDate']; ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label fw-semibold">Tanggal Akhir</label>
                        <input type="date" class="form-control form-control-modern" id="end_date" name="end_date"
                            value="<?php echo $data['endDate']; ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-modern w-100">
                            <i class="fas fa-sync-alt me-2"></i>Muat Data
                        </button>
                    </div>
                </div>
            </form>
            <div class="mt-3">
                <small class="text-muted fw-semibold"><i class="fas fa-filter me-1"></i> Periode:
                    <?php echo $controller->formatDate($data['startDate'], 'd M Y'); ?> -
                    <?php echo $controller->formatDate($data['endDate'], 'd M Y'); ?>
                </small>
            </div>
        </div>

        <?php if ($data['hasData']): ?>
            <!-- Total Summary -->
            <div class="total-summary">
                <div class="row text-center">
                    <div class="col summary-item">
                        <div class="summary-value"><?php echo $controller->formatNumber($data['totalPesanan']); ?></div>
                        <div class="summary-label">Total Pesanan</div>
                    </div>
                    <div class="col summary-item">
                        <div class="summary-value">
                            <?php echo $controller->formatDate($data['startDate'], 'd M'); ?>
                            <small> s/d </small>
                            <?php echo $controller->formatDate($data['endDate'], 'd M Y'); ?>
                        </div>
                        <div class="summary-label">Periode Analisis</div>
                    </div>
                </div>
            </div>

            <!-- Data Cards -->
            <div class="row">
                <?php foreach ($data['orderData'] as $item):
                    $status = $item['status_pesanan'];
                    $jumlah = $item['jumlah'];
                    $config = $data['statusConfig'][$status] ?? $data['statusConfig']['LAINNYA'];
                    $persentase = $controller->calculatePercentage($jumlah, $data['totalPesanan']);
                ?>
                    <div class="col-xl-3 col-lg-4 col-md-6 mt-3">
                        <div class="data-card <?php echo $config['class']; ?>">
                            <div class="card-body text-center p-4">
                                <div class="card-icon" style="color: <?php echo $config['color']; ?>">
                                    <i class="fas <?php echo $config['icon']; ?>"></i>
                                </div>
                                <div class="card-title"><?php echo $status; ?></div>
                                <div class="card-value"><?php echo $controller->formatNumber($jumlah); ?></div>
                                <div class="card-percentage">
                                    <?php echo $persentase; ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- AMT Section -->
            <div class="amt-section mt-5">
                <h3 class="section-title">
                    <i class="fas fa-user-friends me-2"></i>Data AMBT - Ambil di Stock Point Indogrosir
                </h3>

                <?php if (!empty($amt)): ?>
                    <div class="table-scroll-container" id="ambtTableContainer">
                        <table class="table table-modern table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Nomor Transaksi</th>
                                    <th>No PB</th>
                                    <th>Kode Member</th>
                                    <th>Nama Member</th>
                                    <th class="text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($amt as $idx => $amtRow): ?>
                                    <tr>
                                        <td><?php echo $idx + 1; ?></td>
                                        <td><?php echo htmlspecialchars($amtRow['nomor'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($amtRow['notrx'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($amtRow['kdmember'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($amtRow['namamember'] ?? '-'); ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-detail btn-sm"
                                                onclick="showDetail('<?php echo htmlspecialchars($amtRow['nomor'] ?? '', ENT_QUOTES); ?>', '<?php echo date('Y-m-d', strtotime($amtRow['tgltrans'] ?? 'now')); ?>')">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-info">
                        <i class="fas fa-database me-2"></i>
                        Menampilkan <?php echo count($amt); ?> data AMBT
                    </div>
                <?php else: ?>
                    <div class="alert alert-info alert-modern mt-2">
                        <i class="fas fa-info-circle me-2"></i>
                        Tidak ada data AMBT untuk periode terpilih.
                    </div>
                <?php endif; ?>
            </div>

            <!-- PB Section -->
            <div class="pb-section mt-5">
                <h3 class="section-title">
                    <i class="fas fa-file-invoice me-2"></i>Data PB
                </h3>

                <?php if (!empty($pbData)): ?>
                    <div class="table-scroll-container" id="pbTableContainer">
                        <table class="table table-modern table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>No Transaksi</th>
                                    <th>No PB</th>
                                    <th>Tanggal</th>
                                    <th>Kode Member</th>
                                    <th>Ongkir</th>
                                    <th class="text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pbData as $idx => $pb): ?>
                                    <tr>
                                        <td><?php echo $idx + 1; ?></td>
                                        <td><?php echo htmlspecialchars($pb['notrans'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($pb['nopb'] ?? '-'); ?></td>
                                        <td><?php echo $controller->formatDate($pb['tgl'] ?? 'now', 'd M Y H:i'); ?></td>
                                        <td><?php echo htmlspecialchars($pb['kode_member'] ?? '-'); ?></td>
                                        <td>Rp <?php echo $controller->formatNumber($pb['ongkir'] ?? 0); ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-detail btn-sm"
                                                onclick="showDetail('<?php echo htmlspecialchars($pb['notrans'] ?? '', ENT_QUOTES); ?>', '<?php echo date('Y-m-d', strtotime($pb['tgltrans'] ?? 'now')); ?>')">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-info">
                        <i class="fas fa-file-invoice me-2"></i>
                        Menampilkan <?php echo count($pbData); ?> data PB
                    </div>
                <?php else: ?>
                    <div class="alert alert-info alert-modern mt-2">
                        <i class="fas fa-info-circle me-2"></i>
                        Tidak ada data PB untuk periode terpilih.
                    </div>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <!-- No Data Message -->
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info alert-modern text-center">
                        <i class="fas fa-inbox fa-4x mb-3" style="color: #3498db;"></i>
                        <h4 class="alert-heading">Tidak Ada Data Pesanan</h4>
                        <p class="mb-0">Tidak ditemukan data pesanan untuk periode yang dipilih</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">
                    Detail Transaksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detailLoading" class="text-center py-5">
                    <div class="loading-spinner mx-auto mb-3"></div>
                    <p class="text-muted">Memuat data...</p>
                </div>
                <div id="detailContent" style="display: none;">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-card">
                                <h6><i class="fas fa-file-alt me-2"></i>Informasi Transaksi</h6>
                                <p class="mb-2"><strong>No Transaksi:</strong> <span id="detailNoTrans">-</span></p>
                                <p class="mb-2"><strong>No PB:</strong> <span id="detailNoPB">-</span></p>
                                <p class="mb-0"><strong>Tanggal:</strong> <span id="detailTgl">-</span></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <h6><i class="fas fa-user me-2"></i>Informasi Member</h6>
                                <p class="mb-2"><strong>Kode Member:</strong> <span id="detailKodeMember">-</span></p>
                                <p class="mb-0"><strong>Nama Member:</strong> <span id="detailNamaMember">-</span></p>
                            </div>
                        </div>
                    </div>
                    <h6 class="mb-3"><i class="fas fa-shopping-cart me-2"></i>Daftar Item</h6>
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>PLU</th>
                                    <th>Nama Produk</th>
                                    <th class="text-center">Qty Order</th>
                                </tr>
                            </thead>
                            <tbody id="detailTableBody">
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold" style="background: rgba(0,0,0,0.02);">
                                    <td colspan="3" class="text-end">TOTAL QTY:</td>
                                    <td class="text-center">
                                        <span class="badge-qty" id="detailTotalQty">0</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div id="detailError" style="display: none;" class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span id="detailErrorMessage"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/kasir.js"></script>
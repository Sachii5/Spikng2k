<?php
// Helper functions untuk view
function getStatusConfig($status, $statusConfig)
{
    return $statusConfig[$status] ?? $statusConfig['LAINNYA'];
}

// Pastikan controller tersedia
$controller = $data['controller'];

// Tambahan: jalankan query AMT langsung di view untuk debug (sementara)
require_once __DIR__ . '/../config/database.php';
$amt = [];

try {
    $db = new Database();
    $db->connect();

    $start = $data['startDate'] ?? date('Y-m-d');
    $end = $data['endDate'] ?? date('Y-m-d');
    $safeStart = addslashes($start);
    $safeEnd = addslashes($end);

    $sql = "
        SELECT
            h.obi_notrans AS nomor,
            h.obi_nopb AS notrx,
            h.obi_kdmember AS kdmember,
            c.cus_namamember AS namamember,
            h.obi_kdekspedisi,
            c.cus_kodeigr,
            h.obi_tglpb,
            h.obi_tgltrans
        FROM tbtr_obi_h h
        LEFT JOIN tbmaster_customer c ON h.obi_kdmember = c.cus_kodemember
        WHERE h.obi_tglpb IS NOT NULL
          AND h.obi_tglpb::date BETWEEN '{$safeStart}' AND '{$safeEnd}'
          AND (
            COALESCE(h.obi_kdekspedisi, '') LIKE '%Ambil%'
            OR TRIM(UPPER(COALESCE(c.cus_kodeigr, ''))) = '2K'
          )
        ORDER BY h.obi_tglpb DESC
    ";

    error_log('VIEW AMT direct query: ' . preg_replace('/\s+/', ' ', trim($sql)));

    $res = $db->query($sql);
    if ($res) {
        $amt = $db->fetchAll($res);
        if (method_exists($db, 'freeResult')) {
            $db->freeResult($res);
        }
        error_log('VIEW AMT fetched: ' . count($amt) . ' rows');
    } else {
        error_log('VIEW AMT query returned false/empty resource');
    }
} catch (Throwable $e) {
    error_log('VIEW AMT exception: ' . $e->getMessage());
}

// Gunakan data dari controller, bukan dari query langsung di view
$amt = $data['amt'] ?? [];
$pbData = $data['pbData'] ?? [];

// Debug
error_log('VIEW: AMT count = ' . count($amt));
error_log('VIEW: PB count = ' . count($pbData));
?>

<style>
    :root {
        --primary: #2c3e50;
        --secondary: #34495e;
        --accent: #3498db;
        --success: #27ae60;
        --warning: #f39c12;
        --danger: #e74c3c;
        --light: #ecf0f1;
        --dark: #2c3e50;
    }

    body {
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
    }

    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .filter-section {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .data-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .data-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--accent);
    }

    .data-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .data-card.success::before {
        background: var(--success);
    }

    .data-card.primary::before {
        background: var(--accent);
    }

    .data-card.warning::before {
        background: var(--warning);
    }

    .data-card.danger::before {
        background: var(--danger);
    }

    .data-card.secondary::before {
        background: var(--secondary);
    }

    .data-card.purple::before {
        background: #9b59b6;
    }

    .data-card.orange::before {
        background: var(--warning);
    }

    .data-card.dark::before {
        background: var(--dark);
    }

    .card-icon {
        font-size: 2.8rem;
        margin-bottom: 20px;
        opacity: 0.9;
    }

    .card-title {
        font-size: 0.9rem;
        color: #7f8c8d;
        margin-bottom: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-value {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--dark);
    }

    .card-percentage {
        font-size: 0.85rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        background: rgba(52, 152, 219, 0.1);
        color: var(--accent);
        display: inline-block;
    }

    .total-summary {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .summary-item {
        text-align: center;
        padding: 15px;
    }

    .summary-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 5px;
    }

    .summary-label {
        font-size: 0.9rem;
        color: #7f8c8d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-modern {
        background: linear-gradient(135deg, var(--accent), #2980b9);
        border: none;
        border-radius: 12px;
        padding: 12px 30px;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
        color: white;
    }

    .btn-detail {
        background: #0b6fc7;
        border: none;
        border-radius: 8px;
        padding: 6px 16px;
        font-weight: 600;
        font-size: 0.85rem;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(155, 89, 182, 0.3);
    }

    .btn-detail:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(155, 89, 182, 0.4);
        color: white;
    }

    .form-control-modern {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 15px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control-modern:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.1);
    }

    .alert-modern {
        border-radius: 15px;
        border: none;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    h1,
    h2,
    h3,
    h4 {
        font-weight: 700;
        color: var(--dark);
    }

    .text-gradient {
        background: linear-gradient(135deg, var(--accent), #9b59b6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .table-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .table-modern thead th {
        background: linear-gradient(135deg, var(--accent), #2980b9);
        color: white;
        border: none;
        padding: 15px 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table-modern tbody td {
        padding: 15px 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        vertical-align: middle;
    }

    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    .table-modern tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.05);
    }

    .badge-qty {
        background: rgba(52, 152, 219, 0.1);
        color: var(--accent);
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .badge-real {
        background: rgba(39, 174, 96, 0.1);
        color: var(--success);
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        margin: 40px 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 3px solid var(--accent);
        display: inline-block;
    }

    /* Pagination Scroll Styles */
    .table-scroll-container {
        max-height: 600px;
        overflow-y: auto;
        position: relative;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .table-scroll-container::-webkit-scrollbar {
        width: 12px;
    }

    .table-scroll-container::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
    }

    .table-scroll-container::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--accent), #2980b9);
        border-radius: 10px;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .table-scroll-container::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #2980b9, var(--accent));
    }

    .pagination-info {
        background: rgba(52, 152, 219, 0.1);
        padding: 12px 20px;
        border-radius: 12px;
        margin-top: 15px;
        text-align: center;
        font-weight: 600;
        color: var(--accent);
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        background: linear-gradient(135deg, var(--accent), #2980b9);
        color: white;
        border-radius: 20px 20px 0 0;
        border: none;
        padding: 20px 25px;
    }

    .modal-title {
        font-weight: 700;
        font-size: 1.3rem;
    }

    .modal-body {
        padding: 25px;
    }

    .btn-close {
        filter: brightness(0) invert(1);
    }

    .loading-spinner {
        display: inline-block;
        width: 40px;
        height: 40px;
        border: 4px solid rgba(52, 152, 219, 0.3);
        border-radius: 50%;
        border-top-color: var(--accent);
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .info-card {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(155, 89, 182, 0.1));
        border-radius: 12px;
        padding: 20px;
        border: 1px solid rgba(52, 152, 219, 0.2);
    }

    .info-card h6 {
        color: var(--accent);
        font-weight: 700;
        margin-bottom: 15px;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-card p {
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .info-card strong {
        color: var(--dark);
        font-weight: 600;
    }
</style>

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
                    $config = getStatusConfig($status, $data['statusConfig']);
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
                                    <th>Status Pesanan</th>
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
                                        <td><?php echo htmlspecialchars($amtRow['status_pesanan'] ?? '-'); ?></td>
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
                                    <th>Status Pesanan</th>
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
                                        <td><?php echo htmlspecialchars($pb['status_pesanan'] ?? '-'); ?></td>
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
                                <p class="mb-2"><strong>Status Transaksi:</strong> <span id="detailStatusTransaksi">-</span></p>
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

<script>
    // Update current date
    document.getElementById('currentDate').textContent = new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    // Function to show detail modal
    function showDetail(noTrans, tglTrans) {
        console.log('Show detail:', noTrans, tglTrans);

        // Reset modal
        document.getElementById('detailLoading').style.display = 'block';
        document.getElementById('detailContent').style.display = 'none';
        document.getElementById('detailError').style.display = 'none';

        // Show modal
        const modalEl = document.getElementById('detailModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        // Fetch detail data - GUNAKAN ENDPOINT KHUSUS
        const url = `kasir_endpoint.php?action=getDetail&notrans=${encodeURIComponent(noTrans)}&tgl=${encodeURIComponent(tglTrans)}`;
        console.log('Fetching:', url);

        fetch(url)
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                // Cek content type sebelum parse
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    // Baca sebagai text untuk debug
                    return response.text().then(text => {
                        console.error('Expected JSON but got:', text.substring(0, 200));
                        throw new Error(`Server returned HTML instead of JSON. Content: ${text.substring(0, 100)}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                document.getElementById('detailLoading').style.display = 'none';

                if (data.success && data.data && data.data.length > 0) {
                    const firstItem = data.data[0];

                    // Fill header info
                    document.getElementById('detailStatusTransaksi').textContent = firstItem.status_pesanan || '-';
                    document.getElementById('detailNoTrans').textContent = firstItem.nomor || '-';
                    document.getElementById('detailNoPB').textContent = firstItem.nopb || '-';
                    document.getElementById('detailTgl').textContent = firstItem.tgl ? formatDate(firstItem.tgl) : '-';
                    document.getElementById('detailKodeMember').textContent = firstItem.kode_member || '-';
                    document.getElementById('detailNamaMember').textContent = firstItem.nama_member || '-';

                    // Fill table
                    const tbody = document.getElementById('detailTableBody');
                    tbody.innerHTML = '';
                    let totalQty = 0;

                    data.data.forEach((item, index) => {
                        const qty = parseInt(item.qty_order) || 0;
                        totalQty += qty;

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="fw-semibold">${index + 1}</td>
                            <td class="text-muted">${item.plu || '-'}</td>
                            <td class="fw-semibold">${item.nama || '-'}</td>
                            <td class="text-center">
                                <span class="badge-qty">${formatNumber(qty)}</span>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });

                    document.getElementById('detailTotalQty').textContent = formatNumber(totalQty);
                    document.getElementById('detailContent').style.display = 'block';
                } else {
                    document.getElementById('detailError').style.display = 'block';
                    document.getElementById('detailErrorMessage').textContent = data.message || 'Data tidak ditemukan untuk transaksi ini.';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                document.getElementById('detailLoading').style.display = 'none';
                document.getElementById('detailError').style.display = 'block';
                document.getElementById('detailErrorMessage').textContent = 'Terjadi kesalahan saat memuat data: ' + error.message;
            });
    }

    // Helper function to format number
    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    // Helper function to format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return date.toLocaleDateString('id-ID', options);
    }
</script>
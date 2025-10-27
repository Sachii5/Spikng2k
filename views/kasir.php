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
            h.obi_tglpb
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

    $res = $db->query($sql); // coba eksekusi raw SQL di view
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

// Tambahan: pastikan $amt selalu terdefinisi untuk menghindari notice
$amt = $data['amt'] ?? [];
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
        /* padding: 20px 0; */
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

            <!-- AMT Section -->
            <div class="amt-section mt-4">
                <h3 class="section-title">
                    <i class="fas fa-user-friends me-2"></i>Data AMBT - Ambil di Stock Point Indogrosir
                </h3>

                <?php
                // debug: catat apakah $amt ada dan preview (gunakan $amt, bukan $data['amt'])
                error_log('VIEW AMT isset: ' . (empty($amt) ? 'no' : 'yes') . ' | count: ' . count($amt));
                ?>
                <?php if (!empty($amt)): ?>
                    <div class="table-responsive">
                        <table class="table table-modern table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nomor Transaksi</th>
                                    <th>No PB</th>
                                    <th>Kode Member</th>
                                    <th>Nama Member</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($amt as $idx => $amtRow): ?>
                                    <tr>
                                        <td><?php echo $idx + 1; ?></td>
                                        <td><?php echo htmlspecialchars($amtRow['nomor']); ?></td>
                                        <td><?php echo htmlspecialchars($amtRow['notrx']); ?></td>
                                        <td><?php echo htmlspecialchars($amtRow['kdmember']); ?></td>
                                        <td><?php echo htmlspecialchars($amtRow['namamember']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mt-2">
                        Tidak ada data AMBT untuk periode terpilih.
                        </div>
                <?php endif; ?>
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

            <!-- Items Table Section -->
            <?php
            $hasData = !empty($data['hasData']);
            $hasItemsData = !empty($data['hasItemsData']);
            $orderItems = $data['orderItems'] ?? [];
            ?>

            <?php if ($hasData && $hasItemsData && !empty($orderItems)): ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <h3 class="section-title">
                            <i class="fas fa-list-alt me-2"></i>Detail Item Pesanan
                        </h3>

                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Produk</th>
                                        <th>PLU</th>
                                        <th class="text-center">Qty Order</th>
                                        <th class="text-center">Qty Realisasi</th>
                                        <th class="text-center">Selisih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $counter = 1;
                                    foreach ($orderItems as $item):
                                        $orderQty = $item['order'] ?? 0;
                                        $realQty = $item['real'] ?? 0;
                                        $selisih = $orderQty - $realQty;
                                        $selisihClass = $selisih == 0 ? 'text-success' : ($selisih > 0 ? 'text-warning' : 'text-danger');
                                    ?>
                                        <tr>
                                            <td class="fw-semibold"><?php echo $counter++; ?></td>
                                            <td class="fw-semibold"><?php echo htmlspecialchars($item['nama_produk'] ?? '-'); ?></td>
                                            <td class="text-muted"><?php echo htmlspecialchars($item['plu'] ?? '-'); ?></td>
                                            <td class="text-center">
                                                <span class="badge-qty"><?php echo $controller->formatNumber($orderQty); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge-real"><?php echo $controller->formatNumber($realQty); ?></span>
                                            </td>
                                            <td class="text-center fw-bold <?php echo $selisihClass; ?>">
                                                <?php echo $controller->formatNumber($selisih); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold" style="background: rgba(0,0,0,0.02);">
                                        <td colspan="3" class="text-end">TOTAL:</td>
                                        <td class="text-center">
                                            <span class="badge-qty"><?php echo $controller->formatNumber($data['totalItemOrder'] ?? 0); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-real"><?php echo $controller->formatNumber($data['totalItemReal'] ?? 0); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $totalOrder = $data['totalItemOrder'] ?? 0;
                                            $totalReal = $data['totalItemReal'] ?? 0;
                                            $totalSelisih = $totalOrder - $totalReal;
                                            $totalSelisihClass = $totalSelisih == 0 ? 'text-success' : ($totalSelisih > 0 ? 'text-warning' : 'text-danger');
                                            ?>
                                            <span class="fw-bold <?php echo $totalSelisihClass; ?>">
                                                <?php echo $controller->formatNumber($totalSelisih); ?>
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            <?php elseif ($hasData && (!$hasItemsData || empty($orderItems))): ?>
                <!-- No Items Data Message -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-warning alert-modern text-center">
                            <i class="fas fa-boxes fa-3x mb-3" style="color: #f39c12;"></i>
                            <h4 class="alert-heading">Tidak Ada Data Item</h4>
                            <p class="mb-0">Data status pesanan tersedia, tetapi tidak ditemukan data item untuk periode yang dipilih</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

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
</div>

<script>
    // Update current date
    document.getElementById('currentDate').textContent = new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
</script>
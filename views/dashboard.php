<?php
// Helper functions untuk view
function formatNumber($number)
{
    return number_format($number);
}

function formatPercentage($number)
{
    return number_format(abs($number), 1);
}

function getChangeClass($change)
{
    return $change >= 0 ? 'positive' : 'negative';
}

function getArrowIcon($change)
{
    return $change >= 0 ? 'up' : 'down';
}
?>

<style>
    /* Base Styles */
    .content {
        margin-top: 20px;
        background: #f8f9fa;
        min-height: 100vh;
        padding: 20px 0;
    }

    /* Enhanced Header */
    .dashboard-header {
        background: #0b6fc7;
        color: white;
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .dashboard-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header-info {
        display: flex;
        flex-direction: column;
        gap: 8px;
        text-align: right;
    }

    .current-date,
    .update-time {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.95rem;
        opacity: 0.9;
    }

    .current-date i,
    .update-time i {
        font-size: 1.1rem;
    }

    /* Filter Section */
    .filter-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .filter-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: end;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
    }

    .filter-input {
        width: 100%;
        padding: 10px 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .filter-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .filter-buttons {
        display: flex;
        gap: 10px;
    }

    .btn-filter {
        padding: 10px 25px;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: #0b6fc7;
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    /* Quick Filter Buttons */
    .quick-filters {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        flex-wrap: wrap;
    }

    .btn-quick {
        padding: 8px 15px;
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #495057;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-quick:hover {
        background: #e9ecef;
        border-color: #adb5bd;
    }

    .btn-quick.active {
        background: #0b6fc7;
        color: white;
        border-color: #667eea;
    }

    /* Enhanced Data Cards */
    .data-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 28px 24px;
        margin-bottom: 0;
        border: 2px solid transparent;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
        background: linear-gradient(90deg, #667eea, #764ba2);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .data-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .data-card:hover::before {
        opacity: 1;
    }

    /* Card Border Colors */
    .data-card.border-primary {
        border-color: #007bff !important;
    }

    .data-card.border-success {
        border-color: #28a745 !important;
    }

    .data-card.border-warning {
        border-color: #ffc107 !important;
    }

    .data-card.border-danger {
        border-color: #dc3545 !important;
    }

    /* Card Content Styling */
    .card-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #495057;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 15px;
        line-height: 1.3;
        min-height: 18px;
        text-align: center;
        width: 100%;
    }

    .card-value {
        font-size: 2.4rem;
        font-weight: 800;
        color: #2c3e50;
        margin-bottom: 12px;
        line-height: 1.2;
        min-height: 44px;
        width: 100%;
        text-align: center;
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-comparison {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 10px;
        font-weight: 500;
        min-height: 22px;
    }

    /* Change Indicators */
    .card-change {
        font-size: 0.85rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 15px;
        display: inline-block;
        margin-bottom: 8px;
        border: 1px solid transparent;
        min-height: 32px;
    }

    .card-change.positive {
        background: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }

    .card-change.negative {
        background: #f8d7da;
        color: #721c24;
        border-color: #f1b0b7;
    }

    .card-change.neutral {
        background: #e2e3e5;
        color: #383d41;
        border-color: #d6d8db;
    }

    /* Info Text */
    .info-text {
        font-size: 0.8rem;
        color: #adb5bd;
        font-style: italic;
        margin-top: 8px;
        min-height: 18px;
    }

    /* Chart Container */
    .chart-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 25px;
        margin-bottom: 20px;
        border: none;
    }

    /* Chart Legend */
    .chart-legend {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 15px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.8rem;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 2px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 20px;
            margin-bottom: 20px;
        }

        .header-content {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }

        .dashboard-title {
            font-size: 2rem;
        }

        .header-info {
            text-align: center;
        }

        .filter-form {
            flex-direction: column;
        }

        .filter-group {
            min-width: 100%;
        }

        .filter-buttons {
            width: 100%;
        }

        .btn-filter {
            flex: 1;
            justify-content: center;
        }

        .data-card {
            margin-bottom: 15px;
            padding: 20px 15px;
        }

        .card-value {
            font-size: 2rem;
            width: 100%;
            text-align: center;
        }

        .card-title {
            font-size: 0.9rem;
            text-align: center;
            width: 100%;
        }

        .chart-legend {
            flex-wrap: wrap;
            gap: 10px;
        }
    }

    @media (max-width: 576px) {
        .card-value {
            font-size: 1.8rem;
            width: 100%;
            text-align: center;
        }

        .card-title {
            font-size: 0.85rem;
            text-align: center;
            width: 100%;
        }

        .card-comparison {
            font-size: 0.85rem;
        }

        .stat-value {
            font-size: 1.3rem;
        }

        .summary-stats {
            padding: 15px;
        }
    }

    /* Utility Classes */
    .text-center {
        text-align: center;
    }

    .mt-2 {
        margin-top: 0.5rem;
    }

    /* Enhanced Metrics Grid */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 24px;
        margin-bottom: 2rem;
    }

    @media (max-width: 1200px) {
        .metrics-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }
    }

    @media (max-width: 768px) {
        .metrics-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }

    .metric-card {
        height: 100%;
    }

    .metric-card .metric-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        padding: 24px;
        height: 100%;
    }
</style>

<!-- Main content -->
<div class="content">
    <div class="container">
        <!-- Enhanced Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <h1 class="dashboard-title">Monitoring SPI 2K</h1>
                <div class="header-info">
                    <span class="current-date">
                        <i class="fas fa-calendar-alt"></i>
                        <?php echo $this->formatDate($data['dates']['today']); ?>
                    </span>
                    <span class="update-time">
                        <i class="fas fa-clock"></i>
                        Update Terakhir: <?php echo date('H:i'); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-title">
                <i class="fas fa-filter"></i>
                Filter Periode Data
            </div>
            <form method="GET" action="index.php" class="filter-form" id="filterForm">
                <input type="hidden" name="page" value="dashboard">

                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-calendar"></i> Tanggal Mulai
                    </label>
                    <input type="date" name="start_date" class="filter-input"
                        value="<?php echo $_GET['start_date'] ?? date('Y-m-01'); ?>"
                        id="startDate">
                </div>

                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-calendar"></i> Tanggal Akhir
                    </label>
                    <input type="date" name="end_date" class="filter-input"
                        value="<?php echo $_GET['end_date'] ?? date('Y-m-d'); ?>"
                        id="endDate">
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="btn-filter btn-primary">
                        <i class="fas fa-search"></i>
                        Tampilkan
                    </button>
                    <button type="button" class="btn-filter btn-secondary" onclick="resetFilter()">
                        <i class="fas fa-redo"></i>
                        Reset
                    </button>
                </div>
            </form>

            <!-- Quick Filter Buttons -->
            <div class="quick-filters">
                <button class="btn-quick" onclick="setQuickFilter('today')">Hari Ini</button>
                <button class="btn-quick" onclick="setQuickFilter('yesterday')">Kemarin</button>
                <button class="btn-quick" onclick="setQuickFilter('this_week')">Minggu Ini</button>
                <button class="btn-quick" onclick="setQuickFilter('last_week')">Minggu Lalu</button>
                <button class="btn-quick" onclick="setQuickFilter('this_month')">Bulan Ini</button>
                <button class="btn-quick" onclick="setQuickFilter('last_month')">Bulan Lalu</button>
            </div>
        </div>

        <!-- Dashboard utama -->
        <div class="metrics-grid">
            <!-- SALES HARI INI -->
            <div class="metric-card">
                <div class="card data-card border-primary">
                    <div class="metric-body text-center">
                        <div>
                            <div class="card-title mb-1">SALES PERIODE INI</div>
                            <div class="card-value mb-2"><?php echo $data['metrics']['salesToday']; ?></div>
                            <div class="card-comparison mb-1">
                                vs periode sebelumnya: <?php echo $data['metrics']['salesYesterday']; ?>
                            </div>
                        </div>
                        <div>
                            <div
                                class="card-change <?php echo $this->getChangeClass($data['metrics']['salesChange']); ?>">
                                <i
                                    class="fas fa-arrow-<?php echo $this->getArrowIcon($data['metrics']['salesChange']); ?>"></i>
                                <?php echo $this->formatPercentage($data['metrics']['salesChange']); ?>% perubahan
                            </div>
                            <div class="info-text mt-2">
                                Periode: <?php echo isset($_GET['start_date']) ? $this->formatDate($_GET['start_date']) . ' - ' . $this->formatDate($_GET['end_date']) : $this->formatDate($data['dates']['today']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PESANAN HARI INI -->
            <div class="metric-card" onclick="window.location.href='index.php?page=kasir'">
                <div class="card data-card border-success">
                    <div class="metric-body text-center">
                        <div>
                            <div class="card-title mb-1">PESANAN PERIODE INI</div>
                            <div class="card-value mb-2">
                                <?php echo $this->formatNumber($data['metrics']['ordersToday']); ?></div>
                            <div class="card-comparison mb-1">
                                vs periode sebelumnya: <?php echo $this->formatNumber($data['metrics']['ordersYesterday']); ?>
                            </div>
                        </div>
                        <div>
                            <div
                                class="card-change <?php echo $this->getChangeClass($data['metrics']['ordersChange']); ?>">
                                <i
                                    class="fas fa-arrow-<?php echo $this->getArrowIcon($data['metrics']['ordersChange']); ?>"></i>
                                <?php echo $this->formatPercentage($data['metrics']['ordersChange']); ?>% perubahan
                            </div>
                            <div class="info-text mt-2">
                                Periode: <?php echo isset($_GET['start_date']) ? $this->formatDate($_GET['start_date']) . ' - ' . $this->formatDate($_GET['end_date']) : $this->formatDate($data['dates']['today']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MEMBER -->
            <div class="metric-card" onclick="window.location.href='index.php?page=customer'">
                <div class="card data-card border-warning">
                    <div class="metric-body text-center">
                        <div>
                            <div class="card-title mb-1">MEMBER BELANJA PERIODE INI</div>
                            <div class="card-value mb-2">
                                <?php echo $this->formatNumber($data['metrics']['memberAktif']); ?></div>
                            <div class="card-comparison mb-1">
                                vs periode sebelumnya: <?php echo $this->formatNumber($data['metrics']['memberAktifLast']); ?>
                            </div>
                        </div>
                        <div>
                            <div
                                class="card-change <?php echo $this->getChangeClass($data['metrics']['memberAktifChange']); ?>">
                                <i
                                    class="fas fa-arrow-<?php echo $this->getArrowIcon($data['metrics']['memberAktifChange']); ?>"></i>
                                <?php echo $this->formatPercentage($data['metrics']['memberAktifChange']); ?>% perubahan
                            </div>
                            <div class="info-text mt-2">
                                Member aktif dalam periode terpilih
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MARGIN HARI INI -->
            <div class="metric-card">
                <div class="card data-card border-danger">
                    <div class="metric-body text-center">
                        <div>
                            <div class="card-title mb-1">MARGIN PERIODE INI</div>
                            <div class="card-value mb-2"><?php echo $data['metrics']['marginToday']; ?></div>
                            <div class="card-comparison mb-1">
                                vs periode sebelumnya: <?php echo $data['metrics']['marginYesterday']; ?>
                            </div>
                        </div>
                        <div>
                            <div
                                class="card-change <?php echo $this->getChangeClass($data['metrics']['marginChange']); ?>">
                                <i
                                    class="fas fa-arrow-<?php echo $this->getArrowIcon($data['metrics']['marginChange']); ?>"></i>
                                <?php echo $this->formatPercentage($data['metrics']['marginChange']); ?>% perubahan
                            </div>
                            <div class="info-text mt-2">
                                Periode: <?php echo isset($_GET['start_date']) ? $this->formatDate($_GET['start_date']) . ' - ' . $this->formatDate($_GET['end_date']) : $this->formatDate($data['dates']['today']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ONGKIR HARI INI -->
            <div class="metric-card" onclick="window.location.href='index.php?page=kasir'">
                <div class="metric-card">
                    <div class="card data-card border-danger">
                        <div class="metric-body text-center">
                            <div>
                                <div class="card-title mb-1">ONGKIR PERIODE INI</div>
                                <div class="card-value mb-2"><?php echo $data['metrics']['ongkirToday']; ?></div>
                                <div class="card-comparison mb-1">
                                    PB kena ongkir: <?php echo $data['metrics']['ongkirPBToday']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Area Chart dan Perbandingan -->
        <div class="row">
            <div class="col-md-12">
                <div class="chart-container">
                    <h5 class="mb-3 text-center">Perbandingan Pesanan Harian: <?php echo $data['dates']['chartTitle']; ?></h5>
                    <div class="chart-wrapper">
                        <canvas id="ordersComparisonChart" height="300"></canvas>
                    </div>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: rgba(54, 162, 235, 0.6);"></div>
                            <span><?php echo $data['dates']['currentPeriod']; ?></span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: rgba(255, 99, 132, 0.6);"></div>
                            <span><?php echo $data['dates']['previousPeriod']; ?></span>
                        </div>
                    </div>
                    <div class="info-text mt-3 text-center">
                        *Data total pesanan harian (jumlah order per hari)<br>
                        Grafik menunjukkan perbandingan volume pesanan per hari antara periode terpilih dan periode sebelumnya
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- /.container -->
</div>
<!-- /.content -->

<script>
    // Data dari PHP
    const chartLabels = <?php echo json_encode($data['chartData']['labels']); ?>;
    const currentPeriodValues = <?php echo json_encode($data['chartData']['currentMonthValues']); ?>;
    const previousPeriodValues = <?php echo json_encode($data['chartData']['lastMonthValues']); ?>;

    // Inisialisasi chart
    const ctx = document.getElementById('ordersComparisonChart').getContext('2d');
    const ordersComparisonChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                    label: '<?php echo $data['dates']['currentPeriod']; ?>',
                    data: currentPeriodValues,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                    hoverBackgroundColor: 'rgba(54, 162, 235, 1)'
                },
                {
                    label: '<?php echo $data['dates']['previousPeriod']; ?>',
                    data: previousPeriodValues,
                    backgroundColor: 'rgba(255, 99, 132, 0.8)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                    hoverBackgroundColor: 'rgba(255, 99, 132, 1)'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y + ' pesanan';
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Tanggal'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    title: {
                        display: true,
                        text: 'Jumlah Pesanan'
                    },
                    ticks: {
                        callback: function(value) {
                            return value + ' pesanan';
                        }
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });

    // Quick Filter Functions
    function setQuickFilter(period) {
        const today = new Date();
        let startDate, endDate;

        switch (period) {
            case 'today':
                startDate = endDate = formatDate(today);
                break;
            case 'yesterday':
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                startDate = endDate = formatDate(yesterday);
                break;
            case 'this_week':
                const thisWeekStart = new Date(today);
                thisWeekStart.setDate(today.getDate() - today.getDay());
                startDate = formatDate(thisWeekStart);
                endDate = formatDate(today);
                break;
            case 'last_week':
                const lastWeekEnd = new Date(today);
                lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
                const lastWeekStart = new Date(lastWeekEnd);
                lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
                startDate = formatDate(lastWeekStart);
                endDate = formatDate(lastWeekEnd);
                break;
            case 'this_month':
                startDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
                endDate = formatDate(today);
                break;
            case 'last_month':
                const lastMonthStart = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
                startDate = formatDate(lastMonthStart);
                endDate = formatDate(lastMonthEnd);
                break;
        }

        document.getElementById('startDate').value = startDate;
        document.getElementById('endDate').value = endDate;
        document.getElementById('filterForm').submit();
    }

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function resetFilter() {
        document.getElementById('startDate').value = '<?php echo date('Y-m-01'); ?>';
        document.getElementById('endDate').value = '<?php echo date('Y-m-d'); ?>';
        document.getElementById('filterForm').submit();
    }
</script>
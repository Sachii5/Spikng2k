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
                    <input type="date" name="start_date" class="filter-input form-control-modern"
                        value="<?php echo $_GET['start_date'] ?? date('Y-m-01'); ?>"
                        id="startDate">
                </div>

                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-calendar"></i> Tanggal Akhir
                    </label>
                    <input type="date" name="end_date" class="filter-input form-control-modern"
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
            <!-- SALES -->
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
                            <div class="card-change <?php echo $this->getChangeClass($data['metrics']['salesChange']); ?>">
                                <i class="fas fa-arrow-<?php echo $this->getArrowIcon($data['metrics']['salesChange']); ?>"></i>
                                <?php echo $this->formatPercentage($data['metrics']['salesChange']); ?>% perubahan
                            </div>
                            <div class="info-text mt-2">
                                Periode: <?php echo isset($_GET['start_date']) ? $this->formatDate($_GET['start_date']) . ' - ' . $this->formatDate($_GET['end_date']) : $this->formatDate($data['dates']['today']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PESANAN -->
            <div class="metric-card">
                <div class="card data-card border-success">
                    <div class="metric-body text-center">
                        <div>
                            <div class="card-title mb-1">PESANAN PERIODE INI</div>
                            <div class="card-value mb-2"><?php echo $this->formatNumber($data['metrics']['ordersToday']); ?></div>
                            <div class="card-comparison mb-1">
                                vs periode sebelumnya: <?php echo $this->formatNumber($data['metrics']['ordersYesterday']); ?>
                            </div>
                        </div>
                        <div>
                            <div class="card-change <?php echo $this->getChangeClass($data['metrics']['ordersChange']); ?>">
                                <i class="fas fa-arrow-<?php echo $this->getArrowIcon($data['metrics']['ordersChange']); ?>"></i>
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
            <div class="metric-card">
                <div class="card data-card border-warning">
                    <div class="metric-body text-center">
                        <div>
                            <div class="card-title mb-1">MEMBER BELANJA PERIODE INI</div>
                            <div class="card-value mb-2"><?php echo $this->formatNumber($data['metrics']['memberAktif']); ?></div>
                            <div class="card-comparison mb-1">
                                vs periode sebelumnya: <?php echo $this->formatNumber($data['metrics']['memberAktifLast']); ?>
                            </div>
                        </div>
                        <div>
                            <div class="card-change <?php echo $this->getChangeClass($data['metrics']['memberAktifChange']); ?>">
                                <i class="fas fa-arrow-<?php echo $this->getArrowIcon($data['metrics']['memberAktifChange']); ?>"></i>
                                <?php echo $this->formatPercentage($data['metrics']['memberAktifChange']); ?>% perubahan
                            </div>
                            <div class="info-text mt-2">Member aktif dalam periode terpilih</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MARGIN -->
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
                            <div class="card-change <?php echo $this->getChangeClass($data['metrics']['marginChange']); ?>">
                                <i class="fas fa-arrow-<?php echo $this->getArrowIcon($data['metrics']['marginChange']); ?>"></i>
                                <?php echo $this->formatPercentage($data['metrics']['marginChange']); ?>% perubahan
                            </div>
                            <div class="info-text mt-2">
                                Periode: <?php echo isset($_GET['start_date']) ? $this->formatDate($_GET['start_date']) . ' - ' . $this->formatDate($_GET['end_date']) : $this->formatDate($data['dates']['today']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ONGKIR -->
            <div class="metric-card">
                <div class="card data-card border-info">
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

        <!-- Chart -->
        <div class="row">
            <div class="col-md-12">
                <div class="chart-container">
                    <div class="chart-header">
                        <h5 class="chart-title">Grafik Pesanan: <?php echo $data['dates']['chartTitle']; ?></h5>

                        <div class="chart-controls">
                            <div class="view-mode-group">
                                <label class="control-label">Tampilan:</label>
                                <div class="btn-group-toggle">
                                    <button type="button" class="btn-view active" data-view="daily" onclick="changeView('daily')">
                                        <i class="fas fa-calendar-day"></i> Harian
                                    </button>
                                    <button type="button" class="btn-view" data-view="weekly" onclick="changeView('weekly')">
                                        <i class="fas fa-calendar-week"></i> Mingguan
                                    </button>
                                    <button type="button" class="btn-view" data-view="monthly" onclick="changeView('monthly')">
                                        <i class="fas fa-calendar-alt"></i> Bulanan
                                    </button>
                                </div>
                            </div>

                            <div class="comparison-toggle">
                                <label class="switch">
                                    <input type="checkbox" id="showComparison" checked onchange="toggleComparison()">
                                    <span class="slider"></span>
                                </label>
                                <label class="control-label" for="showComparison">Bandingkan Periode</label>
                            </div>
                        </div>
                    </div>

                    <div class="chart-wrapper">
                        <canvas id="ordersComparisonChart" height="300"></canvas>
                    </div>

                    <div class="chart-legend" id="chartLegend">
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: rgba(54, 162, 235, 0.8);"></div>
                            <span id="currentPeriodLabel"><?php echo $data['dates']['currentPeriod']; ?></span>
                        </div>
                        <div class="legend-item" id="previousLegend">
                            <div class="legend-color" style="background-color: rgba(255, 99, 132, 0.8);"></div>
                            <span id="previousPeriodLabel"><?php echo $data['dates']['previousPeriod']; ?></span>
                        </div>
                    </div>

                    <div class="info-text mt-3 text-center" id="chartInfo">
                        *Data total pesanan harian (jumlah order per hari)<br>
                        Grafik menunjukkan perbandingan volume pesanan per hari antara periode terpilih dan periode sebelumnya
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Data for JavaScript -->
<script id="chartData" type="application/json">
    <?php echo json_encode($data['chartData']); ?>
</script>

<script src="assets/js/dashboard.js"></script>
<?php
require_once 'models/DashboardModel.php';

/**
 * Controller untuk halaman Dashboard dengan Filter Tanggal
 */
class DashboardController
{
    private $dashboardModel;
    private $dates;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
        $this->setDates();
    }

    /**
     * Menginisialisasi tanggal berdasarkan filter atau default
     */
    private function setDates()
    {
        // Ambil filter dari GET parameter
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

        // Validasi tanggal
        if ($startDate && $endDate) {
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);

            // Hitung durasi periode untuk menentukan periode pembanding
            $interval = $start->diff($end);
            $days = $interval->days;

            // Periode pembanding (periode sebelumnya dengan durasi yang sama)
            $previousStart = clone $start;
            $previousStart->modify("-" . ($days + 1) . " days");
            $previousEnd = clone $start;
            $previousEnd->modify("-1 day");
        } else {
            // Default: bulan ini (dari tanggal 1 sampai hari ini)
            $today = new DateTime('today');
            $start = new DateTime('first day of this month');
            $end = clone $today;

            // Hitung durasi periode
            $interval = $start->diff($end);
            $days = $interval->days;

            // Pembanding: bulan lalu (dari tanggal 1 sampai tanggal yang sama)
            $previousStart = new DateTime('first day of last month');
            $previousEnd = (clone $today)->modify('last month');
        }

        $this->dates = [
            // Periode saat ini
            'currentStart' => $start->format('Y-m-d'),
            'currentEnd' => $end->format('Y-m-d'),

            // Periode pembanding
            'previousStart' => $previousStart->format('Y-m-d'),
            'previousEnd' => $previousEnd->format('Y-m-d'),

            // Hari ini (untuk keperluan lain)
            'today' => (new DateTime('today'))->format('Y-m-d'),

            // Durasi periode
            'days' => $days,

            // Format untuk chart
            'currentPeriodLabel' => $start->format('d M Y') . ($days > 0 ? ' - ' . $end->format('d M Y') : ''),
            'previousPeriodLabel' => $previousStart->format('d M Y') . ($days > 0 ? ' - ' . $previousEnd->format('d M Y') : '')
        ];
    }

    // Helper untuk styling nilai perubahan pada view
    public function getChangeClass($change)
    {
        if ($change > 0) return 'positive';
        elseif ($change < 0) return 'negative';
        else return 'neutral';
    }

    public function getArrowIcon($change)
    {
        if ($change > 0) return 'up';
        elseif ($change < 0) return 'down';
        else return 'right';
    }

    public function formatPercentage($number)
    {
        return number_format(abs($number), 1);
    }

    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return (($current - $previous) / $previous) * 100;
    }

    private function formatCurrency($amount)
    {
        if ($amount >= 1000000000) {
            return 'Rp ' . number_format($amount / 1000000000, 2) . 'M';
        } elseif ($amount >= 1000000) {
            return 'Rp ' . number_format($amount / 1000000, 1) . 'Jt';
        } else {
            return 'Rp ' . number_format($amount);
        }
    }

    public function formatDate($date, $format = 'd M Y')
    {
        return date($format, strtotime($date));
    }

    public function formatNumber($number)
    {
        return number_format($number);
    }

    /**
     * Mengambil data sales untuk periode range (start - end)
     */
    private function getSalesForPeriod($startDate, $endDate)
    {
        $salesData = $this->dashboardModel->getSalesForPeriod($startDate, $endDate);
        return $salesData['gross_periode'] ?? 0;
    }

    /**
     * Mengambil data pesanan untuk periode range
     */
    private function getOrdersForPeriod($startDate, $endDate)
    {
        $ordersData = $this->dashboardModel->getOrdersForPeriod($startDate, $endDate);
        return $ordersData['total_pb'] ?? 0;
    }

    /**
     * Mengambil data member untuk periode range
     */
    private function getMembersForPeriod($startDate, $endDate)
    {
        $memberData = $this->dashboardModel->getMembersForPeriod($startDate, $endDate);
        return $memberData['member_aktif'] ?? 0;
    }

    /**
     * Mengambil data margin untuk periode range
     */
    private function getMarginForPeriod($startDate, $endDate)
    {
        $marginData = $this->dashboardModel->getMarginForPeriod($startDate, $endDate);
        return $marginData['margin_periode'] ?? 0;
    }

    /**
     * Mengambil data ongkir untuk periode range
     */
    private function getOngkirForPeriod($startDate, $endDate)
    {
        $ongkirData = $this->dashboardModel->getOngkirForPeriod($startDate, $endDate);
        return [
            'pot_ongkir' => $ongkirData['pot_ongkir'] ?? 0,
            'pb_ongkir' => $ongkirData['pb_ongkir'] ?? 0
        ];
    }

    /**
     * Render halaman dashboard dengan filter
     */
    public function index()
    {
        // Ambil data untuk periode saat ini
        $salesToday = $this->getSalesForPeriod($this->dates['currentStart'], $this->dates['currentEnd']);
        $ordersToday = $this->getOrdersForPeriod($this->dates['currentStart'], $this->dates['currentEnd']);
        $memberAktif = $this->getMembersForPeriod($this->dates['currentStart'], $this->dates['currentEnd']);
        $marginToday = $this->getMarginForPeriod($this->dates['currentStart'], $this->dates['currentEnd']);
        $ongkirData = $this->getOngkirForPeriod($this->dates['currentStart'], $this->dates['currentEnd']);

        // Ambil data untuk periode pembanding
        $salesYesterday = $this->getSalesForPeriod($this->dates['previousStart'], $this->dates['previousEnd']);
        $ordersYesterday = $this->getOrdersForPeriod($this->dates['previousStart'], $this->dates['previousEnd']);
        $memberAktifLast = $this->getMembersForPeriod($this->dates['previousStart'], $this->dates['previousEnd']);
        $marginYesterday = $this->getMarginForPeriod($this->dates['previousStart'], $this->dates['previousEnd']);

        // Ambil data ongkir untuk periode pembanding
        $ongkirYesterdayData = $this->getOngkirForPeriod($this->dates['previousStart'], $this->dates['previousEnd']);

        // Hitung perubahan ongkir
        $ongkirChange = $this->calculatePercentageChange($ongkirData['pot_ongkir'], $ongkirYesterdayData['pot_ongkir']);

        // Data Total Member (Cumulative)
        $totalMemberToday = $this->dashboardModel->getTotalMembersAsOf($this->dates['currentEnd']);
        $totalMemberYesterday = $this->dashboardModel->getTotalMembersAsOf($this->dates['previousEnd']);
        $totalMemberChange = $this->calculatePercentageChange($totalMemberToday, $totalMemberYesterday);

        // Hitung perubahan
        $salesChange = $this->calculatePercentageChange($salesToday, $salesYesterday);
        $ordersChange = $this->calculatePercentageChange($ordersToday, $ordersYesterday);
        $memberAktifChange = $this->calculatePercentageChange($memberAktif, $memberAktifLast);
        $marginChange = $this->calculatePercentageChange($marginToday, $marginYesterday);

        // Ambil data untuk chart (harian dalam periode)
        $dailyOrdersData = $this->dashboardModel->getDailyOrdersForPeriod(
            $this->dates['previousStart'],
            $this->dates['currentEnd']
        );

        // Proses data chart
        $chartData = $this->processChartData($dailyOrdersData);

        // Siapkan data untuk view
        $data = [
            'page' => 'dashboard',
            'dates' => [
                'today' => $this->dates['today'],
                'currentPeriod' => $this->dates['currentPeriodLabel'],
                'previousPeriod' => $this->dates['previousPeriodLabel'],
                'chartTitle' => $this->dates['currentPeriodLabel'] . ' vs ' . $this->dates['previousPeriodLabel']
            ],
            'metrics' => [
                'salesToday' => $this->formatCurrency($salesToday),
                'salesYesterday' => $this->formatCurrency($salesYesterday),
                'salesChange' => $salesChange,
                'ordersToday' => $ordersToday,
                'ordersYesterday' => $ordersYesterday,
                'ordersChange' => $ordersChange,
                'memberAktif' => $memberAktif,
                'memberAktifLast' => $memberAktifLast,
                'memberAktifChange' => $memberAktifChange,
                'marginToday' => $this->formatCurrency($marginToday),
                'marginYesterday' => $this->formatCurrency($marginYesterday),
                'marginChange' => $marginChange,
                'ongkirToday' => $this->formatCurrency($ongkirData['pot_ongkir']),
                'ongkirYesterday' => $this->formatCurrency($ongkirYesterdayData['pot_ongkir']), // tambahkan ini
                'ongkirPBToday' => $this->formatNumber($ongkirData['pb_ongkir']),
                'ongkirPBYesterday' => $this->formatNumber($ongkirYesterdayData['pb_ongkir']), // tambahkan ini
                'ongkirChange' => $ongkirChange, // tambahkan ini
                'totalMemberToday' => $this->formatNumber($totalMemberToday),
                'totalMemberYesterday' => $this->formatNumber($totalMemberYesterday),
                'totalMemberChange' => $totalMemberChange,
            ],
            'chartData' => $chartData
        ];

        // Load view dashboard
        require_once 'views/dashboard.php';
    }

    /**
     * Memproses data untuk chart perbandingan
     */
    private function processChartData($dailyOrdersData)
    {
        $currentPeriodData = [];
        $previousPeriodData = [];
        $labels = [];

        // Parse tanggal periode
        $currentStart = new DateTime($this->dates['currentStart']);
        $currentEnd = new DateTime($this->dates['currentEnd']);
        $previousStart = new DateTime($this->dates['previousStart']);
        $previousEnd = new DateTime($this->dates['previousEnd']);

        // Buat array untuk semua tanggal dalam periode current
        $period = new DatePeriod(
            $currentStart,
            new DateInterval('P1D'),
            $currentEnd->modify('+1 day')
        );

        foreach ($period as $date) {
            $dateKey = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            $currentPeriodData[$dateKey] = 0;
        }

        // Buat array untuk semua tanggal dalam periode previous
        $previousPeriod = new DatePeriod(
            $previousStart,
            new DateInterval('P1D'),
            $previousEnd->modify('+1 day')
        );

        foreach ($previousPeriod as $date) {
            $dateKey = $date->format('Y-m-d');
            $previousPeriodData[$dateKey] = 0;
        }

        // Isi data dari database
        foreach ($dailyOrdersData as $data) {
            $tanggal = $data['tanggal'];
            $orders = $data['total_pesanan'];

            if (isset($currentPeriodData[$tanggal])) {
                $currentPeriodData[$tanggal] = $orders;
            } elseif (isset($previousPeriodData[$tanggal])) {
                $previousPeriodData[$tanggal] = $orders;
            }
        }

        return [
            'labels' => $labels,
            'currentMonthValues' => array_values($currentPeriodData),
            'lastMonthValues' => array_values($previousPeriodData)
        ];
    }
}

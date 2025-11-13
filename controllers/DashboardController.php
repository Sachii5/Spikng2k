<?php
require_once 'models/DashboardModel.php';
// require_once 'models/KasirModel.php';

/**
 * Controller untuk halaman Dashboard dan Kasir
 * - Mengambil data ringkasan penjualan, member, pesanan, dan margin
 * - Menyusun data untuk view dan API JSON
 */
class DashboardController
{
    /** @var DashboardModel */
    private $dashboardModel;
    /** @var KasirModel */
    private $kasirModel;
    /** @var array<string,string> Kumpulan tanggal yang sering dipakai */
    private $dates;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
        // $this->kasirModel = new KasirModel();
        $this->setDates();
    }

    /**
     * Menginisialisasi tanggal-tanggal penting untuk rentang waktu:
     * - 1 bulan ini, 1 bulan lalu, hari ini, dan tanggal yang sama bulan lalu
     * - Disertai kunci kompatibilitas lama (yesterday, lastMonthYesterday)
     */
    private function setDates()
    {
        $today = new DateTime('today');
        $currentMonthStart = (clone $today)->modify('first day of this month');
        $currentMonthEnd = (clone $today)->modify('last day of this month');
        $lastMonthStart = (clone $today)->modify('first day of last month');
        $lastMonthEnd = (clone $today)->modify('last day of last month');
        $lastMonthSameDay = (clone $today)->modify('last month');

        $this->dates = [
            // 1 bulan ini
            'currentMonthStart' => $currentMonthStart->format('Y-m-d'),
            'currentMonthEnd' => $currentMonthEnd->format('Y-m-d'),
            // 1 bulan lalu
            'lastMonthStart' => $lastMonthStart->format('Y-m-d'),
            'lastMonthEnd' => $lastMonthEnd->format('Y-m-d'),
            // Hari ini
            'today' => $today->format('Y-m-d'),
            // Bulan lalu di tanggal hari ini
            'lastMonthSameDay' => $lastMonthSameDay->format('Y-m-d'),
            // Kompatibilitas lama
            'yesterday' => (clone $today)->modify('-1 day')->format('Y-m-d'),
            'lastMonthYesterday' => (clone $today)->modify('last month -1 day')->format('Y-m-d')
        ];
    }

    // Helper untuk styling nilai perubahan pada view

    public function getChangeClass($change)
    {
        if ($change > 0) {
            return 'positive';
        } elseif ($change < 0) {
            return 'negative';
        } else {
            return 'neutral';
        }
    }

    public function getArrowIcon($change)
    {
        if ($change > 0) {
            return 'up';
        } elseif ($change < 0) {
            return 'down';
        } else {
            return 'right';
        }
    }

    /** Mengubah angka menjadi persen dengan 1 desimal (nilai absolut). */
    public function formatPercentage($number)
    {
        return number_format(abs($number), 1);
    }



    /**
     * Menghitung persen perubahan dari nilai sebelumnya ke saat ini.
     * Menghindari pembagian nol.
     */
    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return (($current - $previous) / $previous) * 100;
    }

    /**
     * Format rupiah ringkas: >=1M jadi M, >=1Jt jadi Jt, lainnya bilangan bulat.
     */
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

    /** Format bulan (YYYY-MM) menjadi "Mon YYYY" untuk tampilan chart. */
    private function formatMonth($month)
    {
        return date('M Y', strtotime($month . '-01'));
    }

    /** Format tanggal umum untuk tampilan. */
    public function formatDate($date, $format = 'd M Y')
    {
        return date($format, strtotime($date));
    }

    /** Format angka umum (ribuan dipisah koma). */
    public function formatNumber($number)
    {
        return number_format($number);
    }

    /** Hitung persen bagian terhadap total, aman saat total=0. */
    public function calculatePercentage($partial, $total)
    {
        if ($total == 0) return 0;
        return number_format(($partial / $total) * 100, 1);
    }

    // Method untuk dashboard utama
    /**
     * Render halaman dashboard utama.
     * Mengambil data dari model, menghitung perubahan, dan menyiapkan data chart.
     */
    public function index()
    {
        // Ambil data dari model
        $salesTodayData = $this->dashboardModel->getSalesToday($this->dates['today']);
        // bandingkan dengan tanggal yang sama bulan lalu
        $salesYesterdayData = $this->dashboardModel->getSalesYesterday($this->dates['lastMonthSameDay']);
        $memberAktifData = $this->dashboardModel->getMembers($this->dates['today']);
        $memberAktifLastData = $this->dashboardModel->getMembersYesterday($this->dates['lastMonthSameDay']);
        $memberRegistrasiCurrentData = $this->dashboardModel->getNewRegistrationsCurrentMonth($this->dates['currentMonthStart'], $this->dates['currentMonthEnd']);
        $memberRegistrasiLastData = $this->dashboardModel->getNewRegistrationsLastMonth($this->dates['lastMonthStart'], $this->dates['lastMonthEnd']);
        $dailyOrdersData = $this->dashboardModel->getDailyOrders($this->dates['lastMonthStart'], $this->dates['currentMonthEnd']);
        $ordersTodayData = $this->dashboardModel->getOrdersToday($this->dates['today']);
        $ordersYesterdayData = $this->dashboardModel->getOrdersYesterday($this->dates['lastMonthSameDay']);
        $marginTodayData = $this->dashboardModel->getMarginToday($this->dates['today']);
        $marginYesterdayData = $this->dashboardModel->getMarginYesterday($this->dates['lastMonthSameDay']);
        $potOngkirTodayData = $this->dashboardModel->getOngkir($this->dates['today']);
        $pbOngkirTodayData = $this->dashboardModel->getOngkir($this->dates['today']);

        // Proses data
        $salesToday = $salesTodayData['gross_hari_ini'] ?? 0;
        $salesYesterday = $salesYesterdayData['gross_kemarin'] ?? 0;
        $salesChange = $this->calculatePercentageChange($salesToday, $salesYesterday);

        $memberAktif = $memberAktifData['member_aktif'] ?? 0;
        $memberAktifLast = $memberAktifLastData['member_aktif'] ?? 0;
        $memberAktifChange = $this->calculatePercentageChange($memberAktif, $memberAktifLast);

        $memberRegistrasiCurrent = $memberRegistrasiCurrentData['jml_mem'] ?? 0;
        $memberRegistrasiLast = $memberRegistrasiLastData['jml_mem'] ?? 0;
        $memberRegistrasiChange = $this->calculatePercentageChange($memberRegistrasiCurrent, $memberRegistrasiLast);

        $ordersToday = $ordersTodayData['total_pb'] ?? 0;
        $ordersYesterday = $ordersYesterdayData['total_pb'] ?? 0;
        $ordersChange = $this->calculatePercentageChange($ordersToday, $ordersYesterday);

        $marginToday = $marginTodayData['margin_hari_ini'] ?? 0;
        $marginYesterday = $marginYesterdayData['margin_kemarin'] ?? 0;
        $marginChange = $this->calculatePercentageChange($marginToday, $marginYesterday);
        $PotongkirToday = $potOngkirTodayData['pot_ongkir'] ?? 0;
        $ongkirPBToday = $pbOngkirTodayData['pb_ongkir'] ?? 0;

        // Proses data untuk chart
        $currentMonthDays = [];
        $lastMonthDays = [];

        for ($i = 1; $i <= 31; $i++) {
            $day = str_pad($i, 2, '0', STR_PAD_LEFT);
            $currentMonthDays[$day] = 0;
            $lastMonthDays[$day] = 0;
        }

        foreach ($dailyOrdersData as $data) {
            $day = $data['hari'];
            $month = $data['bulan'];
            $year = $data['tahun'];
            $orders = $data['total_pesanan'];

            $currentYear = date('Y');
            $currentMonth = date('n');
            $lastMonth = date('n', strtotime('-1 month'));

            if ($month == $currentMonth && $year == $currentYear) {
                $currentMonthDays[$day] = $orders;
            } elseif ($month == $lastMonth && $year == date('Y', strtotime('-1 month'))) {
                $lastMonthDays[$day] = $orders;
            }
        }

        // Siapkan data untuk view
        $data = [
            'page' => 'dashboard',
            'dates' => [
                'today' => $this->dates['today'],
                'yesterday' => $this->dates['yesterday'],
                'currentMonth' => $this->formatMonth(date('Y-m')),
                'lastMonth' => $this->formatMonth(date('Y-m', strtotime('-1 month')))
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
                'ongkirToday' => $this->formatCurrency($PotongkirToday),
                'ongkirPBToday' => $this->formatCurrency($ongkirPBToday)
            ],
            'chartData' => [
                'labels' => array_keys($currentMonthDays),
                'currentMonthValues' => array_values($currentMonthDays),
                'lastMonthValues' => array_values($lastMonthDays)
            ]
        ];

        // Load view dashboard
        require_once 'views/dashboard.php';
    }
}

<?php
require_once 'models/KasirModel.php';

class KasirController
{
    private $model;

    public function __construct()
    {
        $this->model = new KasirModel();
    }

    public function index()
    {
        // Ambil parameter tanggal dari GET request
        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        // Validasi tanggal
        if ($startDate > $endDate) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        // Ambil data dari model
        $orderData = $this->model->getOrderStatusData($startDate, $endDate);
        $orderItems = $this->model->getOrderItemsData($startDate, $endDate);
        $amt = $this->model->getAMT($startDate, $endDate);
        $pbData = $this->model->getPBData($startDate, $endDate);

        // debug: tulis jumlah baris AMT ke log server
        error_log('Controller AMT rows: ' . count($amt));
        error_log('Controller PB rows: ' . count($pbData));

        // Hitung total pesanan
        $totalPesanan = 0;
        foreach ($orderData as $item) {
            $totalPesanan += (int)$item['jumlah'];
        }

        // Hitung total item
        $totalItemOrder = 0;
        $totalItemReal = 0;
        foreach ($orderItems as $item) {
            $totalItemOrder += (int)($item['order'] ?? 0);
            $totalItemReal += (int)($item['real'] ?? 0);
        }

        // Konfigurasi status
        $statusConfig = [
            'SELESAI' => ['class' => 'success', 'icon' => 'fa-check-circle', 'color' => '#27ae60'],
            'SIAP PICKING' => ['class' => 'primary', 'icon' => 'fa-box-open', 'color' => '#3498db'],
            'SIAP SCANNING' => ['class' => 'warning', 'icon' => 'fa-barcode', 'color' => '#f39c12'],
            'SIAP DRAFT STRUK' => ['class' => 'secondary', 'icon' => 'fa-file-contract', 'color' => '#34495e'],
            'PEMBAYARAN' => ['class' => 'info', 'icon' => 'fa-credit-card', 'color' => '#17a2b8'],
            'SIAP STRUK' => ['class' => 'purple', 'icon' => 'fa-receipt', 'color' => '#9b59b6'],
            'BATAL' => ['class' => 'danger', 'icon' => 'fa-times-circle', 'color' => '#e74c3c'],
            'SIAP SEND HANDHELD' => ['class' => 'orange', 'icon' => 'fa-mobile-alt', 'color' => '#f39c12'],
            'LAINNYA' => ['class' => 'dark', 'icon' => 'fa-question-circle', 'color' => '#2c3e50']
        ];

        // Siapkan data untuk view
        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'orderData' => $orderData,
            'orderItems' => $orderItems,
            'totalPesanan' => $totalPesanan,
            'totalItemOrder' => $totalItemOrder,
            'totalItemReal' => $totalItemReal,
            'statusConfig' => $statusConfig,
            'hasData' => count($orderData) > 0,
            'hasItemsData' => count($orderItems) > 0,
            'controller' => $this,
            'amt' => $amt,
            'pbData' => $pbData
        ];

        // Load view
        require_once 'views/kasir.php';
    }

    // API endpoint untuk get detail
    public function getDetail()
    {
        // Set header JSON di awal
        header('Content-Type: application/json');

        try {
            // Validasi parameter
            if (!isset($_GET['notrans']) || !isset($_GET['tgl'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Parameter tidak lengkap (notrans dan tgl required)',
                    'debug' => [
                        'notrans' => $_GET['notrans'] ?? 'missing',
                        'tgl' => $_GET['tgl'] ?? 'missing'
                    ]
                ]);
                return;
            }

            $noTrans = $_GET['notrans'];
            $tglTrans = $_GET['tgl'];

            error_log("API getDetail called: notrans=$noTrans, tgl=$tglTrans");

            // Ambil detail dari model
            $details = $this->model->getDetailByNoTrans($noTrans, $tglTrans);

            error_log("Detail result count: " . count($details));

            // Return JSON response
            echo json_encode([
                'success' => true,
                'data' => $details,
                'count' => count($details),
                'params' => [
                    'notrans' => $noTrans,
                    'tgl' => $tglTrans
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            error_log("Error in getDetail: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Helper functions untuk view
    public function formatNumber($number)
    {
        return number_format((float)$number, 0, ',', '.');
    }

    public function formatDate($date, $format = 'd M Y')
    {
        if (empty($date) || $date === 'now') {
            return date($format);
        }
        return date($format, strtotime($date));
    }

    public function calculatePercentage($partial, $total)
    {
        if ($total == 0) return 0;
        return number_format(($partial / $total) * 100, 1);
    }
}

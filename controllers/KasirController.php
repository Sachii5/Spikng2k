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

        // debug: tulis jumlah baris AMT ke log server
        error_log('AMT rows: ' . count($amt) . ' | dates: ' . $startDate . ' - ' . $endDate);
        error_log('AMT preview: ' . print_r(array_slice($amt, 0, 20), true));

        // Hitung total pesanan
        $totalPesanan = 0;
        foreach ($orderData as $item) {
            $totalPesanan += (int)$item['jumlah'];
        }

        // Hitung total item
        $totalItemOrder = 0;
        $totalItemReal = 0;
        foreach ($orderItems as $item) {
            $totalItemOrder += (int)$item['order'];
            $totalItemReal += (int)$item['real'];
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
            'amt' => $amt
        ];


        // Load view
        require_once 'views/kasir.php';
    }

    // Helper functions untuk view
    public function formatNumber($number)
    {
        return number_format($number);
    }

    public function formatDate($date, $format = 'd M Y')
    {
        return date($format, strtotime($date));
    }

    public function calculatePercentage($partial, $total)
    {
        if ($total == 0) return 0;
        return number_format(($partial / $total) * 100, 1);
    }
}

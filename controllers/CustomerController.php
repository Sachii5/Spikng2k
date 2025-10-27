<?php
require_once 'models/CustomerModel.php';

class CustomerController
{
    private $model;

    public function __construct()
    {
        $this->model = new CustomerModel();
    }

    // Helper functions
    public function formatCurrency($amount)
    {
        if ($amount >= 1000000000) {
            return 'Rp ' . number_format($amount / 1000000000, 1) . 'M';
        } elseif ($amount >= 1000000) {
            return 'Rp ' . number_format($amount / 1000000, 1) . 'Jt';
        } else {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        }
    }

    public function cleanString($string)
    {
        if (is_null($string)) {
            return '';
        }
        $cleaned = preg_replace('/\s+/', ' ', $string);
        $cleaned = trim($cleaned);
        return $cleaned;
    }

    public function formatNumber($number)
    {
        if (is_null($number)) {
            return '0';
        }
        $number = floatval($number);
        if ($number >= 1000000) {
            return number_format($number / 1000000, 1) . 'Jt';
        } elseif ($number >= 1000) {
            return number_format($number / 1000, 1) . 'K';
        }
        return number_format($number);
    }

    public function formatPercentage($number)
    {
        if (is_null($number)) {
            return '0.0';
        }
        return number_format(abs($number), 1);
    }

    public function getChangeClass($change)
    {
        if (is_null($change)) {
            return 'neutral';
        }
        return $change >= 0 ? 'positive' : 'negative';
    }

    public function getArrowIcon($change)
    {
        if (is_null($change)) {
            return 'right';
        }
        return $change >= 0 ? 'up' : 'down';
    }

    public function formatDate($date, $format = 'd M Y')
    {
        if (!$date || $date == '0000-00-00') {
            return '-';
        }
        return date($format, strtotime($date));
    }

    // Helper untuk membuat URL pagination
    public function getPaginationUrl($page)
    {
        $params = $_GET;
        $params['member_page'] = $page;

        // Hapus parameter yang tidak diperlukan
        unset($params['ajax']);

        return 'index.php?' . http_build_query($params);
    }

    public function index()
    {
        // Ambil parameter
        $salesman = $_GET['salesman'] ?? null;

        // Pagination parameters
        $memberPage = $_GET['member_page'] ?? 1;
        $memberPage = max(1, intval($memberPage)); // Pastikan minimal 1
        $perPage = 15; // Changed from 10 to 15

        // Data summary
        $summaryData = $this->model->getCustomerSummary();

        // List salesman untuk dropdown
        $salesmanList = $this->model->getSalesmanList();

        // Data member dengan pagination
        $memberData = $this->model->getMembers($salesman, $memberPage, $perPage);
        $totalMembers = $this->model->countMembers($salesman);
        $totalMemberPages = ceil($totalMembers / $perPage);

        // Jika request AJAX, return JSON
        if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
            $this->handleAjaxRequest($salesman, $memberPage, $perPage);
            return;
        }

        // Siapkan data untuk view
        $data = [
            'page' => 'customer',
            'summaryData' => $summaryData,
            'salesmanList' => $salesmanList,
            'memberData' => $memberData,
            'selectedSalesman' => $salesman,
            'memberPage' => $memberPage,
            'perPage' => $perPage,
            'totalMembers' => $totalMembers,
            'totalMemberPages' => $totalMemberPages,
            'controller' => $this
        ];

        // Load view
        require_once 'views/customer.php';
    }

    private function handleAjaxRequest($salesman, $memberPage, $perPage)
    {
        header('Content-Type: application/json');

        $memberData = $this->model->getMembers($salesman, $memberPage, $perPage);
        $totalMembers = $this->model->countMembers($salesman);

        // Format data untuk response
        $formattedMemberData = [];
        foreach ($memberData as $memberItem) {
            $formattedMemberData[] = [
                'kode_member' => $memberItem['kode_member'],
                'nama_member' => $memberItem['nama_member'],
                'salesman' => $memberItem['salesman'],
                'total_pesanan' => $memberItem['total_pesanan'],
                'total_pesanan_formatted' => $this->formatNumber($memberItem['total_pesanan']),
                'total_pembelian' => $memberItem['total_pembelian'],
                'total_pembelian_formatted' => $this->formatCurrency($memberItem['total_pembelian']),
                'last_order' => $memberItem['last_order'],
                'last_order_formatted' => $this->formatDate($memberItem['last_order'], 'd M Y'),
                'tgl_registrasi' => $memberItem['tgl_registrasi'],
                'tgl_registrasi_formatted' => $this->formatDate($memberItem['tgl_registrasi'], 'd M Y')
            ];
        }

        $response = [
            'memberData' => $formattedMemberData,
            'displayedMembers' => count($memberData) + (($memberPage - 1) * $perPage),
            'totalMembers' => $totalMembers
        ];

        echo json_encode($response);
        exit;
    }
}

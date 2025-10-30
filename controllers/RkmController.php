<?php

class RkmController
{
    public function index()
    {
        // Koneksi database
        require_once 'config/database.php';
        $db = new Database();
        $db->connect();

        // Ambil data RKM
        $query = "SELECT * FROM rkm_table"; // Ganti dengan query yang sesuai
        $result = $db->query($query);
        $rkmData = $db->fetchAll($result);
        $db->freeResult($result);

        // Siapkan data untuk view
        $data = [
            'rkmData' => $rkmData,
            'hasData' => count($rkmData) > 0,
            'controller' => $this
        ];

        // Load view
        require_once 'views/rkm.php';
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
}
<?php
require_once 'config/database.php';

class CustomerModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->db->connect();
    }

    // Data Salesman untuk dropdown filter
    public function getSalesmanList()
    {
        $query = "
            SELECT 
                cus_nosalesman AS salesman,
                COUNT(DISTINCT cus_kodemember) AS total_member
            FROM tbmaster_customer 
            INNER JOIN tbmaster_customercrm ON cus_kodemember = crm_kodemember
            WHERE cus_kodeigr = '2K' 
                AND crm_kodeigr = '2K' 
                AND cus_namamember <> 'NEW'
                AND cus_nosalesman IS NOT NULL
                AND cus_nosalesman <> ''
            GROUP BY cus_nosalesman
            ORDER BY cus_nosalesman ASC
        ";

        $result = $this->db->query($query);
        if ($result) {
            $data = $this->db->fetchAll($result);
            $this->db->freeResult($result);
            return $data;
        }
        return [];
    }

    // Data Member (semua atau per salesman) dengan pagination
    public function getMembers($salesman = null, $page = 1, $perPage = 15)  // Changed from 10 to 15
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $whereClause = "WHERE cus_kodeigr = '2K' 
                AND crm_kodeigr = '2K' 
                AND cus_namamember <> 'NEW'";

        if ($salesman) {
            $whereClause .= " AND cus_nosalesman = $1";
            $params[] = $salesman;
        }

        $paramCount = count($params);
        $limitParam = '$' . ($paramCount + 1);
        $offsetParam = '$' . ($paramCount + 2);
        $params[] = $perPage;
        $params[] = $offset;

        $query = "
            SELECT 
                cus_kodemember AS kode_member,
                cus_namamember AS nama_member,
                cus_nosalesman AS salesman,
                cus_tglmulai AS tgl_registrasi,
                COUNT(obi_nopb) AS total_pesanan,
                COALESCE(SUM(obi_ttlorder), 0) AS total_pembelian,
                MAX(obi_tglorder) AS last_order
            FROM tbmaster_customer 
            INNER JOIN tbmaster_customercrm ON cus_kodemember = crm_kodemember
            LEFT JOIN tbtr_obi_h ON cus_kodemember = obi_kdmember
            $whereClause
            GROUP BY cus_kodemember, cus_namamember, cus_nosalesman, cus_tglmulai
            ORDER BY total_pembelian DESC, total_pesanan DESC
            LIMIT $limitParam OFFSET $offsetParam
        ";

        $result = $this->db->query($query, $params);
        if ($result) {
            $data = $this->db->fetchAll($result);
            $this->db->freeResult($result);
            return $data;
        }
        return [];
    }

    // Hitung total member untuk pagination
    public function countMembers($salesman = null)
    {
        $params = [];
        $whereClause = "WHERE cus_kodeigr = '2K' 
                AND crm_kodeigr = '2K' 
                AND cus_namamember <> 'NEW'";

        if ($salesman) {
            $whereClause .= " AND cus_nosalesman = $1";
            $params[] = $salesman;
        }

        $query = "
            SELECT COUNT(DISTINCT cus_kodemember) as total
            FROM tbmaster_customer 
            INNER JOIN tbmaster_customercrm ON cus_kodemember = crm_kodemember
            $whereClause
        ";

        $result = $this->db->query($query, $params);
        if ($result) {
            $data = $this->db->fetch($result);
            $this->db->freeResult($result);
            return $data['total'] ?? 0;
        }
        return 0;
    }

    // Data Ringkasan Customer
    public function getCustomerSummary()
    {
        $query = "
            SELECT 
                COUNT(DISTINCT cus_kodemember) AS total_member,
                COUNT(DISTINCT cus_nosalesman) AS total_salesman,
                COUNT(DISTINCT CASE WHEN DATE_TRUNC('month', CUS_TGLMULAI) = DATE_TRUNC('month', CURRENT_DATE) THEN cus_kodemember END) AS member_baru_bulan_ini,
                COUNT(DISTINCT obi_kdmember) AS member_aktif_transaksi
            FROM tbmaster_customer 
            INNER JOIN tbmaster_customercrm ON cus_kodemember = crm_kodemember
            LEFT JOIN tbtr_obi_h ON cus_kodemember = obi_kdmember
            WHERE cus_kodeigr = '2K' 
                AND crm_kodeigr = '2K' 
                AND cus_namamember <> 'NEW'
        ";

        $result = $this->db->query($query);
        if ($result) {
            $data = $this->db->fetch($result);
            $this->db->freeResult($result);
            return $data;
        }
        return [];
    }
}

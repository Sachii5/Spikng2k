<?php
require_once 'config/database.php';

class KasirModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->db->connect();
    }

    public function getOrderStatusData($startDate, $endDate)
    {
        $query = "
            SELECT 
                COUNT(*) as jumlah,
                CASE
                    WHEN obi_recid = '6' THEN 'SELESAI'
                    WHEN obi_recid = '1' THEN 'SIAP PICKING'
                    WHEN obi_recid = '2' THEN 'SIAP SCANNING'
                    WHEN obi_recid = '3' THEN 'SIAP DRAFT STRUK'
                    WHEN obi_recid = '4' THEN 'PEMBAYARAN'
                    WHEN obi_recid = '5' THEN 'SIAP STRUK'
                    WHEN obi_recid LIKE 'B%' THEN 'BATAL'
                    WHEN obi_recid IS NULL THEN 'SIAP SEND HANDHELD'
                    ELSE 'LAINNYA'
                END AS status_pesanan
            FROM tbtr_obi_h
            LEFT JOIN tbmaster_customer ON obi_kdmember = cus_kodemember
            WHERE obi_tglorder::date BETWEEN $1 AND $2
            GROUP BY 
                CASE
                    WHEN obi_recid = '6' THEN 'SELESAI'
                    WHEN obi_recid = '1' THEN 'SIAP PICKING'
                    WHEN obi_recid = '2' THEN 'SIAP SCANNING'
                    WHEN obi_recid = '3' THEN 'SIAP DRAFT STRUK'
                    WHEN obi_recid = '4' THEN 'PEMBAYARAN'
                    WHEN obi_recid = '5' THEN 'SIAP STRUK'
                    WHEN obi_recid LIKE 'B%' THEN 'BATAL'
                    WHEN obi_recid IS NULL THEN 'SIAP SEND HANDHELD'
                    ELSE 'LAINNYA'
                END
            ORDER BY jumlah DESC
        ";

        $result = $this->db->query($query, [$startDate, $endDate]);

        if ($result) {
            $data = $this->db->fetchAll($result);
            $this->db->freeResult($result);
            return $data;
        }

        return [];
    }

    public function getOrderItemsData($startDate, $endDate)
    {
        $query = "
        SELECT nama_produk, plu, SUM(qty_real) as real, SUM(qty_order) as order
        FROM (
            SELECT 
                p.prd_deskripsipendek as nama_produk,
                d.obi_prdcd as plu,
                d.obi_qtyrealisasi as qty_real,
                d.obi_qtyorder as qty_order
            FROM 
                tbtr_obi_h as h
            LEFT JOIN
                tbmaster_customer as c ON h.obi_kdmember = c.cus_kodemember 
            LEFT JOIN
                tbtr_obi_d as d ON h.obi_notrans = d.obi_notrans AND d.obi_tgltrans = h.obi_tgltrans  
            LEFT JOIN
                tbmaster_prodmast as p ON d.obi_prdcd = p.prd_prdcd
            LEFT JOIN 
                payment_klikigr as k ON h.obi_kdmember = k.kode_member
            WHERE h.obi_tglorder::date BETWEEN $1 AND $2
        ) as main
        GROUP BY nama_produk, plu, qty_order, qty_real
        ORDER BY qty_order DESC, qty_real DESC;
        ";

        $result = $this->db->query($query, [$startDate, $endDate]);
        if ($result) {
            $data = $this->db->fetchAll($result);
            $this->db->freeResult($result);
            return $data;
        }
        return [];
    }

    public function getAMT($startDate, $endDate)
    {
        $query = "
        SELECT
            h.obi_notrans as nomor,
            h.obi_nopb as notrx,
            h.obi_kdmember as kdmember,
            c.cus_namamember as namamember,
            h.obi_kdekspedisi,
            c.cus_kodeigr,
            h.obi_tglpb
        FROM tbtr_obi_h h
        LEFT JOIN tbmaster_customer c ON h.obi_kdmember = c.cus_kodemember
        WHERE h.obi_tglpb IS NOT NULL
        AND h.obi_tglpb::date BETWEEN $1 AND $2
        AND h.obi_kdekspedisi like 'Ambil%'
        ORDER BY h.obi_tglpb DESC
        ";

        $result = $this->db->query($query, [$startDate, $endDate]);
        if ($result) {
            $data = $this->db->fetchAll($result);
            $this->db->freeResult($result);
            return $data;
        }
        return [];
    }
}

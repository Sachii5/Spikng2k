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
        SELECT 
            p.prd_deskripsipendek as nama_produk,
            obi_prdcd as plu,
            sum(d.obi_qtyrealisasi) as real,
            sum(d.obi_qtyorder) as order
        FROM 
            tbtr_obi_d d
            LEFT JOIN
                tbtr_obi_h as h ON h.obi_notrans = d.obi_notrans AND d.obi_tgltrans = h.obi_tgltrans  
            LEFT JOIN
                tbmaster_prodmast as p ON d.obi_prdcd = p.prd_prdcd
            LEFT JOIN 
                payment_klikigr as k ON h.obi_kdmember = k.kode_member
            WHERE h.obi_tglorder::date BETWEEN $1 AND $2
        GROUP BY prd_deskripsipendek, obi_prdcd
        ORDER BY nama_produk
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
            CASE
                WHEN h.obi_recid = '6' THEN 'SELESAI'
                WHEN h.obi_recid = '1' THEN 'SIAP PICKING'
                WHEN h.obi_recid = '2' THEN 'SIAP SCANNING'
                WHEN h.obi_recid = '3' THEN 'SIAP DRAFT STRUK'
                WHEN h.obi_recid = '4' THEN 'PEMBAYARAN'
                WHEN h.obi_recid = '5' THEN 'SIAP STRUK'
                WHEN h.obi_recid LIKE 'B%' THEN 'BATAL'
                WHEN h.obi_recid IS NULL THEN 'SIAP SEND HANDHELD'
                ELSE 'LAINNYA'
            END AS status_pesanan,
            h.obi_nopb as notrx,
            h.obi_kdmember as kdmember,
            c.cus_namamember as namamember,
            h.obi_kdekspedisi,
            c.cus_kodeigr,
            h.obi_tglpb,
            h.obi_tgltrans
        FROM tbtr_obi_h h
        LEFT JOIN tbmaster_customer c ON h.obi_kdmember = c.cus_kodemember
        WHERE h.obi_tglpb IS NOT NULL
        AND h.obi_tglpb::date BETWEEN $1 AND $2
        AND h.obi_kdekspedisi like 'Ambil%'
        ORDER BY h.obi_notrans
        ";

        $result = $this->db->query($query, [$startDate, $endDate]);
        if ($result) {
            $data = $this->db->fetchAll($result);
            $this->db->freeResult($result);
            return $data;
        }
        return [];
    }

    public function getPBData($startDate, $endDate)
    {
        $query = "
        SELECT 
            obi_nopb as nopb,
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
            END AS status_pesanan,
            obi_tglpb as tgl, 
            obi_notrans as notrans,
            obi_kdmember as kode_member,
            obi_ekspedisi as ongkir,
            obi_tgltrans as tgltrans
        FROM TBTR_OBI_H
        WHERE obi_tglpb::date BETWEEN $1 AND $2
        ORDER BY obi_notrans
        ";

        $result = $this->db->query($query, [$startDate, $endDate]);
        if ($result) {
            $data = $this->db->fetchAll($result);
            $this->db->freeResult($result);
            return $data;
        }
        return [];
    }

    public function getDetailByNoTrans($noTrans, $tglTrans)
    {
        $query = "
        SELECT 
            d.obi_notrans as nomor,
            CASE
                WHEN h.obi_recid = '6' THEN 'SELESAI'
                WHEN h.obi_recid = '1' THEN 'SIAP PICKING'
                WHEN h.obi_recid = '2' THEN 'SIAP SCANNING'
                WHEN h.obi_recid = '3' THEN 'SIAP DRAFT STRUK'
                WHEN h.obi_recid = '4' THEN 'PEMBAYARAN'
                WHEN h.obi_recid = '5' THEN 'SIAP STRUK'
                WHEN h.obi_recid LIKE 'B%' THEN 'BATAL'
                WHEN h.obi_recid IS NULL THEN 'SIAP SEND HANDHELD'
                ELSE 'LAINNYA'
            END AS status_pesanan,
            h.obi_nopb as nopb,
            h.obi_tglpb as tgl,
            h.obi_kdmember as kode_member,
            cus_namamember as nama_member,
            prd_prdcd as plu,
            prd_deskripsipanjang as nama,
            d.obi_qtyorder as qty_order
        FROM tbtr_obi_d d
        LEFT JOIN tbtr_obi_h h ON h.obi_notrans = d.obi_notrans AND h.obi_tgltrans = d.obi_tgltrans
        LEFT JOIN tbmaster_customer ON cus_kodemember = h.obi_kdmember
        LEFT JOIN tbmaster_prodmast ON prd_prdcd = d.obi_prdcd
        WHERE d.obi_notrans = $1 AND d.obi_tgltrans::date = $2
        ORDER BY d.obi_prdcd
        ";

        $result = $this->db->query($query, [$noTrans, $tglTrans]);
        if ($result) {
            $data = $this->db->fetchAll($result);
            $this->db->freeResult($result);
            return $data;
        }
        return [];
    }
}

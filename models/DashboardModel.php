<?php
require_once 'config/database.php';

/**
 * Model untuk mengambil data dashboard dari database.
 * Berisi query penjualan, member, pesanan, dan margin.
 */
class DashboardModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->db->connect();
    }

    /** Mengambil total sales untuk tanggal tertentu. */
    public function getSalesToday($date)
    {
        $query = "
        SELECT 
            dtl_tanggal,
            TRUNC(SUM(dtl_netto)) AS gross_hari_ini
        FROM (
            SELECT 
                DATE_TRUNC('day', trjd_transactiondate) AS dtl_tanggal,
                CASE 
                    WHEN sls.trjd_transactiontype = 'S' THEN
                        CASE
                            WHEN sls.TRJD_FLAGTAX1 = 'Y' AND COALESCE(sls.TRJD_FLAGTAX2, 'z') IN ('Y', 'z')
                                AND sls.trjd_create_by NOT IN ('IDM','OMI','BKL')
                            THEN sls.TRJD_NOMINALAMT / 11.1 * 10
                            ELSE sls.TRJD_NOMINALAMT
                        END
                    ELSE
                        sls.TRJD_NOMINALAMT * -1
                END AS dtl_netto
            FROM (
                SELECT 
                    trjd_transactiontype,
                    trjd_create_by,
                    TRJD_FLAGTAX1,
                    TRJD_FLAGTAX2,
                    TRJD_NOMINALAMT,
                    trjd_transactiondate
                FROM TBTR_JUALDETAIL
                WHERE trjd_transactiondate::date = '$date'::date
                    AND trjd_recordid IS NULL
                    AND trjd_quantity <> 0
                
                UNION ALL
                
                SELECT 
                    trjd_transactiontype,
                    trjd_create_by,
                    TRJD_FLAGTAX1,
                    TRJD_FLAGTAX2,
                    TRJD_NOMINALAMT,
                    trjd_transactiondate
                FROM tbtr_jualdetail_interface
                WHERE trjd_transactiondate::date = '$date'::date
                    AND trjd_recordid IS NULL
                    AND trjd_quantity <> 0
            ) sls
        ) detailstruk
        GROUP BY dtl_tanggal
        HAVING SUM(dtl_netto) <> 0
        ORDER BY dtl_tanggal
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    /** Mengambil total sales untuk tanggal yang diberikan (kemarin dipasok dari controller). */
    public function getSalesYesterday($date)
    {
        $query = "
        SELECT
            TRUNC(SUM(dtl_netto)) AS gross_kemarin
        FROM (
            SELECT 
                DATE_TRUNC('day', trjd_transactiondate) AS dtl_tanggal,
                CASE 
                    WHEN sls.trjd_transactiontype = 'S' THEN
                        CASE
                            WHEN sls.TRJD_FLAGTAX1 = 'Y' AND COALESCE(sls.TRJD_FLAGTAX2, 'z') IN ('Y', 'z')
                                 AND sls.trjd_create_by NOT IN ('IDM','OMI','BKL')
                            THEN sls.TRJD_NOMINALAMT / 11.1 * 10
                            ELSE sls.TRJD_NOMINALAMT
                        END
                    ELSE
                        sls.TRJD_NOMINALAMT * -1
                END AS dtl_netto
            FROM (
                SELECT 
                    trjd_transactiontype,
                    trjd_create_by,
                    TRJD_FLAGTAX1,
                    TRJD_FLAGTAX2,
                    TRJD_NOMINALAMT,
                    trjd_transactiondate
                FROM TBTR_JUALDETAIL
                WHERE trjd_transactiondate::date = '$date'::date
                    AND trjd_recordid IS NULL
                    AND trjd_quantity <> 0
                
                UNION ALL
                
                SELECT 
                    trjd_transactiontype,
                    trjd_create_by,
                    TRJD_FLAGTAX1,
                    TRJD_FLAGTAX2,
                    TRJD_NOMINALAMT,
                    trjd_transactiondate
                FROM tbtr_jualdetail_interface
                WHERE trjd_transactiondate::date = '$date'::date
                    AND trjd_recordid IS NULL
                    AND trjd_quantity <> 0
            ) sls
        ) detailstruk
        GROUP BY dtl_tanggal
        HAVING SUM(dtl_netto) <> 0
        ORDER BY dtl_tanggal
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    /**
     * Menghitung jumlah member unik yang berbelanja di bulan berjalan.
     * Parameter $date tidak digunakan untuk query ini, tapi tetap disertakan untuk konsistensi.
     */
    public function getMembers($date)
    {
        $query = "
        SELECT COUNT(DISTINCT TRJD_CUS_KODEMEMBER) AS member_aktif 
        FROM TBTR_JUALDETAIL 
        LEFT JOIN TBMASTER_CUSTOMER ON TRJD_CUS_KODEMEMBER = CUS_KODEMEMBER
        WHERE CUS_FLAGMEMBERKHUSUS = 'Y' 
        AND DATE(TRJD_TRANSACTIONDATE) >= DATE_TRUNC('month', CURRENT_DATE)
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    /**
     * Menghitung jumlah member unik yang berbelanja di bulan lalu.
     * Parameter $date dipakai untuk menentukan bulan pembanding.
     */
    public function getMembersYesterday($date)
    {
        $query = "
        SELECT COUNT(DISTINCT TRJD_CUS_KODEMEMBER) AS member_aktif 
        FROM TBTR_JUALDETAIL 
        LEFT JOIN TBMASTER_CUSTOMER ON TRJD_CUS_KODEMEMBER = CUS_KODEMEMBER
        WHERE CUS_FLAGMEMBERKHUSUS = 'Y' 
        AND DATE(TRJD_TRANSACTIONDATE) >= DATE_TRUNC('month', '$date'::date)
        AND DATE(TRJD_TRANSACTIONDATE) < DATE_TRUNC('month', '$date'::date) + INTERVAL '1 month'
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    /** Jumlah registrasi member pada rentang bulan berjalan. */
    public function getNewRegistrationsCurrentMonth($startDate, $endDate)
    {
        $query = "
        SELECT 
            COUNT(DISTINCT cus_kodemember) AS jml_mem
        FROM tbmaster_customer 
        INNER JOIN tbmaster_customercrm ON cus_kodemember = crm_kodemember
        WHERE 
            cus_kodeigr = '2K' 
            AND crm_kodeigr = '2K' 
            AND cus_namamember <> 'NEW'
            AND CUS_TGLREGISTRASI::DATE BETWEEN '$startDate' AND '$endDate'
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    /** Jumlah registrasi member pada rentang bulan lalu. */
    public function getNewRegistrationsLastMonth($startDate, $endDate)
    {
        $query = "
        SELECT 
            COUNT(DISTINCT cus_kodemember) AS jml_mem
        FROM tbmaster_customer 
        INNER JOIN tbmaster_customercrm ON cus_kodemember = crm_kodemember
        WHERE 
            cus_kodeigr = '2K' 
            AND crm_kodeigr = '2K' 
            AND cus_namamember <> 'NEW'
            AND CUS_TGLREGISTRASI::DATE BETWEEN '$startDate' AND '$endDate'
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    /** Total pesanan harian antara awal bulan lalu s.d. akhir bulan ini (untuk perbandingan chart). */
    public function getDailyOrders($lastMonthStart, $currentMonthEnd)
    {
        $query = "
        SELECT 
            TO_CHAR(obi_tglorder, 'YYYY-MM-DD') AS tanggal,
            TO_CHAR(obi_tglorder, 'DD') AS hari,
            COUNT(*) AS total_pesanan,
            EXTRACT(MONTH FROM obi_tglorder) AS bulan,
            EXTRACT(YEAR FROM obi_tglorder) AS tahun
        FROM tbtr_obi_h 
        WHERE obi_tglorder::date BETWEEN '$lastMonthStart' AND '$currentMonthEnd'
        GROUP BY TO_CHAR(obi_tglorder, 'YYYY-MM-DD'), TO_CHAR(obi_tglorder, 'DD'), 
                EXTRACT(MONTH FROM obi_tglorder), EXTRACT(YEAR FROM obi_tglorder)
        ORDER BY tanggal
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetchAll($result);
        $this->db->freeResult($result);
        return $data;
    }

    /** Jumlah pesanan pada tanggal tertentu. */
    public function getOrdersToday($date)
    {
        $query = "
        SELECT COUNT(*) as total_pb 
        FROM tbtr_obi_h 
        WHERE obi_tglorder::date = '$date'::date
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    /** Jumlah pesanan pada tanggal pembanding (dipasok controller). */
    public function getOrdersYesterday($date)
    {
        $query = "
        SELECT COUNT(*) as total_pb 
        FROM tbtr_obi_h 
        WHERE obi_tglorder::date = '$date'::date
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    /** Total margin pada tanggal tertentu. */
    public function getMarginToday($date)
    {
        $query = "
        SELECT CAST(SUM(dtl_margin) AS NUMERIC(15,2)) AS margin_hari_ini
        FROM (
            SELECT 
                CASE
                    WHEN dtl_rtype = 'S' THEN dtl_netto - dtl_hpp
                    ELSE (dtl_netto - dtl_hpp) * -1
                END AS dtl_margin
            FROM (
                SELECT 
                    trjd_transactiontype as dtl_rtype,
                    -- dtl_netto calculation
                    CASE
                        WHEN trjd_divisioncode = '5' AND SUBSTR(trjd_division, 1, 2) = '39' THEN
                            CASE WHEN 'Y' = 'Y' THEN trjd_nominalamt END
                        ELSE
                            CASE
                                WHEN COALESCE(tko_kodesbu, 'z') IN ('O', 'I') THEN
                                    CASE
                                        WHEN tko_tipeomi IN ('HE', 'HG') THEN
                                            trjd_nominalamt - (
                                                CASE
                                                    WHEN trjd_flagtax2 = 'Y' AND COALESCE(trjd_flagtax2, 'z') IN ('Y', 'z') AND COALESCE(prd_kodetag, 'zz') <> 'Q' THEN
                                                        (trjd_nominalamt - (trjd_nominalamt / (1 + (COALESCE(prd_ppn, 10) / 100))))
                                                    ELSE 0
                                                END
                                            )
                                        ELSE trjd_nominalamt
                                    END
                                ELSE
                                    trjd_nominalamt - (
                                        CASE
                                            WHEN SUBSTR(trjd_create_by, 1, 2) = 'EX' THEN 0
                                            ELSE
                                                CASE
                                                    WHEN trjd_flagtax2 = 'Y' AND COALESCE(trjd_flagtax2, 'z') IN ('Y', 'z') AND COALESCE(prd_kodetag, 'zz') <> 'Q' THEN
                                                        (trjd_nominalamt - (trjd_nominalamt / (1 + (COALESCE(prd_ppn, 10) / 100))))
                                                    ELSE 0
                                                END
                                        END
                                    )
                            END
                    END AS dtl_netto,
                    -- dtl_hpp calculation
                    CASE
                        WHEN trjd_divisioncode = '5' AND SUBSTR(trjd_division, 1, 2) = '39' THEN
                            CASE WHEN 'Y' = 'Y' THEN
                                trjd_nominalamt - (
                                    CASE
                                        WHEN prd_markupstandard IS NULL THEN (5 * trjd_nominalamt) / 100
                                        ELSE (prd_markupstandard * trjd_nominalamt) / 100
                                    END
                                )
                            END
                        ELSE
                            (trjd_quantity / CASE WHEN prd_unit = 'KG' THEN 1000 ELSE 1 END) * trjd_baseprice
                    END AS dtl_hpp
                FROM tbtr_jualdetail
                LEFT JOIN tbmaster_prodmast ON trjd_prdcd = prd_prdcd
                LEFT JOIN tbmaster_tokoigr ON trjd_cus_kodemember = tko_kodecustomer
                WHERE trjd_transactiondate::date = '$date'::date
            ) base
        ) calculated
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    /** Total margin pada tanggal pembanding (dipasok controller). */
    public function getMarginYesterday($date)
    {
        $query = "
        SELECT CAST(SUM(dtl_margin) AS NUMERIC(15,2)) AS margin_kemarin
        FROM (
            SELECT 
                CASE
                    WHEN dtl_rtype = 'S' THEN dtl_netto - dtl_hpp
                    ELSE (dtl_netto - dtl_hpp) * -1
                END AS dtl_margin
            FROM (
                SELECT 
                    trjd_transactiontype as dtl_rtype,
                    -- dtl_netto calculation
                    CASE
                        WHEN trjd_divisioncode = '5' AND SUBSTR(trjd_division, 1, 2) = '39' THEN
                            CASE WHEN 'Y' = 'Y' THEN trjd_nominalamt END
                        ELSE
                            CASE
                                WHEN COALESCE(tko_kodesbu, 'z') IN ('O', 'I') THEN
                                    CASE
                                        WHEN tko_tipeomi IN ('HE', 'HG') THEN
                                            trjd_nominalamt - (
                                                CASE
                                                    WHEN trjd_flagtax2 = 'Y' AND COALESCE(trjd_flagtax2, 'z') IN ('Y', 'z') AND COALESCE(prd_kodetag, 'zz') <> 'Q' THEN
                                                        (trjd_nominalamt - (trjd_nominalamt / (1 + (COALESCE(prd_ppn, 10) / 100))))
                                                    ELSE 0
                                                END
                                            )
                                        ELSE trjd_nominalamt
                                    END
                                ELSE
                                    trjd_nominalamt - (
                                        CASE
                                            WHEN SUBSTR(trjd_create_by, 1, 2) = 'EX' THEN 0
                                            ELSE
                                                CASE
                                                    WHEN trjd_flagtax2 = 'Y' AND COALESCE(trjd_flagtax2, 'z') IN ('Y', 'z') AND COALESCE(prd_kodetag, 'zz') <> 'Q' THEN
                                                        (trjd_nominalamt - (trjd_nominalamt / (1 + (COALESCE(prd_ppn, 10) / 100))))
                                                    ELSE 0
                                                END
                                        END
                                    )
                            END
                    END AS dtl_netto,
                    -- dtl_hpp calculation
                    CASE
                        WHEN trjd_divisioncode = '5' AND SUBSTR(trjd_division, 1, 2) = '39' THEN
                            CASE WHEN 'Y' = 'Y' THEN
                                trjd_nominalamt - (
                                    CASE
                                        WHEN prd_markupstandard IS NULL THEN (5 * trjd_nominalamt) / 100
                                        ELSE (prd_markupstandard * trjd_nominalamt) / 100
                                    END
                                )
                            END
                        ELSE
                            (trjd_quantity / CASE WHEN prd_unit = 'KG' THEN 1000 ELSE 1 END) * trjd_baseprice
                    END AS dtl_hpp
                FROM tbtr_jualdetail
                LEFT JOIN tbmaster_prodmast ON trjd_prdcd = prd_prdcd
                LEFT JOIN tbmaster_tokoigr ON trjd_cus_kodemember = tko_kodecustomer
                WHERE trjd_transactiondate::date = '$date'::date
            ) base
        ) calculated
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    // public function __destruct()
    // {
    //     $this->db->close();
    // }
}

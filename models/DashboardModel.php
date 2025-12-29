<?php
require_once 'config/database.php';

/**
 * Model untuk mengambil data dashboard dengan dukungan filter periode
 */
class DashboardModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->db->connect();
    }

    /** 
     * Mengambil total sales untuk periode (range tanggal)
     */
    public function getSalesForPeriod($startDate, $endDate)
    {
        $query = "
        SELECT 
            TRUNC(SUM(dtl_netto)) AS gross_periode
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
                WHERE trjd_transactiondate::date BETWEEN '$startDate'::date AND '$endDate'::date
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
                WHERE trjd_transactiondate::date BETWEEN '$startDate'::date AND '$endDate'::date
                    AND trjd_recordid IS NULL
                    AND trjd_quantity <> 0
            ) sls
        ) detailstruk
        HAVING SUM(dtl_netto) <> 0
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    /** 
     * Mengambil total sales untuk tanggal tertentu (backward compatibility)
     */
    public function getSalesToday($date)
    {
        return $this->getSalesForPeriod($date, $date);
    }

    public function getSalesYesterday($date)
    {
        return $this->getSalesForPeriod($date, $date);
    }

    /**
     * Menghitung jumlah member unik yang berbelanja dalam periode
     */
    public function getMembersForPeriod($startDate, $endDate)
    {
        $query = "
        SELECT COUNT(DISTINCT TRJD_CUS_KODEMEMBER) AS member_aktif 
        FROM TBTR_JUALDETAIL 
        LEFT JOIN TBMASTER_CUSTOMER ON TRJD_CUS_KODEMEMBER = CUS_KODEMEMBER
        WHERE CUS_FLAGMEMBERKHUSUS = 'Y' 
        AND DATE(TRJD_TRANSACTIONDATE) BETWEEN '$startDate'::date AND '$endDate'::date
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    /** Backward compatibility */
    public function getMembers($date)
    {
        $firstDay = date('Y-m-01', strtotime($date));
        return $this->getMembersForPeriod($firstDay, $date);
    }

    public function getMembersYesterday($date)
    {
        $firstDay = date('Y-m-01', strtotime($date));
        $lastDay = date('Y-m-t', strtotime($date));
        return $this->getMembersForPeriod($firstDay, $lastDay);
    }

    /** 
     * Jumlah pesanan dalam periode
     */
    public function getOrdersForPeriod($startDate, $endDate)
    {
        $query = "
        SELECT COUNT(*) as total_pb 
        FROM tbtr_obi_h 
        WHERE obi_tglorder::date BETWEEN '$startDate'::date AND '$endDate'::date
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    /** Backward compatibility */
    public function getOrdersToday($date)
    {
        return $this->getOrdersForPeriod($date, $date);
    }

    public function getOrdersYesterday($date)
    {
        return $this->getOrdersForPeriod($date, $date);
    }

    /**
     * Total pesanan harian untuk periode (untuk chart)
     */
    public function getDailyOrdersForPeriod($startDate, $endDate)
    {
        $query = "
        SELECT 
            TO_CHAR(obi_tglorder, 'YYYY-MM-DD') AS tanggal,
            TO_CHAR(obi_tglorder, 'DD') AS hari,
            COUNT(*) AS total_pesanan,
            EXTRACT(MONTH FROM obi_tglorder) AS bulan,
            EXTRACT(YEAR FROM obi_tglorder) AS tahun
        FROM tbtr_obi_h 
        WHERE obi_tglorder::date BETWEEN '$startDate'::date AND '$endDate'::date
        GROUP BY TO_CHAR(obi_tglorder, 'YYYY-MM-DD'), TO_CHAR(obi_tglorder, 'DD'), 
                EXTRACT(MONTH FROM obi_tglorder), EXTRACT(YEAR FROM obi_tglorder)
        ORDER BY tanggal
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetchAll($result);
        $this->db->freeResult($result);
        return $data;
    }

    /** Backward compatibility */
    public function getDailyOrders($lastMonthStart, $currentMonthEnd)
    {
        return $this->getDailyOrdersForPeriod($lastMonthStart, $currentMonthEnd);
    }

    /**
     * Total margin dalam periode
     */
    public function getMarginForPeriod($startDate, $endDate)
    {
        $query = "
        SELECT CAST(SUM(dtl_margin) AS NUMERIC(15,2)) AS margin_periode
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
                WHERE trjd_transactiondate::date BETWEEN '$startDate'::date AND '$endDate'::date
            ) base
        ) calculated
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    /** Backward compatibility */
    public function getMarginToday($date)
    {
        return $this->getMarginForPeriod($date, $date);
    }

    public function getMarginYesterday($date)
    {
        return $this->getMarginForPeriod($date, $date);
    }

    /**
     * Total ongkir dalam periode
     */
    public function getOngkirForPeriod($startDate, $endDate)
    {
        $query = "
        WITH ranked AS (
    SELECT
        pot_ongkir,
        ongkir,
        sti_penerima,
        sti_tglserahterima,
        sti_vehicleno,
        sti_driverid,
        sti_driverphone,
        ROW_NUMBER() OVER (
            PARTITION BY
                sti_penerima,
                sti_tglserahterima,
                sti_vehicleno,
                sti_driverid,
                sti_driverphone
            ORDER BY
                awi_cost DESC
        ) AS rn
    FROM tbtr_awb_ipp
    LEFT JOIN tbtr_serahterima_ipp
        ON awi_noawb = sti_noawb
    LEFT JOIN payment_klikigr
        ON awi_nopb = no_pb
    LEFT JOIN tbtr_obi_h
        ON awi_nopb = obi_nopb
    WHERE tgl_trans::date BETWEEN '$startDate'::date AND '$endDate'::date
        AND obi_recid = '6'
)
SELECT
    SUM(pot_ongkir) AS pot_ongkir,
    COUNT(CASE WHEN ongkir <> 0 THEN 1 END) as pb_ongkir 
FROM ranked
WHERE rn = 1
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    /** Backward compatibility */
    public function getOngkir($date)
    {
        return $this->getOngkirForPeriod($date, $date);
    }

    /** Registrasi member (tetap menggunakan range) */
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
            AND CUS_TGLREGISTRASI::DATE BETWEEN '$startDate'::date AND '$endDate'::date
        ";

        $result = $this->db->query($query);
        $data = $this->db->fetch($result);
        $this->db->freeResult($result);
        return $data;
    }

    public function getNewRegistrationsLastMonth($startDate, $endDate)
    {
        return $this->getNewRegistrationsCurrentMonth($startDate, $endDate);
    }
}

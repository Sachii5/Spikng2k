<?php
class Database
{
    private $host = "172.31.147.183";
    private $port = "5432";
    private $dbname = "spibdg2k";
    private $user = "edp";
    private $password = "3dp1grVIEW";
    private $connection;
    private $isConnected = false;

    public function connect()
    {
        if (!$this->isConnected) {
            $conn_string = "host={$this->host} port={$this->port} dbname={$this->dbname} user={$this->user} password={$this->password}";
            // var_dump($conn_string);
            // die;
            $this->connection = pg_connect($conn_string);

            if (!$this->connection) {
                die("Connection failed");
            }

            $this->isConnected = true;
        }

        return $this->connection;
    }

    public function query($sql, $params = [])
    {
        if (!$this->isConnected) {
            $this->connect();
        }

        if (empty($params)) {
            $result = pg_query($this->connection, $sql);
        } else {
            $result = pg_query_params($this->connection, $sql, $params);
        }
        return $result;
    }

    public function fetch($result)
    {
        return pg_fetch_assoc($result);
    }

    public function fetchAll($result)
    {
        $data = [];
        while ($row = pg_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public function freeResult($result)
    {
        if ($result) {
            pg_free_result($result);
        }
    }

    public function close()
    {
        if ($this->isConnected && $this->connection) {
            pg_close($this->connection);
            $this->isConnected = false;
            $this->connection = null;
        }
    }
}
<?php namespace SimpleProductLinks;

/**
 * Class Model
 * @package MultiCategories
 */


class Model {
    /** @var \DocumentParser $modx */
    protected $modx = null;
    protected $table = 'product_links';

    /**
     * Model constructor.
     * @param \DocumentParser $modx
     */
    public function __construct(\DocumentParser $modx)
    {
        $this->modx = $modx;
        $this->table = $modx->getFullTableName($this->table);
    }

    public function createTable() {
        $sql = <<< OUT
CREATE TABLE IF NOT EXISTS {$this->table} (
  `master` int(10) UNSIGNED NOT NULL,
  `slave` int(10) UNSIGNED NOT NULL,
  UNIQUE KEY `link` (`master`,`slave`) USING BTREE
) ENGINE=MyISAM;
OUT;
        $this->modx->db->query($sql);
    }

    public function addRelation($master, $slave)
    {
        $master = $this->modx->db->escape($master);
        $slave = $this->modx->db->escape($slave);
        $sql = "
                INSERT INTO {$this->table} ( master, slave)
                VALUES ('$master', '$slave')
                ON DUPLICATE KEY UPDATE  master = '$master', slave = '$slave';
            ";
        $this->modx->db->query($sql);
    }

    public function removeRelation($master)
    {
        $master = $this->modx->db->escape($master);
        $this->modx->db->delete($this->table,"master = $master or slave = $master");
    }

    public function getFullRelations($master, $slave)
    {
        $master = $this->modx->db->escape($master);
        $slave = $this->modx->db->escape($slave);

        $sql = "select slave from $this->table where master in ($master,$slave)";
        return $this->modx->db->getColumn('slave',$this->modx->db->query($sql));
    }

    public function getRelatedItems($master)
    {
        $master = $this->modx->db->escape($master);

        $sql = "select slave from $this->table where master = $master ";
        return $this->modx->db->getColumn('slave',$this->modx->db->query($sql));
    }


}

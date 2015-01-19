<?php

namespace Externals\DB_Email_Templates;

require_once dirname(__FILE__) . '/model.php';

use Externals\DB_Email_Templates\Model as EmailTemplate;
use PDO;
use PDOStatement;

class DataAdapter
{
    /**
     * @var PDO
     */
    private $pdo;

    private $db_table_name;

    private $db_category_table_name;

    public function __construct(PDO $pdo = null, $db_table_name = 'email_template', $db_category_table_name = 'email_template_category')
    {
        $this->setPDO($pdo);
        $this->setDbTableName($db_table_name);
        $this->setDbCategoryTableName($db_category_table_name);
    }

    public function assertDBIsOK() {

        $oldMode = $this->getPDO()->getAttribute(PDO::ATTR_ERRMODE);
        $this->getPDO()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Following statements will throw exception if something's missing
        $assertion1 = $this->getPDO()->query("SELECT * FROM {$this->getDbTableName()} LIMIT 1");
        $assertion2 = $this->getPDO()->query("SELECT * FROM {$this->getDbCategoryTableName()} LIMIT 1");

        $this->getPDO()->setAttribute(PDO::ATTR_ERRMODE, $oldMode);
    }

    /**
     * @param $id
     * @return bool|EmailTemplate
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->getDbTableName()} WHERE id = :id LIMIT 1";

        $query = $this->getPDO()->prepare($sql);

        $query->bindValue('id', $id);

        return $this->getOneRow($query);
    }

    public function getRandomByCategory($category_id)
    {
        $sql = "SELECT * FROM {$this->getDbTableName()} WHERE is_active = 1 AND category_id = :category_id ORDER BY dt_last_used ASC";

        $query = $this->getPDO()->prepare($sql);

        $query->bindValue('category_id', $category_id);

        return $this->getOneRow($query);
    }

    public function touch($id)
    {
        $id = (int) $id;

        return $this->getPDO()->exec("UPDATE {$this->getDbTableName()} SET dt_last_used = NOW() WHERE id = $id");
    }

    /**
     * @param PDO $pdo
     */
    public function setPDO($pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return PDO
     */
    public function getPDO()
    {
        return $this->pdo;
    }

    /**
     * @param mixed $db_table_name
     */
    public function setDbTableName($db_table_name = null)
    {
        $this->db_table_name = $db_table_name;
    }

    /**
     * @return mixed
     */
    public function getDbTableName()
    {
        return $this->db_table_name;
    }

    /**
     * @param mixed $db_category_table_name
     */
    public function setDbCategoryTableName($db_category_table_name)
    {
        $this->db_category_table_name = $db_category_table_name;
    }

    /**
     * @return mixed
     */
    public function getDbCategoryTableName()
    {
        return $this->db_category_table_name;
    }

    private function getOneRow(PDOStatement $query)
    {
        if ($query->execute() && ($row = $query->fetch(PDO::FETCH_ASSOC))) {
            $obj = $this->createObject($row);
            $obj->setId($row['id']);
            return $obj;
        } else {
            return false;
        }
    }

    private function createObject($row)
    {
        $template = new EmailTemplate();
        $template->exchange($row);
        return $template;
    }

}
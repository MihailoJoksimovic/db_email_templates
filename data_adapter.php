<?php

namespace Externals\DB_Email_Templates;

require_once dirname(__FILE__) . '/model.php';
require_once dirname(__FILE__) . '/db_email_template_da_interface.php';

use Externals\DB_Email_Templates\Model as EmailTemplate;
use Externals\DB_Email_Templates\DbEmailTemplateDa as DAInterface;
use PDO;
use PDOStatement;

class DataAdapter implements DAInterface
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

    public function getAllTemplatesByCategorySlug($category_slug, $only_active = false)
    {
        $category_id = $this->getCategoryIdBySlug($category_slug);

        $sql = "SELECT * FROM {$this->getDbTableName()} WHERE category_id = :category_id";

        if ($only_active) {
            $sql .= " AND is_active = 1 ";
        }

        $query = $this->getPDO()->prepare($sql);

        $query->bindValue('category_id', $category_id);

        $query->execute();

        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return array();
        }

        $objs = array();

        foreach ($rows as $row) {
            $obj = $this->createObject($row);

            $objs[] = $obj;
        }

        return $objs;
    }

    public function getActiveTemplatesByCategorySlug($category_slug)
    {
        return $this->getAllTemplatesByCategorySlug($category_slug, $only_active = true);
    }

    public function touch($id)
    {
        $id = (int) $id;

        return $this->getPDO()->exec("UPDATE {$this->getDbTableName()} SET dt_last_used = NOW() WHERE id = $id");
    }

    public function getCategoryIdBySlug($slug)
    {
        $sql = "SELECT id FROM {$this->getDbCategoryTableName()} WHERE slug = :slug LIMIT 1";

        $query = $this->getPDO()->prepare($sql);

        $query->bindValue('slug', $slug);

        $query->execute();

        return $query->fetchColumn();
    }

    public function getAllCategories()
    {
        $sql = "SELECT * FROM {$this->getDbCategoryTableName()}";

        return $this->getPDO()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addCategory(array $category)
    {
        $sql = "INSERT INTO {$this->getDbCategoryTableName()} SET name = :name, slug = :slug";

        $query = $this->getPDO()->prepare($sql);

        return $query->execute(array(
            ':name' => $category['name'],
            ':slug' => $category['slug'],
        ));
    }

    public function updateCategory(array $category)
    {
        $sql = "UPDATE {$this->getDbCategoryTableName()} SET name = :name, slug = :slug WHERE id = :id";

        $query = $this->getPDO()->prepare($sql);

        return $query->execute(array(
            ':id'   => $category['id'],
            ':name' => $category['name'],
            ':slug' => $category['slug'],
        ));
    }

    public function removeCategory($id)
    {
        $sql = "DELETE FROM {$this->getDbCategoryTableName()} WHERE id = :id";

        $query = $this->getPDO()->prepare($sql);

        return $query->execute(array(
            ':id'   => $id
        ));
    }

    public function addTemplate(array $template)
    {
        $sql = <<<SQL
INSERT INTO {$this->getDbTableName()}
SET
  template_name = :name,
  subject = :subject,
  body = :body,
  is_active = :is_active,
  category_id = :category_id
SQL;

        $query = $this->getPDO()->prepare($sql);

        return $query->execute(array(
            ':name'         => $template['template_name'],
            ':subject'      => $template['subject'],
            ':body'         => $template['body'],
            ':is_active'    => $template['is_active'],
            ':category_id'  => $template['category_id'],
        ));
    }

    public function updateTemplate(array $template)
    {
        $sql = <<<SQL
UPDATE  {$this->getDbTableName()}
SET
  template_name = :name,
  subject = :subject,
  body = :body,
  is_active = :is_active,
  category_id = :category_id
WHERE id = :id
SQL;

        $query = $this->getPDO()->prepare($sql);

        return $query->execute(array(
            ':id'           => $template['id'],
            ':name'         => $template['template_name'],
            ':subject'      => $template['subject'],
            ':body'         => $template['body'],
            ':is_active'    => $template['is_active'],
            ':category_id'  => $template['category_id'],
        ));
    }

    public function removeTemplate($id)
    {
        $sql = "DELETE FROM {$this->getDbTableName()} WHERE id = :id";

        $query = $this->getPDO()->prepare($sql);

        return $query->execute(array(
            ':id'   => $id
        ));
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

        if (!empty($row['id'])) {
            $template->setId($row['id']);
        }

        return $template;
    }

}
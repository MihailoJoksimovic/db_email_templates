<?php

namespace Externals\DB_Email_Templates;

interface DbEmailTemplateDa
{
    public function assertDBIsOK();

    public function getById($id);

    public function getRandomByCategory($category_id);

    public function touch($id);

    public function addTemplate(array $template);

    public function updateTemplate(array $template);

    public function removeTemplate($id);

    public function getCategoryIdBySlug($slug);

    public function getAllTemplatesByCategorySlug($category_slug, $only_active = false);

    public function getActiveTemplatesByCategorySlug($category_slug);

    public function getAllCategories();

    public function addCategory(array $category);

    public function updateCategory(array $category);

    public function removeCategory($id);

}


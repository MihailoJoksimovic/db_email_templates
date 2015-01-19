<?php

namespace Externals\DB_Email_Templates;

interface DbEmailTemplateDa
{
    public function assertDBIsOK();

    public function getById($id);

    public function getRandomByCategory($category_id);

    public function touch($id);
}


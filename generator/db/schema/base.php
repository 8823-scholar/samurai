<?php
class Db_Schema_Base extends ActiveGateway_Schema
{
    public function define($version = 20130114101010)
    {
        $this->setVersion($version);

        $this->createTable('simple_case')
            ->column('name')->type('string', 128)->notNull()->comment('name')
            ->column('age')->type('int', 3)->comment('age');

        $this->createIndex('simple_case', 'name');


        $this->createTable('complexity_case')
            ->column('name')->type('string', 128)->comment('name')
            ->column('mail')->type('string', 128)->comment('mail')
            ->column('age')->type('int', 3)->comment('age')
            ->column('gender')->type('list', array('male', 'female'))->defaultValue('male')->comment('gender')
            ->column('created_at')->type('int', 11)
            ->column('updated_at')->type('int', 11)
            ->column('deleted_at')->type('int', 11)->enableNull()
            ->column('active')->type('list', array('1', '2'))->defaultValue(1)
            ->engine('InnoDB')->charset('utf8')->collate('utf8_general_ci')->comment('complexity_case');

        $this->createIndex('complexity_case', array('name', 'mail'));
        $this->createIndex('complexity_case', 'age')->append('gender')->setName('index_hoge');
        $this->createUnique('complexity_case', 'mail');
    }
}


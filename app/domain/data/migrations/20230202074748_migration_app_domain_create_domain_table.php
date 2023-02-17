<?php
use think\migration\Migrator;

class MigrationAppDomainCreateDomainTable extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('domain', ['signed' => false, 'comment' => '主域名表'])
            ->addColumn('domain', 'string', ['comment' => '主域名'])
            ->addColumn('type', 'integer', ['comment' => '运营商类型 1阿里云 2腾讯云'])
            ->addColumn('domain_id', 'string', ['comment' => '运营商处域名id'])
            ->create();
    }
}

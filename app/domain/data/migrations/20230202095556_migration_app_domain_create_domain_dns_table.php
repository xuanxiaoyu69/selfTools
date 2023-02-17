<?php
use think\migration\Migrator;

class MigrationAppDomainCreateDomainDnsTable extends Migrator
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
        $this->table('domain_dns')
            ->addColumn('domain_id', 'integer')
            ->addColumn('dns_id', 'string')
            ->addColumn('sub_domain', 'string')
            ->addColumn('type', 'string')
            ->addColumn('value', 'string')
            ->addColumn('ttl', 'integer')
            ->addColumn('status', 'integer')
            ->addColumn('remark', 'string', ['null' => true])
            ->create();
    }
}

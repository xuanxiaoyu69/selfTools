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
        $this->table('domain_dns', ['signed' => false, 'comment' => '子域名表'])
            ->addColumn('domain_id', 'integer', ['comment' => '主域名表id'])
            ->addColumn('dns_id', 'string', ['comment' => '运营商处子域名id'])
            ->addColumn('sub_domain', 'string', ['comment' => '子域名'])
            ->addColumn('type', 'string', ['comment' => '解析类型'])
            ->addColumn('value', 'string', ['comment' => '解析值'])
            ->addColumn('ttl', 'integer')
            ->addColumn('status', 'integer', ['comment' => '解析状态'])
            ->addColumn('remark', 'string', ['null' => true])
            ->create();
    }
}

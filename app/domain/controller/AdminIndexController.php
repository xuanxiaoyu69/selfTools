<?php

namespace app\domain\controller;

use app\domain\model\DomainDnsModel;
use app\domain\model\DomainModel;
use app\domain\service\AliService;
use app\domain\service\TxyService;
use cmf\controller\AdminBaseController;
use think\db\exception\DbException;

/**
 * Class AdminIndexController
 * @adminMenuRoot(
 *     'name' => '域名管理',
 *     'action' => 'default',
 *     'parent' => '',
 *     'display' => true,
 *     'order' => 10000,
 *     'icon' => 'internet-explorer',
 *     'remark' => ''
 * )
 */
class AdminIndexController extends AdminBaseController
{
    /**
     * 阿里云域名
     * @adminMenu(
     *     'name' => '阿里云域名',
     *     'parent' => 'default',
     *     'display' => true,
     *     'hasView' => true,
     *     'order' => 10000,
     *     'icon' => '',
     *     'remark' => '',
     *     'param' => ''
     * )
     */
    public function ali_index()
    {
        $list = DomainModel::where('type', 1)->paginate(10);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function ali_dns()
    {
        $data = input();
        $info = DomainModel::where('id', $data['id'])->find();
        $list = DomainDnsModel::where('domain_id', $data['id'])->order('id desc')->paginate(10);

        $list->appends($data);
        $this->assign('info', $info);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function ali_dns_update()
    {
        $id = input('id');
        $domain = DomainModel::where('id', $id)->value('domain');
        $request_params = [
            'domainName' => $domain,
            'pageNumber' => 1,
            'pageSize' => 10
        ];

        $ali = new AliService();
        $list = $ali->getDomainDnsList([], $request_params);
        $list = array_reverse($list);
        $new_ids = [];
        foreach ($list as $k => $v) {
            $new_ids[] = $v['recordId'];
        }

        DomainDnsModel::startTrans();

        try {
            $data_ids_str = DomainDnsModel::where('domain_id', $id)->value('group_concat(dns_id) ids');
            $data_ids = !empty($data_ids_str) ? explode(',', $data_ids_str) : [];

            // 新增的差集
            $left_diff = array_diff($new_ids, $data_ids);
            // 删除的差集
            $right_diff = array_diff($data_ids, $new_ids);
            // 更新的交集
            $center_diff = array_intersect($new_ids, $data_ids);

            foreach ($list as $k => $v) {
                $data = [
                    'domain_id' => $id,
                    'dns_id' => $v['recordId'],
                    'sub_domain' => $v['RR'],
                    'type' => $v['type'],
                    'value' => $v['value'],
                    'ttl' => $v['TTL'],
                    'status' => $v['status'] === 'ENABLE' ? 1 : 0,
                    'remark' => $v['remark']
                ];

                if(in_array($v['recordId'], $left_diff)){
                    DomainDnsModel::insert($data);
                }

                if(in_array($v['recordId'], $center_diff)){
                    DomainDnsModel::where('dns_id', $v['recordId'])->update($data);
                }
            }

            $right_diff_ids = implode(',', $right_diff);
            DomainDnsModel::where('dns_id', 'in', $right_diff_ids)->delete();

            DomainDnsModel::commit();
            $this->success('更新完成');
        }catch(DbException $e){
            DomainDnsModel::rollback();
            $this->error('更新完成');
        }
    }

    public function ali_dns_add()
    {
        $domain_id = input('domain_id');
        $info = DomainModel::where('id', $domain_id)->find();
        $this->assign('info', $info);
        return $this->fetch();
    }

    public function ali_dns_add_post()
    {
        $data = input();
        $info = DomainModel::where('id', $data['domain_id'])->find();
        $request_params = [
            'domainName' => $info['domain'],
            'RR' => $data['sub_domain'],
            'type' => $data['type'],
            'value' => $data['value'],
            'TTL' => (int) $data['ttl'],
        ];

        $ali = new AliService();
        $res = $ali->addDomainRecord($request_params);

        if($res){
            $data['dns_id'] = $res;
            $data['status'] = 1;
            DomainDnsModel::insert($data);
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }

    public function ali_dns_edit()
    {
        $id = input('id');
        $domain_info = DomainDnsModel::where('id', $id)->find();
        $info = DomainModel::where('id', $domain_info['domain_id'])->find();

        $this->assign('domain_info', $domain_info);
        $this->assign('info', $info);
        return $this->fetch();
    }

    public function ali_dns_edit_post()
    {
        $data = input();
        $info = DomainDnsModel::where('id', $data['id'])->find();
        $request_params = [
            'recordId' => $info['dns_id'],
            'RR' => $data['sub_domain'],
            'type' => $data['type'],
            'value' => $data['value'],
            'TTL' => (int) $data['ttl'],
        ];

        $ali = new AliService();
        $res = $ali->updateDomainRecord($request_params);

        if($res){
            DomainDnsModel::update($data);
            $this->success('编辑成功');
        }else{
            $this->error('编辑失败');
        }
    }

    public function ali_dns_status()
    {
        $id = input('id');
        $status = input('status');

        $info = DomainDnsModel::where('id', $id)->find();
        $request_params = [
            'recordId' => $info['dns_id'],
            'status' => $status == 1 ? 'Enable' : 'Disable'
        ];

        $ali = new AliService();
        $res = $ali->setDomainRecordStatus($request_params);

        if($res){
            DomainDnsModel::where('id', $id)->update(['status' => $status]);
            $this->success('变更成功');
        }else{
            $this->error('变更失败');
        }
    }

    public function ali_dns_remark()
    {
        $id = input('id');
        $info = DomainDnsModel::where('id', $id)->find();

        $this->assign('info', $info);
        return $this->fetch();
    }

    public function ali_dns_remark_post()
    {
        $id = input('id');
        $remark = input('remark');

        $info = DomainDnsModel::where('id', $id)->find();
        $request_params = [
            'recordId' => $info['dns_id'],
            'remark' => $remark
        ];

        $ali = new AliService();
        $ali->updateDomainRecordRemark($request_params);

        DomainDnsModel::where('id', $id)->update(['remark' => $remark]);
        $this->success('变更成功');
    }

    public function ali_dns_del()
    {
        $id = input('id');

        $info = DomainDnsModel::where('id', $id)->find();
        $request_params = [
            'recordId' => $info['dns_id']
        ];

        $ali = new AliService();
        $ali->deleteDomainRecord($request_params);

        DomainDnsModel::where('id', $id)->delete();
        $this->success('删除成功');
    }

    public function ali_update()
    {
        $request_params = [
            'pageNumber' => 1
        ];
        $ali = new AliService();
        $list = $ali->getDomainList([], $request_params);
        foreach ($list as $k => $v) {
            if(!DomainModel::where('domain', $v['domainName'])->find()){
                DomainModel::insert(['domain' => $v['domainName'], 'type' => 1, 'domain_id' => $v['domainId']]);
            }
        }
        $this->success('更新完成');
    }

    /**
     * 腾讯云域名
     * @adminMenu(
     *     'name' => '腾讯云域名',
     *     'parent' => 'default',
     *     'display' => true,
     *     'hasView' => true,
     *     'order' => 10000,
     *     'icon' => '',
     *     'remark' => '',
     *     'param' => ''
     * )
     */
    public function txy_index()
    {
        $list = DomainModel::where('type', 2)->paginate(10);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function txy_dns()
    {
        $data = input();
        $info = DomainModel::where('id', $data['id'])->find();
        $list = DomainDnsModel::where('domain_id', $data['id'])->order('id desc')->paginate(10);

        $list->appends($data);
        $this->assign('info', $info);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function txy_dns_update()
    {
        $id = input('id');
        $domain = DomainModel::where('id', $id)->value('domain');
        $request_params = [
            'Domain' => $domain,
            'Offset' => 0,
            'Limit' => 3000
        ];

        $txy = new TxyService();
        $list = $txy->getDomainDnsList($request_params);

        $new_ids = [];
        foreach ($list as $k => $v) {
            $new_ids[] = $v['RecordId'];
        }

        DomainDnsModel::startTrans();

        try {
            $data_ids_str = DomainDnsModel::where('domain_id', $id)->value('group_concat(dns_id) ids');
            $data_ids = !empty($data_ids_str) ? explode(',', $data_ids_str) : [];

            // 新增的差集
            $left_diff = array_diff($new_ids, $data_ids);
            // 删除的差集
            $right_diff = array_diff($data_ids, $new_ids);
            // 更新的交集
            $center_diff = array_intersect($new_ids, $data_ids);

            foreach ($list as $k => $v) {
                $data = [
                    'domain_id' => $id,
                    'dns_id' => $v['RecordId'],
                    'sub_domain' => $v['Name'],
                    'type' => $v['Type'],
                    'value' => $v['Value'],
                    'ttl' => $v['TTL'],
                    'status' => $v['Status'] === 'ENABLE' ? 1 : 0,
                    'remark' => $v['Remark']
                ];

                if(in_array($v['RecordId'], $left_diff)){
                    DomainDnsModel::insert($data);
                }

                if(in_array($v['RecordId'], $center_diff)){
                    DomainDnsModel::where('dns_id', $v['RecordId'])->update($data);
                }
            }

            $right_diff_ids = implode(',', $right_diff);
            DomainDnsModel::where('dns_id', 'in', $right_diff_ids)->delete();

            DomainDnsModel::commit();
            $this->success('更新完成');
        }catch(DbException $e){
            DomainDnsModel::rollback();
            $this->error('更新完成');
        }
    }

    public function txy_dns_add()
    {
        $domain_id = input('domain_id');
        $info = DomainModel::where('id', $domain_id)->find();
        $this->assign('info', $info);
        return $this->fetch();
    }

    public function txy_dns_add_post()
    {
        $data = input();
        $info = DomainModel::where('id', $data['domain_id'])->find();
        $request_params = [
            'Domain' => $info['domain'],
            'SubDomain' => $data['sub_domain'],
            'RecordType' => $data['type'],
            'RecordLine' => '默认',
            'Value' => $data['value'],
            'TTL' => (int) $data['ttl'],
        ];

        $txy = new TxyService();
        $res = $txy->addDomainRecord($request_params);

        if($res){
            $data['dns_id'] = $res;
            $data['status'] = 1;
            DomainDnsModel::insert($data);
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }

    public function txy_dns_edit()
    {
        $id = input('id');
        $domain_info = DomainDnsModel::where('id', $id)->find();
        $info = DomainModel::where('id', $domain_info['domain_id'])->find();

        $this->assign('domain_info', $domain_info);
        $this->assign('info', $info);
        return $this->fetch();
    }

    public function txy_dns_edit_post()
    {
        $data = input();
        $domain_info = DomainDnsModel::where('id', $data['id'])->find();
        $info = DomainModel::where('id', $domain_info['domain_id'])->find();
        $request_params = [
            'Domain' => $info['domain'],
            'RecordId' => (int) $domain_info['dns_id'],
            'SubDomain' => $data['sub_domain'],
            'RecordType' => $data['type'],
            'RecordLine' => '默认',
            'Value' => $data['value'],
            'TTL' => (int) $data['ttl'],
        ];

        $txy = new TxyService();
        $res = $txy->updateDomainRecord($request_params);

        if($res){
            DomainDnsModel::update($data);
            $this->success('编辑成功');
        }else{
            $this->error('编辑失败');
        }
    }

    public function txy_dns_status()
    {
        $id = input('id');
        $status = input('status');

        $domain_info = DomainDnsModel::where('id', $id)->find();
        $info = DomainModel::where('id', $domain_info['domain_id'])->find();
        $request_params = [
            'Domain' => $info['domain'],
            'RecordId' => (int) $domain_info['dns_id'],
            'Status' => $status == 1 ? 'ENABLE' : 'DISABLE'
        ];

        $txy = new TxyService();
        $res = $txy->setDomainRecordStatus($request_params);

        if($res){
            DomainDnsModel::where('id', $id)->update(['status' => $status]);
            $this->success('变更成功');
        }else{
            $this->error('变更失败');
        }
    }

    public function txy_dns_remark()
    {
        $id = input('id');
        $info = DomainDnsModel::where('id', $id)->find();

        $this->assign('info', $info);
        return $this->fetch();
    }

    public function txy_dns_remark_post()
    {
        $id = input('id');
        $remark = input('remark');

        $domain_info = DomainDnsModel::where('id', $id)->find();
        $info = DomainModel::where('id', $domain_info['domain_id'])->find();
        $request_params = [
            'Domain' => $info['domain'],
            'RecordId' => (int) $domain_info['dns_id'],
            'Remark' => $remark
        ];

        $txy = new TxyService();
        $txy->updateDomainRecordRemark($request_params);

        DomainDnsModel::where('id', $id)->update(['remark' => $remark]);
        $this->success('变更成功');
    }

    public function txy_dns_del()
    {
        $id = input('id');

        $domain_info = DomainDnsModel::where('id', $id)->find();
        $info = DomainModel::where('id', $domain_info['domain_id'])->find();
        $request_params = [
            'Domain' => $info['domain'],
            'RecordId' => (int) $domain_info['dns_id'],
        ];

        $txy = new TxyService();
        $txy->deleteDomainRecord($request_params);

        DomainDnsModel::where('id', $id)->delete();
        $this->success('删除成功');
    }

    public function txy_update()
    {
        $txy = new TxyService();
        $list = $txy->getDomainList();
        foreach ($list as $k => $v) {
            if(!DomainModel::where('domain', $v['Name'])->find()){
                DomainModel::insert(['domain' => $v['Name'], 'type' => 2, 'domain_id' => $v['DomainId']]);
            }
        }
        $this->success('更新完成');
    }
}
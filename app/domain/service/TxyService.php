<?php

namespace app\domain\service;

use TencentCloud\Common\Credential;
use TencentCloud\Dnspod\V20210323\DnspodClient;
use TencentCloud\Dnspod\V20210323\Models\CreateRecordRequest;
use TencentCloud\Dnspod\V20210323\Models\DeleteRecordRequest;
use TencentCloud\Dnspod\V20210323\Models\DescribeDomainListRequest;
use TencentCloud\Dnspod\V20210323\Models\DescribeRecordListRequest;
use TencentCloud\Dnspod\V20210323\Models\ModifyRecordRemarkRequest;
use TencentCloud\Dnspod\V20210323\Models\ModifyRecordRequest;
use TencentCloud\Dnspod\V20210323\Models\ModifyRecordStatusRequest;
use think\facade\Env;

class TxyService
{
    // 默认查询3000条域名
    public function getDomainList()
    {
        $config = new Credential(Env::get('TXY_KEY'), Env::get('TXY_SECRET'));

        $txy = new DnspodClient($config, '');

        $request = new DescribeDomainListRequest();
        $response = $txy->DescribeDomainList($request);
        $res = json_decode(json_encode($response), true);

        return $res['DomainList'];
    }

    public function getDomainDnsList($request_params = [])
    {
        $config = new Credential(Env::get('TXY_KEY'), Env::get('TXY_SECRET'));

        $txy = new DnspodClient($config, '');

        $request = new DescribeRecordListRequest();
        $request->fromJsonString(json_encode($request_params));
        $response = $txy->DescribeRecordList($request);

        $res = json_decode(json_encode($response), true);

        return $res['RecordList'];
    }

    public function addDomainRecord($request_params = [])
    {
        $config = new Credential(Env::get('TXY_KEY'), Env::get('TXY_SECRET'));

        $txy = new DnspodClient($config, '');

        $request = new CreateRecordRequest();
        $request->fromJsonString(json_encode($request_params));
        $response = $txy->CreateRecord($request);

        $res = json_decode(json_encode($response), true);

        return !empty($res['RecordId']) ? $res['RecordId'] : 0;
    }

    public function updateDomainRecord($request_params = [])
    {
        $config = new Credential(Env::get('TXY_KEY'), Env::get('TXY_SECRET'));

        $txy = new DnspodClient($config, '');

        $request = new ModifyRecordRequest();
        $request->fromJsonString(json_encode($request_params));
        $response = $txy->ModifyRecord($request);

        $res = json_decode(json_encode($response), true);

        return !empty($res['RecordId']) ? $res['RecordId'] : 0;
    }

    public function setDomainRecordStatus($request_params = [])
    {
        $config = new Credential(Env::get('TXY_KEY'), Env::get('TXY_SECRET'));

        $txy = new DnspodClient($config, '');

        $request = new ModifyRecordStatusRequest();
        $request->fromJsonString(json_encode($request_params));
        $response = $txy->ModifyRecordStatus($request);

        $res = json_decode(json_encode($response), true);

        return !empty($res['RecordId']) ? $res['RecordId'] : 0;
    }

    public function updateDomainRecordRemark($request_params = [])
    {
        $config = new Credential(Env::get('TXY_KEY'), Env::get('TXY_SECRET'));

        $txy = new DnspodClient($config, '');

        $request = new ModifyRecordRemarkRequest();
        $request->fromJsonString(json_encode($request_params));
        $response = $txy->ModifyRecordRemark($request);

        $res = json_decode(json_encode($response), true);

        return !empty($res['RecordId']) ? $res['RecordId'] : 0;
    }

    public function deleteDomainRecord($request_params = [])
    {
        $config = new Credential(Env::get('TXY_KEY'), Env::get('TXY_SECRET'));

        $txy = new DnspodClient($config, '');

        $request = new DeleteRecordRequest();
        $request->fromJsonString(json_encode($request_params));
        $response = $txy->DeleteRecord($request);

        $res = json_decode(json_encode($response), true);

        return !empty($res['RequestId']) ? $res['RequestId'] : 0;
    }
}
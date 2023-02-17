<?php

namespace app\domain\service;

use AlibabaCloud\SDK\Alidns\V20150109\Alidns;
use AlibabaCloud\SDK\Alidns\V20150109\Models\AddDomainRecordRequest;
use AlibabaCloud\SDK\Alidns\V20150109\Models\DeleteDomainRecordRequest;
use AlibabaCloud\SDK\Alidns\V20150109\Models\DescribeDomainRecordsRequest;
use AlibabaCloud\SDK\Alidns\V20150109\Models\DescribeDomainsRequest;
use AlibabaCloud\SDK\Alidns\V20150109\Models\SetDomainRecordStatusRequest;
use AlibabaCloud\SDK\Alidns\V20150109\Models\UpdateDomainRecordRemarkRequest;
use AlibabaCloud\SDK\Alidns\V20150109\Models\UpdateDomainRecordRequest;
use Darabonba\OpenApi\Models\Config;
use think\facade\Env;

class AliService
{
    public function getDomainList($list = [], $request_params = [])
    {
        $config = new Config();
        $config->accessKeyId = Env::get('ALI_KEY');
        $config->accessKeySecret = Env::get('ALI_SECRET');

        $ali = new Alidns($config);
        $request = new DescribeDomainsRequest($request_params);
        $response = $ali->DescribeDomains($request);
        $res = json_decode(json_encode($response->body), true);

        $list = empty($list) ? $res['domains']['domain'] : array_merge($list, $res['domains']['domain']);
        if($res['pageNumber'] * $res['pageSize'] < $res['totalCount']){
            $request_params['pageNumber']++;
            $list = $this->getDomainList($list, $request_params);
        }
        return $list;
    }

    public function getDomainDnsList($list = [], $request_params = [])
    {
        $config = new Config();
        $config->accessKeyId = Env::get('ALI_KEY');
        $config->accessKeySecret = Env::get('ALI_SECRET');

        $ali = new Alidns($config);
        $request = new DescribeDomainRecordsRequest($request_params);
        $response = $ali->DescribeDomainRecords($request);
        $res = json_decode(json_encode($response->body), true);

        $list = empty($list) ? $res['domainRecords']['record'] : array_merge($list, $res['domainRecords']['record']);
        if($res['pageNumber'] * $res['pageSize'] < $res['totalCount']){
            $request_params['pageNumber']++;
            $list = $this->getDomainDnsList($list, $request_params);
        }
        return $list;
    }

    public function updateDomainRecordRemark($request_params)
    {
        $config = new Config();
        $config->accessKeyId = Env::get('ALI_KEY');
        $config->accessKeySecret = Env::get('ALI_SECRET');

        $ali = new Alidns($config);
        $request = new UpdateDomainRecordRemarkRequest($request_params);
        $response = $ali->UpdateDomainRecordRemark($request);

        return json_decode(json_encode($response->body), true);
    }

    public function setDomainRecordStatus($request_params)
    {
        $config = new Config();
        $config->accessKeyId = Env::get('ALI_KEY');
        $config->accessKeySecret = Env::get('ALI_SECRET');

        $ali = new Alidns($config);
        $request = new SetDomainRecordStatusRequest($request_params);
        $response = $ali->SetDomainRecordStatus($request);

        $res = json_decode(json_encode($response->body), true);
        return $res['status'] === $request_params['status'];
    }

    public function addDomainRecord($request_params)
    {
        $config = new Config();
        $config->accessKeyId = Env::get('ALI_KEY');
        $config->accessKeySecret = Env::get('ALI_SECRET');

        $ali = new Alidns($config);
        $request = new AddDomainRecordRequest($request_params);
        $response = $ali->AddDomainRecord($request);

        $res = json_decode(json_encode($response->body), true);
        return !empty($res['recordId']) ? $res['recordId'] : 0;
    }

    public function updateDomainRecord($request_params)
    {
        $config = new Config();
        $config->accessKeyId = Env::get('ALI_KEY');
        $config->accessKeySecret = Env::get('ALI_SECRET');

        $ali = new Alidns($config);
        $request = new UpdateDomainRecordRequest($request_params);
        $response = $ali->UpdateDomainRecord($request);

        $res = json_decode(json_encode($response->body), true);
        return !empty($res['recordId']) ? $res['recordId'] : 0;
    }

    public function deleteDomainRecord($request_params)
    {
        $config = new Config();
        $config->accessKeyId = Env::get('ALI_KEY');
        $config->accessKeySecret = Env::get('ALI_SECRET');

        $ali = new Alidns($config);
        $request = new DeleteDomainRecordRequest($request_params);
        $response = $ali->DeleteDomainRecord($request);

        $res = json_decode(json_encode($response->body), true);
        return !empty($res['recordId']) ? $res['recordId'] : 0;
    }
}
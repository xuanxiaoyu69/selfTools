<?php

// This file is auto-generated, don't edit it. Thanks.

namespace AlibabaCloud\SDK\Alidns\V20150109\Models\DescribeDnsGtmAddressPoolAvailableConfigResponseBody\attributeInfos;

use AlibabaCloud\Tea\Model;

class attributeInfo extends Model
{
    /**
     * @var string
     */
    public $fatherCode;

    /**
     * @var string
     */
    public $groupCode;

    /**
     * @var string
     */
    public $groupName;

    /**
     * @var string
     */
    public $lineCode;

    /**
     * @var string
     */
    public $lineName;
    protected $_name = [
        'fatherCode' => 'FatherCode',
        'groupCode'  => 'GroupCode',
        'groupName'  => 'GroupName',
        'lineCode'   => 'LineCode',
        'lineName'   => 'LineName',
    ];

    public function validate()
    {
    }

    public function toMap()
    {
        $res = [];
        if (null !== $this->fatherCode) {
            $res['FatherCode'] = $this->fatherCode;
        }
        if (null !== $this->groupCode) {
            $res['GroupCode'] = $this->groupCode;
        }
        if (null !== $this->groupName) {
            $res['GroupName'] = $this->groupName;
        }
        if (null !== $this->lineCode) {
            $res['LineCode'] = $this->lineCode;
        }
        if (null !== $this->lineName) {
            $res['LineName'] = $this->lineName;
        }

        return $res;
    }

    /**
     * @param array $map
     *
     * @return attributeInfo
     */
    public static function fromMap($map = [])
    {
        $model = new self();
        if (isset($map['FatherCode'])) {
            $model->fatherCode = $map['FatherCode'];
        }
        if (isset($map['GroupCode'])) {
            $model->groupCode = $map['GroupCode'];
        }
        if (isset($map['GroupName'])) {
            $model->groupName = $map['GroupName'];
        }
        if (isset($map['LineCode'])) {
            $model->lineCode = $map['LineCode'];
        }
        if (isset($map['LineName'])) {
            $model->lineName = $map['LineName'];
        }

        return $model;
    }
}

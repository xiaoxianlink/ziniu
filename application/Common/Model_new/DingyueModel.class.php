<?php
namespace Common\Model_new;
use Common\Model\CommonModel;
class DingyueModel extends CommonModel{
    protected $autoCheckFields =false;
    protected function _before_write(&$data) {
        parent::_before_write($data);
    }
}
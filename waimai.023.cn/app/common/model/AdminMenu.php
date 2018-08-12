<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/6
 * Time: 16:11
 */

namespace app\common\model;


class AdminMenu extends Base
{
    protected $name = 'auth_rule';

    protected $search_field = 'title|name';

    public function setNameAttr($val,$data)
    {
        if ($data['menu'] == 3) {  // 顶部菜单
            $value['menu'] = 3;
            return $val . '$' . http_build_query($value);
        }elseif ($data['menu'] == 2){ //按钮
            if (!empty($data['but_type'])){
                $value['but_type'] = $data['but_type'];
            }
            if (!empty($data['but_site'])){
                $value['but_site'] = $data['but_site'];
            }
            if (!empty($data['but_field'])){
                $value['but_field'] = $data['but_field'];
            }
            return $val . '$' . http_build_query($value);
        }else{
            return $val;
        }
    }

    public function getNameAttr($val,$data)
    {
        if (in_array($data['menu'],[2,3])){
            $list = explode('$',$val);
            return $list[0];
        }else{
            return $val;
        }
    }

    public function setPidAttr($val)
    {
        if (empty($val)){
            return 0;
        }else{
            return $val;
        }
    }

    public function getMenuAttr($val)
    {
        $list = config('auth.menu_type_value');
        return $list[$val];
    }

}
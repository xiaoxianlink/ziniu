<?php
return array (
  'app' => 'Admin_new',
  'model' => 'Menu',
  'action' => 'default',
  'data' => '',
  'type' => '1',
  'status' => '1',
  'name' => '菜单管理',
  'icon' => 'list',
  'remark' => '',
  'listorder' => '20',
  'children' => 
  array (
    array (
      'app' => 'Admin_new',
      'model' => 'Navcat',
      'action' => 'default1',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '前台菜单',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
      'children' => 
      array (
        array (
          'app' => 'Admin_new',
          'model' => 'Nav',
          'action' => 'index',
          'data' => '',
          'type' => '1',
          'status' => '1',
          'name' => '菜单管理',
          'icon' => '',
          'remark' => '',
          'listorder' => '0',
          'children' => 
          array (
            array (
              'app' => 'Admin_new',
              'model' => 'Nav',
              'action' => 'listorders',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '前台导航排序',
              'icon' => '',
              'remark' => '',
              'listorder' => '0',
            ),
            array (
              'app' => 'Admin_new',
              'model' => 'Nav',
              'action' => 'delete',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '删除菜单',
              'icon' => '',
              'remark' => '',
              'listorder' => '1000',
            ),
            array (
              'app' => 'Admin_new',
              'model' => 'Nav',
              'action' => 'edit',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '编辑菜单',
              'icon' => '',
              'remark' => '',
              'listorder' => '1000',
              'children' => 
              array (
                array (
                  'app' => 'Admin_new',
                  'model' => 'Nav',
                  'action' => 'edit_post',
                  'data' => '',
                  'type' => '1',
                  'status' => '0',
                  'name' => '提交编辑',
                  'icon' => '',
                  'remark' => '',
                  'listorder' => '0',
                ),
              ),
            ),
            array (
              'app' => 'Admin_new',
              'model' => 'Nav',
              'action' => 'add',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '添加菜单',
              'icon' => '',
              'remark' => '',
              'listorder' => '1000',
              'children' => 
              array (
                array (
                  'app' => 'Admin_new',
                  'model' => 'Nav',
                  'action' => 'add_post',
                  'data' => '',
                  'type' => '1',
                  'status' => '0',
                  'name' => '提交添加',
                  'icon' => '',
                  'remark' => '',
                  'listorder' => '0',
                ),
              ),
            ),
          ),
        ),
        array (
          'app' => 'Admin_new',
          'model' => 'Navcat',
          'action' => 'index',
          'data' => '',
          'type' => '1',
          'status' => '1',
          'name' => '菜单分类',
          'icon' => '',
          'remark' => '',
          'listorder' => '0',
          'children' => 
          array (
            array (
              'app' => 'Admin_new',
              'model' => 'Navcat',
              'action' => 'delete',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '删除分类',
              'icon' => '',
              'remark' => '',
              'listorder' => '1000',
            ),
            array (
              'app' => 'Admin_new',
              'model' => 'Navcat',
              'action' => 'edit',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '编辑分类',
              'icon' => '',
              'remark' => '',
              'listorder' => '1000',
              'children' => 
              array (
                array (
                  'app' => 'Admin_new',
                  'model' => 'Navcat',
                  'action' => 'edit_post',
                  'data' => '',
                  'type' => '1',
                  'status' => '0',
                  'name' => '提交编辑',
                  'icon' => '',
                  'remark' => '',
                  'listorder' => '0',
                ),
              ),
            ),
            array (
              'app' => 'Admin_new',
              'model' => 'Navcat',
              'action' => 'add',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '添加分类',
              'icon' => '',
              'remark' => '',
              'listorder' => '1000',
              'children' => 
              array (
                array (
                  'app' => 'Admin_new',
                  'model' => 'Navcat',
                  'action' => 'add_post',
                  'data' => '',
                  'type' => '1',
                  'status' => '0',
                  'name' => '提交添加',
                  'icon' => '',
                  'remark' => '',
                  'listorder' => '0',
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    array (
      'app' => 'Admin_new',
      'model' => 'Menu',
      'action' => 'index',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '后台菜单',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
      'children' => 
      array (
        array (
          'app' => 'Admin_new',
          'model' => 'Menu',
          'action' => 'add',
          'data' => '',
          'type' => '1',
          'status' => '0',
          'name' => '添加菜单',
          'icon' => '',
          'remark' => '',
          'listorder' => '0',
          'children' => 
          array (
            array (
              'app' => 'Admin_new',
              'model' => 'Menu',
              'action' => 'add_post',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '提交添加',
              'icon' => '',
              'remark' => '',
              'listorder' => '0',
            ),
          ),
        ),
        array (
          'app' => 'Admin_new',
          'model' => 'Menu',
          'action' => 'listorders',
          'data' => '',
          'type' => '1',
          'status' => '0',
          'name' => '后台菜单排序',
          'icon' => '',
          'remark' => '',
          'listorder' => '0',
        ),
        array (
          'app' => 'Admin_new',
          'model' => 'Menu',
          'action' => 'export_menu',
          'data' => '',
          'type' => '1',
          'status' => '0',
          'name' => '菜单备份',
          'icon' => '',
          'remark' => '',
          'listorder' => '1000',
        ),
        array (
          'app' => 'Admin_new',
          'model' => 'Menu',
          'action' => 'edit',
          'data' => '',
          'type' => '1',
          'status' => '0',
          'name' => '编辑菜单',
          'icon' => '',
          'remark' => '',
          'listorder' => '1000',
          'children' => 
          array (
            array (
              'app' => 'Admin_new',
              'model' => 'Menu',
              'action' => 'edit_post',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '提交编辑',
              'icon' => '',
              'remark' => '',
              'listorder' => '0',
            ),
          ),
        ),
        array (
          'app' => 'Admin_new',
          'model' => 'Menu',
          'action' => 'delete',
          'data' => '',
          'type' => '1',
          'status' => '0',
          'name' => '删除菜单',
          'icon' => '',
          'remark' => '',
          'listorder' => '1000',
        ),
        array (
          'app' => 'Admin_new',
          'model' => 'Menu',
          'action' => 'lists',
          'data' => '',
          'type' => '1',
          'status' => '0',
          'name' => '所有菜单',
          'icon' => '',
          'remark' => '',
          'listorder' => '1000',
        ),
      ),
    ),
  ),
);
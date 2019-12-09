# LemonTemplate - 模板插件

## 使用方法
```php
/**
 * 挂载监听方法
 *
 * 如果是主题的默认模板 - 调用 LemonTemplate_MountTheme
 */
LemonTemplate_Mount('调用名称', '应用名称', '类型:限定theme|plugin', '模板文件夹');
/**
 * 挂载监听方法 - 监听主题
 */
LemonTemplate_MountTheme('主题名称');
/**
 * 卸载监听方法
 */
LemonTemplate_Unmount('调用名称');
/**
 * 处理页面输出前，调用激活
 *
 * 如是监听的当前主题，无需调用此方法
 */
LemonTemplate_Active('调用名称');
```

## 语法
与ZBlogPHP原有的模板语法一致，额外增加子文件夹处理  
编写子文件夹模板无需额外申明，调用模板文件时，路径层级依据template文件夹来计算  
```php
/**
 * 子文件夹调用 - 案例1
 *
 * template/a/aaa.php中调用template/b/bbb.php
 */
{template:b/bbb}
// 或
include $zbp->template->GetTemplate('b/bbb');

/**
 * 子文件夹调用 - 案例2
 *
 * template/a/ccc.php中调用template/a/aaa.php 和 template/b/bbb.php
 */
{template:a/aaa}
{template:b/bbb}
// 或
include $zbp->template->GetTemplate('a/aaa');
include $zbp->template->GetTemplate('b/bbb');

/**
 * 子文件夹调用 - 案例3
 *
 * template/a/ccc.php中调用template/ddd.php
 */
{template:ddd}
// 或
include $zbp->template->GetTemplate('ddd');
```

## 模板注释
json文件标注模板注释，在应用目录下，创建`lmTpl_监听模板文件夹.json`文件标注注释  
示例：
```json
{
    "id": "应用名称", // 冗余项
    "templates": [
        {
            "filename": "index",
            "type": "list",
            "name": "列表自动模板"
        },
        {
            "filename": "single",
            "type": "single",
            "name": "文章/单页自动模板"
        },
        {
            "filename": "page/archive",
            "type": "page",
            "name": "文章归档页面"
        },
        {
            "filename": "page/link",
            "type": "page",
            "name": "友情链接页面"
        },
        {
            "filename": "page/tag-cloud",
            "type": "page",
            "name": "标签云页面"
        }
    ]
}
```
### 文件规则：
```
`templates` 字段为可以被选择的模板文件数组
`filename`  字段为文件名（*不加.php）
`type`      字段为类型，用于区分不同位置
`name`      字段为模板名称描述
```
### type支持的类型：
```
// 插件定义类型
all     - 全部类型可用
single  - 文章或单页中可用
list    - 所有列表类型可用，分类/标签/日期/作者页等
// 系统默认类型
article - 文章
page    - 单页
cate    - 分类
tag     - 标签
date    - 日期
auth    - 作者
```
### 使用
在完成lmTpl_template.json文件的标注以后，插件会自动替换调系统原有的模板选择框  
如果该文件不存在，不执行替换操作  

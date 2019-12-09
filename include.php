<?php
/**
 * LemonTemplate
 * 柠檬系列：模板插件
 */

include_once __DIR__ . '/function/index.php';

/**
 * 注册插件
 */
RegisterPlugin('LemonTemplate', 'ActivePlugin_LemonTemplate');

/**
 * 激活插件工具
 */
function ActivePlugin_LemonTemplate() {
    // 挂载系统接口
    Add_Filter_Plugin('Filter_Plugin_ViewIndex_Begin', 'LemonTemplate_ViewIndex_Begin');
    Add_Filter_Plugin('Filter_Plugin_Misc_Begin', 'LemonTemplate_Misc_Begin');
    Add_Filter_Plugin('Filter_Plugin_Template_GetTemplate', 'LemonTemplate_Plugin_Template_GetTemplate');
    Add_Filter_Plugin('Filter_Plugin_Edit_Response3', 'LemonTemplate_OutputTemplateSelect');
    Add_Filter_Plugin('Filter_Plugin_Category_Edit_Response', 'LemonTemplate_OutputTemplateSelect');
    Add_Filter_Plugin('Filter_Plugin_Tag_Edit_Response', 'LemonTemplate_OutputTemplateSelect');
    Add_Filter_Plugin('Filter_Plugin_Member_Edit_Response', 'LemonTemplate_OutputTemplateSelect');
}

/**
 * 安装插件执行内容
 */
function InstallPlugin_LemonTemplate() {
    global $zbp;
    if (!$zbp->Config('LemonTemplateWatch')->HasKey('list')) {
        $zbp->Config('LemonTemplateWatch')->list = [];
        $zbp->SaveConfig('LemonTemplateWatch');
    }
}

/**
 * 卸载插件执行内容
 */
function UninstallPlugin_LemonTemplate() {}

/**
 * ViewIndex挂载接口
 */
function LemonTemplate_ViewIndex_Begin() {
    global $zbp;
    if ($zbp->option['ZC_DEBUG_MODE']) {
        LemonTemplate_Compiles();
    }
    LemonTemplate_AutoActive();
    LemonTemplate_GetViewDeviceType();
}

/**
 * misc中刷新编译模板
 */
function LemonTemplate_Misc_Begin() {
    LemonTemplate_Compiles();
}

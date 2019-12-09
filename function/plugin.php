<?php
/**
 * 挂载编译监听
 * @param {String} $name 调用名称
 * @param {String} $appname 监听应用名称
 * @param {String} $type theme:主题 | plugin:插件
 * @param {String} $template 模板路径名称
 */
function LemonTemplate_Mount($name, $appname, $type = 'theme', $template = 'template') {
    global $zbp;

    $watchs = $zbp->Config('LemonTemplateWatch')->list;
    if (!is_array($watchs)) {
        $watchs = array();
    }
    $watchs[$name] = array($name, $appname, $type, $template);
    $zbp->Config('LemonTemplateWatch')->list = $watchs;
    $zbp->SaveConfig('LemonTemplateWatch');
}

/**
 * 挂载编译监听 - 监听主题
 *
 * @param {String} $appname 主题名称
 */
function LemonTemplate_MountTheme($appname) {
    global $zbp;

    $watchs = $zbp->Config('LemonTemplateWatch')->list;
    if (!is_array($watchs)) {
        $watchs = array();
    }
    $watchs[$appname] = array($appname, $appname, 'theme', 'template');
    $zbp->Config('LemonTemplateWatch')->list = $watchs;
    $zbp->SaveConfig('LemonTemplateWatch');
}

/**
 * 卸载编译监听
 * @param {String} $name 监听的调用名称
 */
function LemonTemplate_Unmount($name) {
    global $zbp;

    $watchs = $zbp->Config('LemonTemplateWatch')->list;
    if (!is_array($watchs)) {
        $watchs = array();
    }
    if (isset($watchs[$name])) {
        unset($watchs[$name]);
    }
    $zbp->Config('LemonTemplateWatch')->list = $watchs;
    $zbp->SaveConfig('LemonTemplateWatch');
}

/**
 * 是否监听此应用
 * @param {String} $name 监听的调用名称
 */
function LemonTemplate_HasWatch($name) {
    global $zbp;

    $watchs = $zbp->Config('LemonTemplateWatch')->list;
    if (!is_array($watchs)) {
        $watchs = array();
    }

    return isset($watchs[$name]);
}

/**
 * 刷新编译模板
 *
 * @param {String} $name 编译模板名称
 */
function LemonTemplate_Compile($name, $appname = null) {
    global $zbp;

    if (!$appname) {
        $appname = $name;
    }

    $watchs = $zbp->Config('LemonTemplateWatch')->list;
    if (!is_array($watchs)) {
        $watchs = array();
    }

    if (isset($watchs[$name])) {
        $val = $watchs[$name];
        $tpl = new LemonTemplate();
        $tpl->Init($val[0], $val[1], $val[2], $val[3]);
        $tpl->Compile();
    }
}

/**
 * 刷新编译模板
 */
function LemonTemplate_Compiles() {
    global $zbp;

    $watchs = $zbp->Config('LemonTemplateWatch')->list;
    if (!is_array($watchs)) {
        $watchs = array();
    }

    foreach ($watchs as $key => $val) {
        $tpl = new LemonTemplate();
        $tpl->Init($val[0], $val[1], $val[2], $val[3]);
        $tpl->Compile();
    }
}

/**
 * 激活模板的使用
 *
 * @param {String} $name 调用模板名称
 */
function LemonTemplate_Active($name) {
    global $zbp;

    $watchs = $zbp->Config('LemonTemplateWatch')->list;
    if (!is_array($watchs)) {
        $watchs = array();
    }

    if (isset($watchs[$name])) {
        $val = $watchs[$name];
        $tpl = new LemonTemplate();
        $tpl->Init($val[0], $val[1], $val[2], $val[3]);
        $tpl->Active();
    }
}

/**
 * 自动激活模板
 */
function LemonTemplate_AutoActive() {
    global $zbp;
    $name = $zbp->theme;
    LemonTemplate_Active($name);
}

/**
 * 判断客户端类型
 */
function LemonTemplate_GetViewDeviceType() {
    global $zbp, $LemonTemplateBase;

    $ua = GetVars('HTTP_USER_AGENT', 'SERVER');

    if (preg_match('/mobile/i', $ua)) {
        $LemonTemplateBase['isMobile'] = true;
    } else {
        $LemonTemplateBase['isMobile'] = false;
    }
    if (preg_match('/pad/i', $ua)) {
        $LemonTemplateBase['isPad'] = true;
    } else {
        $LemonTemplateBase['isPad'] = false;
    }
    if (preg_match('/android/i', $ua)) {
        $LemonTemplateBase['isAndroid'] = true;
    } else {
        $LemonTemplateBase['isAndroid'] = false;
    }
    if (preg_match('/iOS/i', $ua)) {
        $LemonTemplateBase['isIOS'] = true;
    } else {
        $LemonTemplateBase['isIOS'] = false;
    }
    if (preg_match('/MicroMessenger/i', $ua)) {
        $LemonTemplateBase['isWechat'] = true;
    } else {
        $LemonTemplateBase['isWechat'] = false;
    }
    if (preg_match('/alipay/i', $ua)) {
        $LemonTemplateBase['isAlipay'] = true;
    } else {
        $LemonTemplateBase['isAlipay'] = false;
    }

    $zbp->template->SetTags('LemonTemplateBase', $LemonTemplateBase);
}

/**
 * 选择模板
 * 处理@/
 */
function LemonTemplate_Plugin_Template_GetTemplate($template, $name) {
    global $zbp;

    if (strripos($name, '@/') !== false) {
        $GLOBALS['hooks']['Filter_Plugin_Template_GetTemplate']['LemonTemplate_Plugin_Template_GetTemplate'] = 'return';
        $pathname = str_replace('@/', '', $name);
        $tplPath = $template->GetPath();
        return $tplPath . $pathname . '.php';
    }
}

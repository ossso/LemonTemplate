<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('LemonTemplate')) {$zbp->ShowError(48);die();}

$blogtitle='LemonTemplate';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

$watchName = GetVars('name', 'GET');
$cmdType = GetVars('type', 'GET');
if ($cmdType == 'unwatch') {
    if (isset($watchName)) {
        LemonTemplate_Unmount($watchName);
        $zbp->SetHint('good', '已取消监听');
        Redirect('./main.php');
    }
} elseif ($cmdType == 'compile') {
    if (isset($watchName)) {
        LemonTemplate_Compile($watchName);
        $zbp->SetHint('good', '编译成功');
        Redirect('./main.php');
    }
}
?>

<script>
window.lmtplCancalWatch = function(watchName) {
    if (window.confirm('取消监听可能会导致插件的模板异常，是否继续？')) {
        window.location.href = './main.php?type=unwatch&name=' + watchName;
    }
};
window.lmtplCompile = function(watchName) {
    window.location.href = './main.php?type=compile&name=' + watchName;
};
</script>

<div id="divMain">
    <div class="divHeader">LemonTemplate 模板监听编译列表</div>
    <div class="SubMenu">
    </div>
    <div id="divMain2">
        <p style="line-height: 2; font-weight: bold; font-size: 18px; color: #f00;">取消监听可能会导致插件的模板异常，请勿随意取消</p>
        <p>当前主题: [<?php echo $zbp->theme; ?>] - status: [<?php echo LemonTemplate_HasWatch($zbp->theme); ?>]</p>
        <table class="tableFull tableBorder tableBorder-thcenter" style="max-width: 800px">
            <thead>
                <tr>
                    <th>KEY</th>
                    <th>监听名称</th>
                    <th>来自应用</th>
                    <th>应用类型</th>
                    <th>模板文件夹</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $list = $zbp->Config('LemonTemplateWatch')->list;
                    foreach ($list as $k => $item) {
                        echo '<tr>
                            <td>' . $k . '</td>
                            <td>' . $item[0] . '</td>
                            <td>' . $item[1] . '</td>
                            <td>' . ($item[2] == 'theme' ? '主题' : '插件') . '</td>
                            <td>' . $item[3] . '</td>
                            <td>
                                <a style="color: #f00; margin-right: 10px;" href="javascript:void(0);" onclick="lmtplCancalWatch(\'' . $k . '\')">取消监听</a>
                                <a style="color: #369;" href="javascript:void(0);" onclick="lmtplCompile(\'' . $k . '\')">重新编译</a>
                            </td>
                        </tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
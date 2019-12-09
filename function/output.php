<?php
/**
 * 模板页面输出模板选择器
 */
function LemonTemplate_OutputTemplateSelect() {
    global $zbp;
    if (!LemonTemplate_HasWatch($zbp->theme)) {
        return null;
    }

    $type = GetVars('act', 'GET');

    switch ($type) {
        case 'ArticleEdt':
        case 'PageEdt':
            $type = $type == 'ArticleEdt' ? 'article' : 'page';
            global $article;
            $object = $article;
        break;
        case 'CategoryEdt':
            $type = 'cate';
            global $cate;
            $object = $cate;
        break;
        case 'TagEdt':
            $type = 'tag';
            global $tag;
            $object = $tag;
        break;
        case 'MemberNew':
        case 'MemberEdt':
            $type = 'author';
            global $member;
            $object = $member;
        break;
    }

    $tpl = new LemonTemplate();
    $tpl->Init($zbp->theme, 'theme', 'template');
    if (!$tpl->tplNoteFilePathStat) {
        return null;
    }

    $ops = $tpl->CreateTemplateSelectOptions($type, $object->Template);
    echo '
    <textarea style="display: none;" id="LemonTemplate_templates">' . $ops . '</textarea>
    <script>
    !function() {
        document.getElementByID("cmbTemplate").innerHTML = document.getElementById("LemonTemplate_templates").value;
    }();
    </script>
    ';

    // 分类会多出一个文章的模板选择
    if ($type == 'cate') {
        $ops = $tpl->CreateTemplateSelectOptions('article', $object->LogTemplate);
        echo '
        <textarea style="display: none;" id="LemonTemplate_templates2">' . $ops . '</textarea>
        <script>
        !function() {
            document.getElementByID("#cmbLogTemplate").innerHTML = document.getElementById("LemonTemplate_templates2").value;
        }();
        </script>
        ';
    }
}

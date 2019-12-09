<?php
/**
 * Lemon模板操作类
 */
class LemonTemplate
{
    /**
     * @var string 模板名称
     */
    public $tplName = '';

    /**
     * @var string 模板应用名称
     */
    public $tplAppName = '';

    /**
     * @var string 模板编译路径名称
     */
    public $tplCompilePathName = '';

    /**
     * @var string 模板路径
     */
    public $tplPath = '';

    /**
     * @var string 模板备注文件路径
     */
    public $tplNoteFilePath = '';

    /**
     * @var boolean 模板备注文件是否存在
     */
    public $tplNoteFilePathStat = false;

    /**
     * 初始化
     * 设定模板路径
     * @param {String} $name 模板名称
     * @param {String} $appname 应用名称
     * @param {String} $type 类型 主题theme | 插件plugin
     * @param {String} $pathname 应用下的模板文件路径
     */
    public function Init($name, $appname, $type, $pathname = 'template') {
        global $zbp;

        $this->tplName = $name;
        $this->tplAppName = $appname;
        $this->tplCompilePathName = 'lmTpl_' . $this->tplName;
        $this->tplPath = $type . '/' . $this->tplAppName . '/' . $pathname . '/';
        $this->tplNoteFilePath = $type . '/' . $this->tplAppName . '/lmTpl_' . $pathname . '.json';
        $filePath = $zbp->usersdir . $this->tplNoteFilePath;
        $this->tplNoteFilePathStat = file_exists($filePath);

        return $this;
    }

    /**
     * 读取模板目录下的文件列表
     * @param {String} $dirName 子目录名称
     * @param {String} $parentName 父级目录名称
     *
     * @return {Array} 目录集合
     */
    public function GetTemplateFileList($dirName = null, $parentName = null) {
        global $zbp;

        $templates = array();

        // 读取系统预置模板
        if (empty($dirName)) {
            $files = GetFilesInDir($zbp->path . 'zb_system/defend/default/', 'php');
            foreach ($files as $name => $filePath) {
                $templates[$name] = file_get_contents($filePath);
            }
        }
        
        // 读取设定模板
        $tplPath = $zbp->usersdir . $this->tplPath;
        if (isset($dirName)) {
            $tplPath .= $dirName . '/';
        }
        $files = GetFilesInDir($tplPath, 'php');
        foreach ($files as $name => $filePath) {
            $key = $name;
            if (isset($dirName)) {
                $key = $dirName . '/' . $key;
            }
            if (isset($parentName)) {
                $key = $parentName . '/' . $key;
            }
            $templates[$key] = file_get_contents($filePath);
        }

        // 读取子目录
        $dirs = GetDirsInDir($tplPath);
        foreach ($dirs as $childDirName) {
            $childTemplates = $this->GetTemplateFileList($childDirName, $dirName);
            if (count($childTemplates)) {
                $templates = array_merge($templates, $childTemplates);
            }
        }

        return $templates;
    }

    /**
     * 激活模板的使用
     */
    public function Active() {
        global $zbp;
        $templatePath = $zbp->usersdir . 'cache/compiled/' . $this->tplCompilePathName . '/';
        $zbp->template->SetPath($templatePath);
    }

    /**
     * 编译模板
     */
    public function Compile() {
        global $zbp;

        $compilePath = $zbp->usersdir . 'cache/compiled/' . $this->tplCompilePathName . '/';

        $tpl = new Template();
        $tpl->templates = $this->GetTemplateFileList();
        $childPathList = array();
        $regexp = "{([^/]*)\/}";
        foreach ($tpl->templates as $key => $val) {
            $dirList = array();
            preg_match_all($regexp, $key, $dirList);
            if (isset($dirList[1]) && count($dirList[1]) > 0) {
                $dirPath = $compilePath;
                foreach ($dirList[1] as $item) {
                    $dirPath .= $item . '/';
                    if (!file_exists($dirPath)) {
                        @mkdir($dirPath, 0755, true);
                    }
                }
            }
        }
        $tpl->SetPath($compilePath);
        $tpl->BuildTemplate();
    }

    /**
     * 读取模板路径下的备注
     */
    public function GetTemplateNoteList() {
        global $zbp;
        $filePath = $zbp->usersdir . $this->tplNoteFilePath;
        if (!file_exists($filePath)) {
            return [];
        }
        $fileContent = file_get_contents($filePath);

        try {
            $tplNotes = json_decode($fileContent, true);
        } catch (Exception $e) {
            return [];
        }

        if (empty($tplNotes['templates'])) {
            return [];
        }

        return $tplNotes['templates'];
    }

    /**
     * 匹配对应的模板备注
     * @param {String} $type 调用页面类型
     * @param {String} $filename 需要匹配的名称
     * @param {Array} $notes 备注列表
     */
    public function MatchTemplateNote($type, $filename, $notes) {
        global $zbp;
        $note = null;

        if (empty($notes)) {
            $notes = $this->GetTemplateNoteList();
        }

        foreach ($notes as $item) {
            if ($item['filename'] === $filename) {
                $note = $item;
                break;
            }
        }

        if (empty($note)) {
            return null;
        }

        if ($type == $note['type'] || $note['type'] == 'all') {
            return $note;
        } elseif (($type == 'article' || $type == 'page') && $note['type'] == 'single') {
            return $note;
        } elseif (($type == 'cate' || $type == 'tag' || $type == 'date' || $type == 'author') && $note['type'] == 'list') {
            return $note;
        }

        return null;
    }

    /**
     * 替换系统的模板select选项生成
     * @param {String} $type 调用页面类型
     * @param {String} $selected 被选中的模板名称
     */
    public function CreateTemplateSelectOptions($type, $selected) {
        global $zbp;

        $s = null;
        $s .= '<option value="" >' . $zbp->lang['msg']['none'] . '</option>';

        $notes = $this->GetTemplateNoteList();

        $templates = $zbp->template->templates;

        foreach ($templates as $k => $v) {
            $note = $this->MatchTemplateNote($type, $k, $notes);
            if (isset($note)) {
                if ($selected == $k) {
                    $s .= '<option value="' . $k . '" selected="selected">' . $k . ' [' . $note['name'] . '](' . $zbp->lang['msg']['current_template'] . ')' . '</option>';
                } else {
                    $s .= '<option value="' . $k . '" >' . $k . ' [' . $note['name'] . ']</option>';
                }
            }
        }

        return $s;
    }
}

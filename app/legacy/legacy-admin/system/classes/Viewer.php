<?php

use Legacy\System\Classes\View;

class Viewer
{
    const SYSTEM_TEMPLATES = SERVER_ROOT.'/system/templates';

    const APP_TEMPLATES = SERVER_ROOT.'/app/templates';

    const VIEWS_PATH = SERVER_ROOT.'/pages/views';

    const TEMPLATE_OPENING_TAG = '{% extends ';

    const BLOCK_OPENING_TAG = '{% block ';

    const PLACING_OPENING_TAG = '{% place ';

    const CLOSING_TAG = ' %}';

    private static $templateFile;

    private static $viewFile = false;

    private static $baseTemplateContent;

    private static $blocks = [];

    // render page
    public static function render()
    {

        // fetch the view files
        self::$templateFile = self::getTemplateFile(View::$template);
        if (file_exists($file = self::VIEWS_PATH.'/'.View::$name.'.php')) {
            self::$viewFile = $file;
        }

        echo self::renderView();
    }

    // put togheter the whole view
    private static function renderView()
    {

        // render every file to store the content in block and find the base template
        if (self::$viewFile) {
            self::renderContent(self::$viewFile);
        }
        self::renderContent(self::$templateFile);

        // place the blocks content in the render
        $render = self::$baseTemplateContent;
        $render = self::placeBlocks($render);

        return $render;
    }

    // render a single file
    private static function renderContent($__filePath)
    {
        $parentName = false;

        // access variables
        foreach (View::$variables as $var => $value) {
            ${$var} = $value;
        }

        // render the file content
        ob_start();
        include_once $__filePath;
        $render = ob_get_clean();

        // get template parent
        if (strpos($render, self::TEMPLATE_OPENING_TAG) !== false) {
            $scanning = explode(self::TEMPLATE_OPENING_TAG, $render, 2)[1];
            $parentName = explode(self::CLOSING_TAG, $scanning, 2)[0];
        }

        // store the blocks of content
        self::storeBlocks($render);

        // render the parent, if present
        if ($parentName) {
            $parentFile = self::getTemplateFile($parentName);
            self::renderContent($parentFile);
        } else {
            self::$baseTemplateContent = $render;
        }
    }

    // store the blocks of content of a single render
    private static function storeBlocks($render)
    {
        while (strpos($render, self::BLOCK_OPENING_TAG) !== false) {
            // explode where a block stars
            $scanning = explode(self::BLOCK_OPENING_TAG, $render, 2);
            // store the render before the block
            $contentBeforeBlock = $scanning[0];
            // explode where the block content starts
            $scanning = explode(self::CLOSING_TAG, $scanning[1], 2);
            // store the block name
            $blockName = $scanning[0];
            // explode where the block ends
            $scanning = explode('{% end '.$blockName.self::CLOSING_TAG, $scanning[1], 2);
            // store the block's content
            $blockContent = $scanning[0];
            // store the content after the block
            $contentAfterBlock = $scanning[1];

            // put back togheter the render without the block
            $render = $contentBeforeBlock.$contentAfterBlock;
            // store the block content in the class variable
            self::$blocks[$blockName] = $blockContent;
        }
    }

    // place the blocks in the render
    private static function placeBlocks($render)
    {
        while (strpos($render, self::PLACING_OPENING_TAG) !== false) {
            $scanning = explode(self::PLACING_OPENING_TAG, $render, 2)[1];
            $blockName = explode(self::CLOSING_TAG, $scanning, 2)[0];
            $placeTag = self::PLACING_OPENING_TAG.$blockName.self::CLOSING_TAG;

            $render = str_replace($placeTag, self::$blocks[$blockName] ?? '', $render);
        }

        return $render;
    }

    // get the full path of a template file
    private static function getTemplateFile($templateName)
    {
        if (file_exists($file = self::APP_TEMPLATES.'/views/'.$templateName.'.php')) {
            $templateFile = $file;
        } else {
            $templateFile = self::SYSTEM_TEMPLATES.'/views/'.$templateName.'.php';
        }

        return $templateFile;
    }
}

<?php

class Viewer
{
    public $view;

    private $views_path = SERVER_ROOT.'/pages/views';

    private $app_path = SERVER_ROOT.'/app/templates';

    private $system_path = SERVER_ROOT.'/system/templates';

    public function __construct($view)
    {

        $this->view = $view;
    }

    // render page
    public function show()
    {

        $view_file = false;

        $view = $this->view;

        // add assets
        $this->view->set('styles', $this->view->styles->make());
        $this->view->set('scripts', $this->view->scripts->make());

        // load template
        if (file_exists($file = $this->views_path.'/'.$this->view->name.'.php')) {
            $view_file = $file;
        }
        // else throw new Exception('Template '.$this->view->template.' not found');

        // access variables
        foreach ($this->view->variables as $var => $value) {
            ${$var} = $value;
        }

        // render
        ob_start();

        if ($view_file) {
            include_once $view_file;
        }

        $render = ob_get_clean();

        // $render = $this->loop($render, 'for');
        // $render = $this->loop($render, 'if');
        $render = $this->extend($render);
        // $render = $this->place_variables($render);

        echo $render;
    }

    private function extend($render)
    {

        $ext_tag = '{% extends ';

        // recover variabiles
        foreach ($this->view->variables as $var => $value) {
            ${$var} = $value;
        }

        $view = $this->view;

        // add template if the page is empty
        if (strpos($render, $ext_tag) === false) {
            $render = $ext_tag.$this->view->template.' %}'.$render;
        }

        // process templates
        while (strpos($render, $ext_tag) !== false) {

            preg_match_all('/'.$ext_tag.'(.*?) %}/', $render, $match);

            $template = $match[1][0];

            $blocks = $this->get_blocks($render);

            if (file_exists($file = $this->app_path.'/views/'.$template.'.php')) {
            } elseif (file_exists($file = $this->system_path.'/views/'.$template.'.php')) {
            }

            ob_start();

            include $file;

            $render = ob_get_clean();

            $render = $this->place_blocks($render, $blocks);
        }

        return $render;
    }

    private function get_blocks($render, $offset = 0)
    {

        $blocks = [];
        $names = [];

        $start_tag = '{% block ';
        $end_tag = '{% end ';
        $tag_limit = ' %}';

        $heads = explode($start_tag, $render);

        array_shift($heads);

        foreach ($heads as $head) {
            $names[] = explode($tag_limit, $head, 2)[0];
        }

        foreach ($names as $name) {

            $content = explode($start_tag.$name.$tag_limit, $render);

            $blocks[$name] = explode($end_tag.$name.$tag_limit, $content[1])[0];
        }

        return $blocks;
    }

    private function place_blocks($render, $blocks)
    {

        $names = [];

        $start_tag = '{% place ';
        $end_tag = '{% end ';
        $tag_limit = ' %}';

        $heads = explode($start_tag, $render);

        array_shift($heads);

        foreach ($heads as $head) {
            $names[] = explode($tag_limit, $head, 2)[0];
        }

        foreach ($names as $name) {

            $start = $start_tag.$name.$tag_limit;
            $end = $end_tag.$name.$tag_limit;

            $process = explode($start, $render);

            $before = $process[0];

            $process = explode($end, $process[1]);

            $after = $process[1];

            $content = $blocks[$name] ?? $process[0];

            $render = $before.$content.$after;
        }

        return $render;
    }

    /*private function place_variables($render) {

        //access variables
        foreach ($this->view->variables as $var => $value) ${$var} = $value;

        $start_tag = '{{ ';
        $end_tag = ' }}';

        while (strpos($render, '{{ ')) {

            $block = explode($start_tag, $render, 2);

            $before = $block[0];

            $block = explode($end_tag, $block[1], 2);

            $variable = $block[0];

            $after = $block[1];

            if ($variable[0] == '_') $value = constant($variable);
            else $value = $this->calc_var($variable);

            $render = $before.$value.$after;
        }

        return $render;
    }

    //calculate a variable
    private function calc_var($variable, $temp_val = null, $field = false) {

        //explode to get the variable data
        $variable = explode(' ', $variable);

        //get the possible operations
        $operation = $variable[1] ?? false;

        if ($operation == '>') {

            $value = call_user_func($variable[0], $variable[2]);

        } else {
            //canculate the value if not set yet
            if ($temp_val === null) {

                $field = $field ?: $this->view->variables;

                if (strpos($variable[0], '.')) {

                    $value_raw = explode('.', $variable[0]);

                    $value1 = $field[$value_raw[0]][$value_raw[1]];

                } else if (strpos($variable[0], '->')) {

                    $value_raw = explode('->', $variable[0]);

                    $value1 = $field[$value_raw[0]]->{$value_raw[1]};

                } else $value1 = $field[$variable[0]];

            //otherwise recover the value
            } else $value1 = $temp_val;

            //manage the operations
            switch ($operation) {

                case '+': $value = $value1 + $variable[2]; break;
                case '?': $value = $value1 ? $variable[2] : $variable[4]; break;

                default: $value = $value1; break;
            }
        }

        return $value;
    }*/

    /*private function loop($render, $loop) {

        $start_tag = '{% '.$loop.' ';
        $end_tag = ' %}';
        $loop_end = '{% end'.$loop.' %}';

        while (strpos($render, $start_tag)) {

            $result = '';

            //explode where the loop starts
            $block = explode($start_tag, $render, 2);

            //save the render previous to the loop
            $before = $block[0];

            //explode where the content starts
            $block = explode($end_tag, $block[1], 2);

            //save the loop data
            $loop_data = explode(' ', $block[0]);

            //explode where the loops ends
            $block = explode($loop_end, $block[1], 2);

            //save the content of the loop
            $content = $block[0];

            //save the render after the loop
            $after = $block[1];

            //if it's an if
            if ($loop == 'if') {

                $condition = explode('.', $loop_data[0]);

                $val = false;

                if (isset($condition[0])) $val = $this->calc_var($condition[0]);

                if ($val) $result = $content;
            }

            //if it's a for/freach
            else if ($loop == 'for') {

                $pholder_arr = $loop_data[0];
                $array = $this->view->variables[$loop_data[2]];

                foreach ($array as $key => $element) {

                    $cycle = $content;

                    //replace the loop placeholders
                    foreach ($element as $var => $value) {

                        $pholder = $pholder_arr.'.'.$var;

                        $cycle = str_replace('{{ '.$pholder.' }}', $this->calc_var($pholder, $value), $cycle);

                    }

                    //replace the key placeholders
                    preg_match_all('/\{\{\s(.*?)\s\}\}/', $cycle, $matches_raw);

                    $matches = $matches_raw[0];
                    $values = $matches_raw[1];

                    foreach ($matches as $i => $match) {

                        $cycle = str_replace($match, $this->calc_var($values[$i], $key), $cycle);
                    }

                    $result .= $cycle.PHP_EOL;
                }
            }

            $render = implode('', [$before, $result, $after]);
        }

        return $render;
    }*/
}

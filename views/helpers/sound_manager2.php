<?php
class SoundManager2Helper extends AppHelper {
    var $helpers = array("Html");

    function script($script, $options = array()) {
        $new_script = null;
        if (is_array($script)) {
            $new_script = array();
            foreach ($script as $current) {
                array_push($new_script, $this->get_script_path($current));
            }
        } else {
            $new_script = $this->get_script_path($script);
        }

        return $this->Html->script($new_script, $options);
    }

    function css($css, $rel = null, $options = array()) {
        $new_css = null;
        if (is_array($css)) {
            $new_css = array();
            foreach ($css as $current) {
                array_push($new_css, $this->get_css_path($current));
            }
        } else {
            $new_css = $this->get_css_path($css);
        }

        return $this->Html->css($new_css, $rel, $options);
    }

    function container($class="", $text="", $options=array()) {
        if (!isset($options["id"]))
            $options["id"] = "sm2-container";

        return $this->Html->div($class, $text, $options);
    }

    /**
     * $playlist expected to be an array of $song=>array("title"=>"", 
     *                                                   "link"=>"", 
     *                                                   "id"=>"",
     *                                                   "class"=>"").
     */
    function page_player($playlist, $html_attr=array()) {
        $songs = "";
        foreach ($playlist as $song) {
            if (!isset($song["class"]))
                $song["class"] = "";

            $song["class"] = "inline-playable $song[class]";

            $songs .= $this->Html->tag("li", 
                    $this->Html->link($song["title"], $song["link"], 
                            array("id"=>$song["id"], "class"=>$song["class"]))
            );
        }

        if (!isset($html_attr["class"]))
            $html_attr["class"] = "";

        $html_attr["class"] = "playlist $html_attr[class]";

        return $this->Html->tag("ul", $songs, $html_attr);
    }

    function init($options=array()) {
        $js = "";

        if (!isset($options["url"]))
            $options["url"] = "/sm2/swf";
        if (!isset($options["flashVersion"]))
            $options["flashVersion"] = 9;

        foreach ($options as $key => $val) {
            $js .= "soundManager.$key = ";

            if (is_bool($val)) {
                $js .= $val ? "true" : "false";
            } else if (is_numeric($val)) {
                $js .=  $val;
            } else {
                $js .= "\"$val\"";
            }

            $js .= ";\n";
        } 

        return $js;
    }

    function get_css_path($css) {
        if (substr($css, 0, 1) != "/") {
            $css = "/sm2/css/$css";
        }

        return $css;
    }

    function get_script_path($script) {
        if (substr($script, 0, 1) != "/") {
            $script = "/sm2/script/$script";
        }

        return $script;
    }

    function build_page_player($playlist, $sm_options=array(), $html_attr=array()) {
        $this->css("page-player", null, array("inline"=>false));
        $code = $this->script(array("soundmanager2-nodebug-jsmin", "page-player"));
        $code .= $this->Html->scriptBlock($this->init($sm_options), array("inline"=>true));
        $code .= $this->container("", "", array("style"=>"width: 1px; height: 1px"));
        return $code . $this->page_player($playlist, $html_attr);
    }
}

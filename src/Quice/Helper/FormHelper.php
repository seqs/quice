<?php

namespace Quice\Helper;

class FormHelper
{
    public $request = null;
    public $state = null;

    public function error($name)
    {
        $pattern = '<span class="form_message_error">%s</span>';
        if ($error = $this->state->getError($name)) {
            return sprintf($pattern, $error);
        } else {
            return '';
        }
    }

    public function file($name = '')
    {
        $pattern = '<input type="file" class="form_text form_small" name="%s" id="form_%s" value="" />';
        return sprintf($pattern, $name, $name);
    }

    public function input($name = '', $value = '', $size = 30)
    {
        $pattern = '<input name="%s" id="form_%s" value="%s" type="text" size="%d" class="form_text" />';
        $value = $this->request->getPost($name, $value);
        return sprintf($pattern, $name, $name, $value, $size);
    }

    public function password($name = '', $value = '', $size = 30)
    {
        $pattern = '<input name="%s" id="form_%s" value="%s" type="password" size="%d" class="form_text" />';
        $value = $this->request->getPost($name, $value);
        return sprintf($pattern, $name, $name, $value, $size);
    }

    public function checkbox($label = '', $name = '', $checked = false)
    {
        $pattern = '<input name="%s" id="form_%s" type="checkbox" value="yes"%s />'
            .'<label for="form_%s">%s</label>';
        $request = $this->request;
        if (($request->isPost() && $request->getPost($name)) || (!$request->isPost() && $checked)) {
            $value = ' checked="checked"';
        } else {
            $value = '';
        }
        return sprintf($pattern, $name, $name, $value, $name, $label);
    }

    public function submit($text = '', $class = '')
    {
        $pattern = '<input class="form_submit%s" type="submit" title="%s" value="%s" />';
        return sprintf($pattern, ' '.$class, $text, $text);
    }

    public function reset($text = '', $class = '')
    {
        $pattern = '<input class="form_button%s" type="reset" title="%s" value="%s" />';
        return sprintf($pattern, ' '.$class, $text, $text);
    }

    public function button($text = '', $class = '')
    {
        $pattern = '<input class="form_button%s" type="submit" title="%s" value="%s" />';
        return sprintf($pattern, ' '.$class, $text, $text);
    }

    public function textarea($name = '', $value = '')
    {
        $pattern = '<textarea name="%s" id="form_%s" rows="" cols="" class="form_text">%s</textarea>';
        $value = $this->request->getPost($name, $value);
        return sprintf($pattern, $name, $name, $value);
    }

    public function select($name = '', $value = '', $options = array(), $style="")
    {
        $value = $this->request->getPost($name, $value);
        $html = sprintf('<select name="%s" id="form_%s" class="%s">', $value, $value, $style);
        $html .= '<option>-</option>';
        foreach ($options as $k => $v) {
            $selected = ($value == $k) ? ' selected="selected"' : '';
            $html .= sprintf('<option value="%s">%s</option>', $k, $selected, $v);
        }
        $html .= '</select>';
        return $html;
    }

    public function label($text, $required = false)
    {
        if ($required) {
            $pattern = '<label class="form_label">%s <em class="form_required">*</em></label>';
        } else {
            $pattern = '<label class="form_label">%s</label>';
        }

        return sprintf($pattern, $text);
    }
}

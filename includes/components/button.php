<?php
function render_button($label, $type = 'button', $class = 'btn-primary', $attributes = []) {
    $attr_str = '';
    foreach ($attributes as $key => $value) {
        $attr_str .= " $key=\"" . htmlspecialchars($value) . "\"";
    }
    
    if ($type === 'link') {
        $href = $attributes['href'] ?? '#';
        unset($attributes['href']);
        echo "<a href=\"$href\" class=\"btn $class\"$attr_str>$label</a>";
    } else {
        echo "<button type=\"$type\" class=\"btn $class\"$attr_str>$label</button>";
    }
}
?>

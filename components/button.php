<?php
/**
 * Button Component
 * Reusable button với các style khác nhau
 * 
 * @param string $text - Button text
 * @param string $type - Button type: primary, secondary, outline
 * @param string $href - Link URL (optional)
 * @param string $class - Additional CSS classes
 */
if (!isset($text)) $text = 'Button';
if (!isset($type)) $type = 'primary';
if (!isset($class)) $class = '';

$buttonClass = "btn btn-{$type} {$class}";
$buttonText = e($text);

if (isset($href)) {
    echo "<a href=\"{$href}\" class=\"{$buttonClass}\">{$buttonText}</a>";
} else {
    echo "<button type=\"button\" class=\"{$buttonClass}\">{$buttonText}</button>";
}
?>

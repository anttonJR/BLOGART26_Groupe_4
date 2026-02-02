<?php
/**
 * Convertit le BBCode en HTML
 * 
 * @param string $text Texte avec BBCode
 * @return string Texte HTML
 */
function bbcode_to_html($text) {
    if (empty($text)) {
        return '';
    }
    
    // Tableau des conversions
    $bbcode = [
        // Gras
        '/\[b\](.*?)\[\/b\]/is' => '<strong>$1</strong>',
        
        // Italique
        '/\[i\](.*?)\[\/i\]/is' => '<em>$1</em>',
        
        // Souligné
        '/\[u\](.*?)\[\/u\]/is' => '<u>$1</u>',
        
        // Lien
        '/\[url=(.*?)\](.*?)\[\/url\]/is' => '<a href="$1" target="_blank">$2</a>',
        '/\[url\](.*?)\[\/url\]/is' => '<a href="$1" target="_blank">$1</a>',
        
        // Ancre
        '/\[anchor\](.*?)\[\/anchor\]/is' => '<a id="$1"></a>',
        
        // Lien vers ancre
        '/\[goto=(.*?)\](.*?)\[\/goto\]/is' => '<a href="#$1">$2</a>',
        
        // Emoji
        '/\:smile\:/i' => '😊',
        '/\:wink\:/i' => '😉',
        '/\:heart\:/i' => '❤️',
        '/\:star\:/i' => '⭐',
    ];
    
    // Application des conversions
    foreach ($bbcode as $pattern => $replacement) {
        $text = preg_replace($pattern, $replacement, $text);
    }
    
    // Conversion des retours à la ligne
    $text = nl2br($text);
    
    return $text;
}

/**
 * Échappe les caractères HTML mais préserve le BBCode
 * 
 * @param string $text Texte à échapper
 * @return string Texte échappé
 */
function bbcode_escape($text) {
    // On échappe tout sauf les balises BBCode
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    return $text;
}
?>
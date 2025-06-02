<?php

class Library {

    public static function makeLinksClickable($text) {
        $text = htmlspecialchars($text); // Protege contra XSS
        $text = nl2br($text);            // Mantém as quebras de linha
        
        // Expressão regular que detecta URLs (http, https, www)
        $pattern = '/(https?:\/\/[^\s<]+|www\.[^\s<]+)/i';
        
        // Callback para transformar em link clicável
        $callback = function ($matches) {
            $url = $matches[0];
            // Se começar com www, adiciona http://
            $href = (preg_match('/^www\./i', $url)) ? 'https://' . $url : $url;
            return '<a href="' . $href . '" target="_blank" rel="noopener noreferrer">' . $url . '</a>';
        };

        // Aplica o regex e faz a substituição
        return preg_replace_callback($pattern, $callback, $text);
    }

}
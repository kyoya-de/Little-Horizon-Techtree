<?php
class TechTree_Bbcode_Smilies
{
    /**
     * @var array
     */
    private static $_smileyList = null;

    /**
     * Returns a list of available smileys.
     *
     * @static
     *
     * @return array
     */
    public static function getSmileyList()
    {
        if (self::$_smileyList === null) {
            $basePath          = 'smilies/';
            self::$_smileyList = array(
                ":lol:"     => $basePath . "icon_lol.gif",
                ":oops:"    => $basePath . "icon_redface.gif",
                ":cry:"     => $basePath . "icon_cry.gif",
                ":evil:"    => $basePath . "icon_evil.gif",
                ":twisted:" => $basePath . "icon_twisted.gif",
                ":roll:"    => $basePath . "icon_rolleyes.gif",
                ":wink:"    => $basePath . "icon_wink.gif",
                ":idea:"    => $basePath . "icon_idea.gif",
                ":arrow:"   => $basePath . "icon_arrow.gif",
                ":!:"       => $basePath . "icon_exclaim.gif",
                ":?:"       => $basePath . "icon_question.gif",
                ":mrgreen:" => $basePath . "icon_mrgreen.gif",
                ":D"        => $basePath . "icon_biggrin.gif",
                ":)"        => $basePath . "icon_smile.gif",
                ":("        => $basePath . "icon_sad.gif",
                ":o"        => $basePath . "icon_surprised.gif",
                ":?"        => $basePath . "icon_confused.gif",
                "8)"        => $basePath . "icon_cool.gif",
                ":x"        => $basePath . "icon_mad.gif",
                ":P"        => $basePath . "icon_razz.gif",
                ";)"        => $basePath . "icon_wink.gif",
                ":|"        => $basePath . "icon_neutral.gif",
            );
        }

        return self::$_smileyList;
    }
}

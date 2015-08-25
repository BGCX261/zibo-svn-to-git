<?php

namespace zibo\jquery\jplayer\view;

use zibo\core\Zibo;

use zibo\jquery\jplayer\Module as JPlayerModule;
use zibo\jquery\Module as JQueryModule;

use zibo\library\smarty\view\SmartyView;

/**
 * View for a JPlayer instance
 */
class JPlayerView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'jquery/jplayer';

    /**
     * Path to the CSS style of this view
     * @var string
     */
    const STYLE = 'web/styles/jquery/jplayer.css';

    /**
     * Constructs a new JPlayer view
     * @param string $file Path or URL to the media file
     * @param string $id Style id for the container
     * @return null
     */
    public function __construct($file, $id = null) {
        parent::__construct(self::TEMPLATE);

        if (!$id) {
            $id = md5(microtime());
        }

//        $file = 'http://localhost/kayalion/sian/public/sample.mp3';

        $this->set('playerId', $id);

        $this->addStyle(JQueryModule::STYLE_JQUERY_UI);
        $this->addStyle(self::STYLE);

        $this->addJavascript(JQueryModule::SCRIPT_JQUERY);
        $this->addJavascript(JQueryModule::SCRIPT_JQUERY_UI);
        $this->addJavascript(JPlayerModule::SCRIPT_JPLAYER);

        $js = '$("#' . $id . '").jPlayer( {';
        $js .= 'customCssIds: true, ';
        $js .= 'nativeSupport: false, ';
        $js .= 'ready: function () { this.element.jPlayer("setFile", "' . $file . '"); }, ';
        $js .= 'swfPath: "' . $this->getSwfPath() . '"';
        $js .= '});';

        $js .= "var global_lp = 0;

    $('#" . $id . "').jPlayer('onProgressChange', function(lp,ppr,ppa,pt,tt) {
        var lpInt = parseInt(lp);
        var ppaInt = parseInt(ppa);
        global_lp = lpInt;

        $('#" . $id . "-loader-playback').progressbar('option', 'value', lpInt);
        $('#" . $id . "-slider-playback').slider('option', 'value', ppaInt);
    })

    $('#" . $id . "-pause').hide();

    function showPauseBtn() {
        $('#" . $id . "-play').fadeOut(function(){
            $('#" . $id . "-pause').fadeIn();
        });
    }

    function showPlayBtn() {
        $('#" . $id . "-pause').fadeOut(function(){
            $('#" . $id . "-play').fadeIn();
        });
    }

    function playTrack(t, n) {
        $('#" . $id . "').jPlayer('setFile', t).jPlayer('play');

        showPauseBtn();

        return false;
    }

    $('#" . $id . "-play').click(function() {
        $('#" . $id . "').jPlayer('play');
        showPauseBtn();
        return false;
    });

    $('#" . $id . "-pause').click(function() {
        $('#" . $id . "').jPlayer('pause');
        showPlayBtn();
        return false;
    });

    $('#" . $id . "-stop').click(function() {
        $('#" . $id . "').jPlayer('stop');
        showPlayBtn();
        return false;
    });

    $('#" . $id . "-volume-min').click( function() {
        $('#" . $id . "').jPlayer('volume', 0);
        $('#" . $id . "-slider-volume').slider('option', 'value', 0);
        return false;
    });

    $('#" . $id . "-volume-max').click( function() {
        $('#" . $id . "').jPlayer('volume', 100);
        $('#" . $id . "-slider-volume').slider('option', 'value', 100);
        return false;
    });

    // Slider
    $('#" . $id . "-slider-playback').slider({
        max: 100,
        range: 'min',
        animate: true,

        slide: function(event, ui) {
            $('#" . $id . "').jPlayer('playHead', ui.value*(100.0/global_lp));
        }
    });

    $('#" . $id . "-slider-volume').slider({
        value : 80,
        max: 100,
        range: 'min',
        animate: true,

        slide: function(event, ui) {
            $('#" . $id . "').jPlayer('volume', ui.value);
        }
    });

    $('#" . $id . "-loader-playback').progressbar();


    //hover states on the static widgets
    $('#dialog_link, #" . $id . "Container ul.icons li').hover(
        function() { $(this).addClass('ui-state-hover'); },
        function() { $(this).removeClass('ui-state-hover'); }
    );
        ";

        $this->addInlineJavascript($js);
    }

    /**
     * Renders this view
     * @param boolean $return
     * @return null|string
     */
    public function render($return = true) {
        return parent::render($return);
    }

    /**
     * Gets the path of the SWF file
     * @return string
     */
    private function getSwfPath() {
        $request = Zibo::getInstance()->getRequest();
        $baseUrl = $request->getBaseUrl();
        $swfUrl = $baseUrl . '/' . JPlayerModule::PATH_SWF;

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $host = 'https://';
        } else {
            $host = 'http://';
        }

        if (!isset($_SERVER['SERVER_NAME'])) {
            return null;
        }
        $host .= $_SERVER['SERVER_NAME'];

        $port = $_SERVER['SERVER_PORT'];
        if (!empty($port) && $port != 80) {
            $host .=  ':' . $port;
        }

        return str_replace($host, '', $swfUrl);
    }

}
<?php

namespace zibo\library\bbcode;

use zibo\library\google\Youtube;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class BBCodeParserTest extends BaseTestCase {

    public function testConstructWithoutArgumentsSetDefaultTags() {
        $bbcodeParser = new BBCodeParser();

        $tags = Reflection::getProperty($bbcodeParser, 'tags');

        $this->assertFalse(empty($tags));
    }

	/**
     * @dataProvider providerParse
	 */
	public function testParse($expected, $test) {
	    $bbcodeParser = new BBCodeParser();
        $result = $bbcodeParser->parse($test);

        $this->assertEquals($expected, $result);
	}

	public function providerParse() {
		$youtubeUrl = 'http://www.youtube.com/watch?v=Kg1oDmxLP1g';

		$youtube = new Youtube($youtubeUrl);
		$youtubeHtml = $youtube->getHtml();

	    $tests = array(
            array('I would like to <strong>emphasize</strong> this', 'I would like to [b]emphasize[/b] this'),
            array('Making text <em>italic</em> italic is kind of easy', 'Making text [i]italic[/i] italic is kind of easy'),
            array('u is used for the <span style="text-decoration: underline;">underline</span> tag', 'u is used for the [u]underline[/u] tag'),
            array('<span style="text-decoration: line-through;">Striking</span> through some test is done with s', '[s]Striking[/s] through some test is done with s'),
            array('sub is used for <sub>subscript</sub> and sup is used for <sup>superscript</sup>', 'sub is used for [sub]subscript[/sub] and sup is used for [sup]superscript[/sup]'),
            array('I <span style="text-decoration: line-through;">had been</span> was born in Denmark', 'I [s]had been[/s] was born in Denmark'),
            array('It <span style="font-size: 14px;">g</span><span style="font-size: 18px;">r</span><span style="font-size: 22px;">o</span><span style="font-size: 26px;">o</span><span style="font-size: 28px;">w</span><span style="font-size: 32px;">s</span>!!!', 'It [size=14]g[/size][size=18]r[/size][size=22]o[/size][size=26]o[/size][size=28]w[/size][size=32]s[/size]!!!'),
            array('It is possible to colour the text <span style="color: red;">red</span> <span style="color: green;">green</span> <span style="color: blue;">blue</span> - or <span style="color: #DB7900;">whatever</span>', 'It is possible to colour the text [color=red]red[/color] [color=green]green[/color] [color=blue]blue[/color] - or [color=#DB7900]whatever[/color]'),
            array('This is some text<div style="text-align: center;">This is some centered text</div>', 'This is some text[center]This is some centered text[/center]'),
            array('Quoting noone in particular<div class="quote">\'Tis be a bad day</div>Quoting someone in particular<div class="quote"><strong>Bjarne:</strong> This be the day of days!</div>', 'Quoting noone in particular[quote]\'Tis be a bad day[/quote]Quoting someone in particular[quote=Bjarne]This be the day of days![/quote]'),
            array('You can list items: <ul><li>Item 1</li><li>Item 2</li><li>...</li></ul>', 'You can list items: [list][*]Item 1[*]Item 2[*]...[/list]'),
            array("You can list items:<br />\n<ul><li>Item 1</li><li>Item 2</li><li><strong>...</strong></li></ul>", "You can list items:\n[list]\n[*]Item 1\n[*]Item 2\n[*][b]...[/b]\n[/list]"),
            array('Linking with no link title: <a href="http://www.bbcode.org/" rel="nofollow" target="_blank">http://www.bbcode.org/</a>, Linking to a named site: <a href="http://www.bbcode.org/" rel="nofollow" target="_blank">This be bbcode.org!</a>', 'Linking with no link title: [url]http://www.bbcode.org/[/url], Linking to a named site: [url=http://www.bbcode.org/]This be bbcode.org![/url]'),
            array('Insert some youtube movie: ' . $youtubeHtml, 'Insert some youtube movie: [youtube]' . $youtubeUrl . '[/youtube]'),
            array('[youtube]invalid youtube[/youtube]', '[youtube]invalid youtube[/youtube]'),
            array('<strong>Oopsie:[i]</strong> we have some misplaced tags[/i]', '[b]Oopsie:[i][/b] we have some misplaced tags[/i]'),
            array('strip &lt;span&gt;tags&lt;/span&gt; test', "strip <span>tags</span> test"),
            array("multiline<br />\n<br />\ntest", "multiline\n\ntest"),
        );

	    return $tests;
	}

}
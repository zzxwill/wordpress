<?php

class Preserve_Code_Formatting_Test extends WP_UnitTestCase {

	static function setUpBeforeClass() {
		add_filter( 'pcf_text', array( c2c_PreserveCodeFormatting::get_instance(), 'preserve_preprocess' ), 2 );
		add_filter( 'pcf_text', array( c2c_PreserveCodeFormatting::get_instance(), 'preserve_postprocess_and_preserve' ), 100 );
	}

	function setUp() {
		parent::setUp();
		$this->set_option();
	}



	/**
	 *
	 * DATA PROVIDERS
	 *
	 */



	public static function get_preserved_tags( $more_tags = array() ) {
		return array(
			array( 'code' ),
			array( 'pre' ),
		);
	}

	public static function get_default_filters() {
		return array(
			array( 'the_content' ),
			array( 'the_excerpt' ),
		);
	}



	/**
	 *
	 * HELPER FUNCTIONS
	 *
	 */



	private function set_option( $settings = array() ) {
		$defaults = array(
			'preserve_tags'              => array( 'code', 'pre' ),
			'preserve_in_posts'          => true,
			'preserve_in_comments'       => true,
			'wrap_multiline_code_in_pre' => true,
			'use_nbsp_for_spaces'        => true,
			'nl2br'                      => false,
		);
		$settings = wp_parse_args( $settings, $defaults );
		c2c_PreserveCodeFormatting::get_instance()->update_option( $settings, true );
	}

	private function preserve( $text, $filter = 'pcf_text' ) {
		return apply_filters( $filter, $text );
	}



	/**
	 *
	 * TESTS
	 *
	 */



	/**
	 * @dataProvider get_preserved_tags
	 */
	function test_html_tags_are_preserved_in_preserved_tag( $tag ) {
		$code = '<strong>bold</strong> other markup <i>here</i>';
		$text = "Example <code>$code</code>";

		$this->assertEquals(
			'Example <code>' . htmlspecialchars( $code, ENT_QUOTES ) . '</code>',
			$this->preserve( $text )
		);
	}

	/**
	 * @dataProvider get_preserved_tags
	 */
	function test_special_characters_are_preserved_in_preserved_tag( $tag ) {
		$code = "first\r\nsecond\rthird\n\n\n\n\$fourth\nfifth<?php test(); ?>";
		$text = "Example <code>$code</code>";
		$expected_code = "first\nsecond\nthird\n\n\$fourth\nfifth&lt;?php test(); ?&gt;";

		$this->assertEquals(
			'Example <pre><code>' . $expected_code . '</code></pre>',
			$this->preserve( $text )
		);
	}

	/**
	 * @dataProvider get_preserved_tags
	 */
	function test_tabs_are_replaced_in_preserved_tag( $tag ) {
		$code = "\tfirst\n\t\tsecond";
		$text = "Example <code>$code</code>";

		$this->assertEquals(
			'Example <pre><code>' . str_replace( "\t", "&nbsp;&nbsp;", $code ) . '</code></pre>',
			$this->preserve( $text )
		);
	}

	/**
	 * @dataProvider get_preserved_tags
	 */
	function test_spaces_are_preserved_in_preserved_tag( $tag ) {
		$text = "Example <$tag>preserve  multiple  spaces</$tag>";

		$this->assertEquals(
			"Example <$tag>preserve&nbsp;&nbsp;multiple&nbsp;&nbsp;spaces</$tag>",
			$this->preserve( $text )
		);
	}

	function test_spaces_are_not_preserved_in_unhandled_tag() {
		$tag = 'strong';
		$text = "Example <$tag>preserve  multiple  spaces</$tag>";

		$this->assertEquals( $text, apply_filters( 'pcf_text', $text ) );
	}

	/**
	 * @dataProvider get_preserved_tags
	 */
	function test_space_is_not_replaced_with_nbsp_if_false_for_setting_use_nbsp_for_spaces( $tag ) {
		$this->set_option( array( 'use_nbsp_for_spaces' => false ) );

		$text = "Example <$tag>preserve  multiple  spaces</$tag>";

		$this->assertEquals( $text, $this->preserve( $text ) );
	}

	function test_multiline_code_gets_wrapped_in_pre() {
		$text = "<code>some code\nanother line\n yet another</code>";

		$this->assertEquals( "Example <pre>$text</pre>", $this->preserve( 'Example ' . $text ) );
	}

	function test_multiline_pre_does_not_get_wrapped_in_pre() {
		$text = "Example <pre>some code\nanother line\n yet another</pre>";

		$this->assertEquals( $text, $this->preserve( $text ) );
	}

	function test_multiline_code_not_wrapped_in_pre_if_setting_wrap_multiline_code_in_pre_is_false() {
		$this->set_option( array( 'wrap_multiline_code_in_pre' => false ) );

		$text = "Example <code>some code\nanother line\n yet another</code>";

		$this->assertEquals( $text, $this->preserve( $text ) );
	}

	function test_nl2br_setting() {
		$this->set_option( array( 'nl2br' => true ) );

		$text = "<code>some code\nanother line\n yet another</code>";

		$this->assertEquals( str_replace( "\n", "<br />\n", "Example <pre>$text</pre>" ), $this->preserve( 'Example ' . $text ) );
	}

	function test_code_preserving_honors_setting_preserve_tags() {
		$this->set_option( array( 'preserve_tags' => array( 'pre', 'strong' ) ) );
		$text = "<TAG>preserve  multiple  spaces</TAG>";

		// 'code' typically is preserved, but the setting un-does that
		$t = str_replace( 'TAG', 'code', $text );
		$this->assertEquals( $t, $this->preserve( $t ) );

		// it should now handle 'strong'
		$t = str_replace( 'TAG', 'strong', $text );
		$this->assertEquals( str_replace( ' ', '&nbsp;', $t ), $this->preserve( $t ) );
	}

	/**
	 * @dataProvider get_default_filters
	 */
	function test_filters_default_filters( $filter ) {
		$code = '<strong>bold</strong> other markup <i>here</i>';
		$text = "Example <code>$code</code>";

		$this->assertEquals(
			wpautop( 'Example <code>' . htmlspecialchars( $code, ENT_QUOTES ) . '</code>' ),
			$this->preserve( $text, $filter )
		);
	}

}

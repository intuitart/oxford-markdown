<?php
/**
 * Plugin Name: Oxford Markdown
 * Plugin URI:  http://oxfordframework.com
 * Description: A plugin to parse gutenberg markdown code blocks into html
 * Version:     1.0.0
 * Date:        20 Jun 2019
 * Author:      Andrew Patterson
 * Author URI:  http://pattersonresearch.ca
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

// abort if accessed directly
defined( 'ABSPATH' ) || die;
// if ( ! defined( 'WPINC' ) ) {
//     die;
// }

class oxford_markdown {

  private $trigger_class = 'mdToHtml';

  private function __construct() {
    add_action( 'init', array( $this, 'init' ) );
    add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
    add_filter( 'pre_render_block', array( $this, 'pre_render_block' ), 10, 2 );
    add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
    add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
  }

  public static function instance() {
    static $instance = null;
    if ( $instance == null ) {
          $instance = new self;
      }
      return $instance;
  }

  // init action
  public function init() {
    add_shortcode( 'md_url', array( $this, 'md_url' ) );
  }

  // init action
  public function md_url( $atts ) {
    $atts = array_change_key_case( (array)$atts, CASE_LOWER );
    extract( shortcode_atts(
			array(
				'url'		=> '',
				'tag'		=> '',
        'id'    => '',
				'class'	=> '',
			),
			$atts,
			'md_url'
		) );

    $content = '';
    if ( !empty ( esc_url( $url ) ) ) {
      $response = wp_remote_request( esc_url( $url ) );
      $content = wp_remote_retrieve_body($response);
      $parser = new ParsedownExtra();
      $content = $parser->text( $content );
      $content = sprintf(
        '<%1$s id="%2$s" class="%3$s">%4$s</%1$s>',
        empty( esc_attr( $tag ) ) ? 'div' : esc_attr( $tag ),
        esc_attr( $id ),
        'mdToHtml ' . esc_attr( $class ),
        $content
      );
    }

    return $content;
  }

  // plugins_loaded action
  public function plugins_loaded() {
    if ( ! class_exists( 'Parsedown' ) ) {
      require_once( plugin_dir_path( __FILE__ ) . 'vendor/parsedown/Parsedown.php');
      require_once( plugin_dir_path( __FILE__ ) . 'vendor/parsedown-extra/ParsedownExtra.php');
    }
  }

  // enqueue front end bits
  public function wp_enqueue_scripts() {
    wp_enqueue_script(
      'oxford-markdown-script',
      plugins_url( 'frontend.js', __FILE__ ),
      array( 'jquery' ),
      filemtime( plugin_dir_path( __FILE__ ) . 'frontend.js' )
    );
  }

  // enqueue block editor bits
  public function enqueue_block_editor_assets() {
    wp_enqueue_script(
      'oxford-markdown-script',
      plugins_url( 'blocks.js', __FILE__ ),
      array( 'wp-blocks' ),
      filemtime( plugin_dir_path( __FILE__ ) . 'blocks.js' )
    );
  }

  // parse block for markdown
  public function pre_render_block( $pre_render, $block ) {
    $debug_log = '/var/www/debug/debug.log';
    $results = print_r( $block, true );
    file_put_contents( $debug_log, '$block:' . PHP_EOL, FILE_APPEND );
    file_put_contents( $debug_log, $results, FILE_APPEND );

    // content to parse
    if( is_null( $pre_render ) ) {
      $content = trim( $block['innerHTML'] );
    } else {
      $content = trim( $pre_render );
    }

    // init
    $block_class = '';

    // legacy content, or classic editor
    if ( empty( $blocks['blockName'] ) ) {

      // parse all legacy and classic editor content
      if ( apply_filters( 'oxford-markdown-enable-legacy', false ) ) {
        $block_class = 'core-classic-editor';
      }

      // parse legacy and classic editor content that begins with a '#'
      elseif ( apply_filters( 'oxford-markdown-controlled-legacy', false ) ) {
        $line = rtrim( strtok($content, PHP_EOL) );
        if ( $line[0] == '#' ) {
          if ( strlen( $line ) == 1 ) {
            $pos = strpos( $content, '#' );
            $content = ltrim( substr( $content, $pos + 1 ) );
          }
          $block_class = 'core-classic-editor';
        }
      }
    }

    // any block except legacy/classic editor
    elseif ( false !== strpos( $block['attrs']['className'], $this->trigger_class ) ) {
      $del = chr( 127 );
      $content = preg_replace(
        $del . '^<.+?>(<.+?>)?(<.+?>)?(.*?)(</.+?>)?(</.+?>)?</.+?>$' . $del . 's',
        '${3}',
        $content
      );
      $block_class = str_replace( '/', '-', $block['blockName'] );
    }

    // parse, wrap
    if ( !empty( $block_class ) ) {
      $parser = new ParsedownExtra();
      $content = $parser->text( $content );
      $pre_render = sprintf( '<div class="%1$s %2$s">%3$s</div>', $this->trigger_class, $block_class, $content );
    }

    return $pre_render;
  }

} // __CLASS__ oxford_markdown

oxford_markdown::instance();

// filter to enable parsing of all legacy and classic editor content
// add_filter( 'oxford-markdown-enable-legacy', '__return_true' );

// filter to enable controlled parsing of legacy and classic editor content
add_filter( 'oxford-markdown-controlled-legacy', '__return_true' );

// filter to disable block editor and fallback to classic editor
// add_filter('use_block_editor_for_post', '__return_false');

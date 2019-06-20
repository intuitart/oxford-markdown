# Oxford Markdown (README)

Plugin: [Oxford Markdown](https://www.oxfordframework.com/oxford-markdown)  
Contributors: intuitart  
Tags: gutenberg, classic editor, markdown, html, parse, parsedown  
Author: [Andrew Patterson](https://www.pattersonresearch.ca/?target=_blank)  
Plugin URL: [https://github.com/intuitart/oxford-markdown](https://github.com/intuitart/oxford-markdown?target=_blank)  
Requires at least: 5.0  
Tested up to: 5.2.1  
Stable tag: 1.0.0  
Version: 1.0.0  
License: GPLv3 or later  
License URL: [https://www.gnu.org/licenses/gpl-3.0.txt](https://www.gnu.org/licenses/gpl-3.0.txt?target=_blank)  

Want to write in markdown? We parse Gutenberg markdown blocks into html.

## Description

This plugin looks for Gutenberg blocks captured as markdown and parses the content into html for display. So you can write in markdown knowing that your content will be displayed as html content.

It works at the Gutenberg block level, and only blocks identified as needing conversion will be affected.

We also have a markdown shortcode that allows you to load a markdown file from an external site, and display it on your page. This is particularly useful when you want to display a `readme.md`, or other markdown file, on your website when the original is somewhere else (e.g. github).

## Usage

The default usage is to convert gutenberg blocks captured as markdown into html for display. Here are the steps:

1. Install and activate the plugin.

1. Using one of the recognized Gutenberg block code editors, choose `md->html` as the block style. This is usually available in the Styles dropdown to the right of the editor or in the **Change block types or style** dropdown to left above the editor.

1. Enter your markdown into your chosen block code editor.

1. Update or Publish your page when finished.

That's it. When you view your page, the markdown code block with the block style `md->html` will be parsed into html from markdown.

Code block editors that have been tested and work include:
- inbuilt Wordpress block code editor
- [Code Editor Blocks](https://wordpress.org/plugins/code-editor-blocks/?target=_blank)
- [Simple Code Block](https://wordpress.org/plugins/simple-code-block/?target=_blank) with Ace Editor
- [CodeMirror Blocks](https://wordpress.org/plugins/wp-codemirror-block/?target=_blank)

All the above except the Wordpress code editor also allow you to select markdown as the type of code you are working with. That is useful as it highlights syntax while editing. But you must add the `md->html` style for it to be parsed into html by this plugin, when viewed.

To show actual markdown code on your page, just leave the style as `default`, and this plugin will do nothing with it.

Code block editors that do not work include:
- [Enlighter](https://wordpress.org/plugins/enlighter/?target=_blank)

If a code editor is not explicitly supported, but it allows you to add custom classes, add the class `mdToHtml`. This is usually available under the **Advanced** drop down when editing a block. That's all it takes to trigger the block to have it's markdown parsed into html as it is displayed.

## Classic Editor

Blocks identified as coming from the classic editor can also be parsed as markdown. This may be useful to you if you find yourself in one of these situations:

- you have a Wordpress site where content is currently stored as Markdown
- you want to disable Gutenberg and use the Classic Editor
- you want to use Gutenberg and edit with the Code Editor instead of the Visual Editor

Internally Gutenberg implements all of these as a classic-editor block. To use, here are the steps:

1. Add one of the following to your functions.php file in your theme folder:
```php
add_filter( 'oxford-markdown-enable-legacy', '__return_true' );
add_filter( 'oxford-markdown-controlled-legacy', '__return_true' );
```
1. Install and activate the plugin.

1. Select Code Editor instead of Visual Editor when editing a document.

1. Enter your markdown into the page code editor.

1. Update or Publish your page when finished.

Now when you view your page the entire content will be parsed into html from markdown.

The difference between the two filters, in a nutshell, is that the first will parse all classic editor content, while the second will parse all classic editor content where the first character found is a `#` character.

When using the controlled filter, and the first character in your content is not a `#` for a heading, add a single `#` all by itself on it's own line. Like this:
```
#
Let's introduce this page with a paragraph before we have our first heading.
# heading
Now we'll have some more content.
```
The following is how the above markdown code will appear after being parsed into html. Notice that the `#` character on the first line is excluded.

  > Let’s introduce this page with a paragraph before we have our first heading.
  > #heading
  > Now we’ll have some more content.

## Markdown URL

To display markdown from an external url, we use a Wordpress shortcode. It's simple. You need the shortcode name `md_url` and your source url. Here is an example:
```
[md_url url="https://raw.githubusercontent.com/intuitart/oxford-markdown/master/readme.md"]
```
This will display the latest version of this document. In fact, if you are reading this on my website, that's where it's coming from.

Assuming you are using the visual editor in Gutenberg, add a shortcode block where you want the text from your external markdown file to appear. Otherwise, enter the shortcode directly in your post content in the classic editor.

There are some additional parameters, should you need them. You can add an `id` to the wrapper, add an extra `class`, or specify the `tag` to be used.
```
[md_url url="https://raw.githubusercontent.com/intuitart/oxford-markdown/master/readme.md" id="readme" class="md-class1 md-class2 " tag="section"]
```
The only required parameter is the `url` parameter. If not specified, the `id` attribute for the wrapper will be empty. The `class` will always include `mdToHtml` along with any additional classes you add. And the default `tag` is `div`, unless you specify otherwise.

## Open new tab
We've slipped in one more little snippet. Some of us like to open a new browser tab when a user clicks on an external link on our page. Markdown doesn't support a syntax for this out of the box.

What you can do here is add a `target=_blank` parameter to your link. When the page is loaded we'll convert that to a `target="_blank"` attribute. For example,
```
[Code Editor Blocks](https://wordpress.org/plugins/code-editor-blocks/?target=_blank)
```
becomes
```
<a href="https://wordpress.org/plugins/code-editor-blocks/" target="_blank">Code Editor Blocks</a>
```

Because it's a legitimate url parameter it passes through markdown parsers. If your html is not displayed with this plugin, the parameter will remain and have no affect, but it should do no harm as websites generally ignore unexpected parameters.

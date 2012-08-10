<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php sandbox_blog_lang(); ?>>
<head profile="http://gmpg.org/xfn/11">
	<meta charset="<?php bloginfo( 'charset' ); ?>" />

	<title><?php bloginfo('name'); if ( is_404() ) : _e(' &raquo; ', 'sandbox'); _e('Not Found', 'sandbox'); elseif ( is_home() ) : _e(' &raquo; ', 'sandbox'); bloginfo('description'); else : wp_title(); endif; ?></title>
	<meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
	<meta name="description" content="<?php bloginfo('description') ?>" />
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url') ?>" title="<?php echo esc_html(get_bloginfo('name'), 1) ?> <?php _e('Posts RSS feed', 'sandbox'); ?>" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="<?php echo esc_html(get_bloginfo('name'), 1) ?> <?php _e('Comments RSS feed', 'sandbox'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url') ?>" />
	<?php if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' ); ?>
	<link rel="icon" type="image/png" href="<?php echo esc_url(sigf_option('favi'))?>">

<?php 
	wp_head();	

	echo html_entity_decode(sigf_option('head_tracker')); 
?>

</head>

<body class="<?php sandbox_body_class() ?>">

<div id="wrapper" class="hfeed">

	<div id="header">
		<ul id="pages">
			<?php wp_list_pages('title_li=&sort_column=post_title&sort_order=desc&depth=1' ) ?>
		</ul>
		<?php if(sigf_option('logo')) :?>
			<h1><a class="blog-title" href="<?php echo esc_url(get_option('home')) ?>/" title="<?php bloginfo('name') ?>" rel="home"><img id='main_logo' alt = "<?php bloginfo('name') ?>" src="<?php echo esc_url(sigf_option('logo')) ?>"/></a><span> | <?php bloginfo('description') ?></span></h1>
		<?php else : ?>
			<h1><a class="blog-title" href="<?php echo esc_url(get_option('home')) ?>/" title="<?php bloginfo('name') ?>" rel="home"><?php bloginfo('name') ?></a><span> | <?php bloginfo('description') ?></span></h1>
		<?php endif; ?>
		<div id="blog-description"></div>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>
	</div><!--  #header -->

	<div id="access">
		<div class="skip-link"><a href="#content" title="<?php _e('Skip navigation to the content', 'sandbox'); ?>"><?php _e('Skip to content', 'sandbox'); ?></a></div>
	</div><!-- #access -->

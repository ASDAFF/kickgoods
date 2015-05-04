<? extract($GLOBALS["arInclude"]); ?>

<ul class="blog-article-meta accent-text">

	<li class="blog-article-meta-item blog-article-meta-share">
		<span></span>
		<a href="<?=$DETAIL_PAGE_URL;?>" class="blog-article-share">Поделиться</a>

		<div class="share-widget">
			<a href="http://twitter.com/home?status=Check%20out%20this%20blog%20post:+<?=$SHARE_DETAIL_PAGE_URL;?>" class="sw-twitter"></a>
			<a href="http://www.facebook.com/sharer.php?u=<?=$SHARE_DETAIL_PAGE_URL;?>&amp;t=<?=$SHARE_NAME;?>" class="sw-facebook"></a>
			<a href="http://www.tumblr.com/share/link?url=<?=$SHARE_DETAIL_PAGE_URL;?>" class="sw-tumblr"></a>
			<a href="https://plus.google.com/share?url=<?=$SHARE_DETAIL_PAGE_URL;?>" onclick="javascript:window.open(this.href,  '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="sw-google"></a>
			<a href="mailto:?subject=<?=$SHARE_NAME;?>&body=Check out this blog post: <?=$SHARE_DETAIL_PAGE_URL;?>" class="sw-mail"></a>
		</div>
	</li>

	<li class="blog-article-meta-item share-stats" data-url="<?=$DETAIL_PAGE_URL_FULL;?>"></li>

</ul>
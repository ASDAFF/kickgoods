

			<? if (!defined("PAGE_TYPE")) { ?>
			</div>
			<? } ?>
			<!-- #page-container -->

			<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "template1", Array(
	"START_FROM" => "0",	// Номер пункта, начиная с которого будет построена навигационная цепочка
		"PATH" => "",	// Путь, для которого будет построена навигационная цепочка (по умолчанию, текущий путь)
		"SITE_ID" => "-",	// Cайт (устанавливается в случае многосайтовой версии, когда DOCUMENT_ROOT у сайтов разный)
	),
	false
);?>

		</div>
		<!-- #content -->



		<footer id="footer" class="has-twitter">

			<ul class="clearfix" id="footer-modules">

				<li class="ft-module " id="about-module">
					<h3>О магазине</h3>
					<div id="about-description" class="clearfix">
						Кикгудс — интернет-магазин изобретений с Кикстартера, большинство из которых сложно найти в России даже при большом желании. Кикгудс нацелен решить эту проблему.
					</div>
					<a id="ft-share-twitter" class="ft-share ir" href="http://www.twitter.com/kickgoods">Twitter</a>
					<a id="ft-share-facebook" class="ft-share ir" href="https://www.facebook.com/pages/Kickgoods/324148094364940">Facebook</a>
					<a id="ft-share-instagram" class="ft-share ir" href="http://instagram.com/kickgoods">Instagram</a>
					<a id="ft-share-vk" class="ft-share ir" href="http://vk.com/kickgoods">Vkontakte</a>
				</li>

				<li class="ft-module" id="contact-module">
					<h3>Координаты</h3>
					<ul id="contact-details">

						<li class="cd-item-1" id="cd-address">
							<a href="/pages/delivery#map">
								<img src="<?=SITE_TEMPLATE_PATH;?>/images/map3.jpg" />
								<br />Москва, Новинский б-р 15
							</a>
							<br />
						</li>

						<li class="cd-item-2" id="cd-email">
<a href="mailto:hello@kickgoods.ru">hello@kickgoods.ru</a>
						</li>

						<li class="cd-item-2" id="cd-phone">
+7 499 678-02-55<br />
8 800 700-23-94<br />
(8:00–22:00, 24/7)
						</li>
					</ul>
				</li>

				<li class="ft-module" id="twitter-module">
					<h3>Твиттер</h3>
					<!--
					<div class="clearfix tweet-area accent-text">
					<span class="twitter-niblet"></span>
					</div>
					<!-- #tweet-area -->

					<a class="twitter-timeline" href="https://twitter.com/kickgoods" data-widget-id="347675763304185857" height="500" data-tweet-limit="1" lang="ru">Tweets by @kickgoods</a>
					<script>
						! function(d, s, id) {
							var js, fjs = d.getElementsByTagName(s)[0],
								p = /^http:/.test(d.location) ? 'http' : 'https';
							if (!d.getElementById(id)) {
								js = d.createElement(s);
								js.id = id;
								js.src = p + "://platform.twitter.com/widgets.js";
								fjs.parentNode.insertBefore(js, fjs);
							}
						}(document, "script", "twitter-wjs");
					</script>

					<div class="twitter-meta">
						<a class="twitter-avatar" href="http://www.twitter.com/kickgoods"></a>
						<!-- #twitter-avatar -->

						<div class="twitter-names">	</div>
						<!-- #twitter-names -->
					</div>
					<!-- #twitter-meta -->
				</li>

				<li class="ft-module" id="mailing-list-module">
					<h3>Подписка</h3>
					<p>Обещаем отправлять вам только самое важное</p>

					<form action="http://kickgoods.us6.list-manage1.com/subscribe/post?u=8779b7aad2d2bead86da08670&amp;id=26c00fa6d5" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
						<input type="email" placeholder="Электропочта" name="EMAIL" id="email-input" />
						<input type="submit" class="btn styled-submit" value=" Подписаться" name="subscribe" id="email-submit" />
					</form>
				</li>

			</ul>

		</footer>
		<!-- #footer -->

	</div>
	<!-- #container -->


	<div id="sub-footer" class="clearfix">

		<div class="footer-left-content">
			<p id="shopify-attr" class="accent-text" role="contentinfo">&copy; 2012&#150;<?=date("Y");?> <a href="/">Кickgoods</a></p>
		</div>
		<!-- #footer-left-content -->

		<div class="yandex_market">
			<a href="http://clck.yandex.ru/redir/dtype=stred/pid=47/cid=1248/*http://market.yandex.ru/shop/170584/reviews/add"><img src="http://savepic.net/4155567.png" border="0" alt="Оцените качество магазина на Яндекс.Маркете." /></a>
		</div>

		<div class="yandex_market1" style="float:left; margin-top:-8px; margin-left:20px">
			<a href="http://clck.yandex.ru/redir/dtype=stred/pid=47/cid=2508/*http://market.yandex.ru/shop/170584/reviews"><img src="http://clck.yandex.ru/redir/dtype=stred/pid=47/cid=2505/*http://grade.market.yandex.ru/?id=170584&action=image&size=0" border="0" width="88" height="31" alt="Читайте отзывы покупателей и оценивайте качество магазина на Яндекс.Маркете" /></a>
		</div>

		<!--<div><a href="http://clck.yandex.ru/redir/dtype=stred/pid=47/cid=2508/*http://market.yandex.ru/shop/170584/reviews"><img src="http://clck.yandex.ru/redir/dtype=stred/pid=47/cid=2505/*http://grade.market.yandex.ru/?id=170584&action=image&size=0" border="0" width="88" height="31" alt="Читайте отзывы покупателей и оценивайте качество магазина на Яндекс.Маркете" /></a></div>-->

		<ul id="payment-options" class="clearfix">
			<li class="payment-option" id="pay-qiwi">QIWI</li>
			<li class="payment-option" id="pay-yandex"><a href="/pages/yandexmoney_guideline/">Yandex</a></li>
			<li class="payment-option" id="pay-mastercard">Mastercard</li>
			<li class="payment-option" id="pay-visa">Visa</li>
		</ul>

	</div>
	<!-- #sub-footer -->


	<div class="seo-footer">
		<strong>Kickgoods.ru</strong> — интернет-магазин изобретений с Kickstarter. Нас интересуют инновационные гаджеты и устройства, которые не только упрощают жизнь наших покупателей, но и помогают раскрывать их творческий потенциал. Кикстертер — крупнейшая краудфандинг-платформа, где находят финансирование по-настоящему уникальные и востребованные проекты. Мы проводим много времени на Кикстартере, чтобы на ранних этапах заметить перспективные решения и как можно скорее порадовать покупателей из России <strong>инновационными гаджетами</strong>, будь то независимые игровые консоли, <strong>умные электронные часы</strong>, <strong>беспроводная акустика</strong>, <strong>кошельки из микроволокна</strong>, <strong>портативные элементы питания</strong>, <strong>многофункциональные мобильные аксессуары</strong>, <strong>удобные стилусы</strong> или революционные <strong>кисточки для цифровой живописи</strong>. На сегодняшний день нашим абсолютным хитом является cпециальная кисть для устройств с сенсорным экраном Sensu Brush, объединяющая кисточку для емкостных тач-устройств и стилус в одном корпусе. Успех кисточки Sensu Brush подтолкнул нас к тому, чтобы теснее работать с цифровыми художниками через социальные сети, где мы активно ведем свой блог о цифровом творчестве и регулярно устраиваем конкурсы с призами. Наши принципы не позволяют нам начать продавать товар, если мы собственноручно не протестируем устройство на предмет пригодности для использования в России и наличия каких-либо недоработок. Адекватная ценовая политика обеспечивается нашими прямыми контактами с компаниями-разработчиками устройств. Мы любим наших покупателей и всегда детально информируем их о статусе покупки — мы не успокоимся, пока долгожданное устройство не окажется в руках у адресата!
	</div>


	<br />

	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include", 
		".default", 
		array(
			"COMPONENT_TEMPLATE" => ".default",
			"AREA_FILE_SHOW" => "file",
			"AREA_FILE_SUFFIX" => "inc",
			"EDIT_TEMPLATE" => "",
			"PATH" => SITE_TEMPLATE_PATH."/includes/ym.php"
		),
		false
	);?>

</body>

</html>
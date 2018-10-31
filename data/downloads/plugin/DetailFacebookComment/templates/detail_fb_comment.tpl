 <!--★FBコメント-->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ja_JP/all.js#xfbml=1&appId=<!--{$detail_fb_comment_app_id|h}-->";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div class="mt20 fb-comments" data-href="<!--{$smarty.const.HTTP_URL}-->products/detail.php?product_id=<!--{$arrProduct.product_id|u}-->" data-num-posts="<!--{$detail_fb_comment_data_num_post|h}-->" data-width="<!--{$detail_fb_comment_data_width|h}-->"></div>
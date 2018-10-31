function show_all_comment(){
	$("li.depth-1").css("display","block");
	$("div.comment_all").css("display","none");
	$("div.comment_limit").css("display","block");
	return false;
}

function show_limit_comment(comment_num){
	var comment_num = comment_num-1;
	$("div#wppost > li.depth-1:gt("+comment_num+")").css("display", "none");
	$("div.comment_limit").css("display","none");
	$("div.comment_all").css("display","block");
}

function comment_bloc(ec_root, wp_root, post_id, comment_id){
    var ec_root = ec_root;
    var wp_root = wp_root;
    var post_id = post_id;
    var comment_id = comment_id;
    $("div#comment_reply").remove();
    $("div.reply a").css("display","inline");
    $("div#page_comment").css("display","none");
    //$("#div-comment-"+comment_id+" .reply a").css("display","none");
    $("#div-comment-"+comment_id+" div.reply a").css("display","none");
    $("#div-comment-"+comment_id+" div.reply").after('<div id="comment_reply"><h3 id="reply-title">このコメントに返信する</h3><div class="comment_cancel"><a rel="nofollow" id="cancel-comment-reply-link" href="#" onclick="return cancel_comment('+comment_id+')">キャンセル</a></div><form action="'+ec_root+wp_root+'/wp-comments-post.php" method="post" id="commentform"><p class="comment-notes">メールアドレスが公開されることはありません。 <span class="required">*</span> が付いている欄は必須項目です</p><p class="comment-form-author"><label for="author">名前</label> <span class="required">*</span><input id="author" name="author" type="text" value="" size="30" aria-required="true"></p><p class="comment-form-email"><label for="email">メールアドレス</label> <span class="required">*</span><input id="email" name="email" type="text" value="" size="30" aria-required="true"></p><p class="comment-form-url"><label for="url">ウェブサイト</label><input id="url" name="url" type="text" value="" size="30"></p><p class="comment-form-comment"><label for="comment">コメント</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p><p class="form-allowed-tags">次の<abbr title="HyperText Markup Language">HTML</abbr> タグと属性が使えます:  <code>&lt;a href="" title=""&gt; &lt;abbr title=""&gt; &lt;acronym title=""&gt; &lt;b&gt; &lt;blockquote cite=""&gt; &lt;cite&gt; &lt;code&gt; &lt;del datetime=""&gt; &lt;em&gt; &lt;i&gt; &lt;q cite=""&gt; &lt;strike&gt; &lt;strong&gt; </code></p><p class="form-submit"><input name="submit" type="submit" id="submit" value="返信する"><input type="hidden" name="comment_post_ID" value="'+post_id+'" id="comment_post_ID"><input type="hidden" name="comment_parent" id="comment_parent" value="'+comment_id+'"><input type="hidden" name="redirect_to" value="'+ec_root+'wppost/plg_WpPost_post.php?postid='+post_id+'" id="redirect_to" /></p></form></div>');
    return false;
}
function cancel_comment(comment_id){
    $("div#comment_reply").remove();
    $("#div-comment-"+comment_id+" .reply a").css("display","inline");
    $("div#page_comment").css("display","block");
    return false;
}

<!--{*
 * WPPost
 * Copyright(c) 2000-2014 GIZMO CO.,LTD. All Rights Reserved.
 * http://www.gizmo.co.jp/
 *
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *}-->
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<h2>WpPost</h2>
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|h}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit">
<p>WpPostの詳細な設定が行えます。<br/>
    <br/>
</p>
<div style="background: #FFC;padding: 10px;margin-bottom: 1em;">
<p style="font-weight: bold;margin-bottom: 10px;">WordPress側の必須作業</strong></p>
WordPressインストールディレクトリの<strong>wp-config.php</strong>書き換え
<ul style="margin-bottom: 10px;">
    <li style="list-style-type: disc; list-style-position: inside;">DB_NAME → WPDB_NAME</li>
    <li style="list-style-type: disc; list-style-position: inside;">DB_USER → WPDB_USER</li>
    <li style="list-style-type: disc; list-style-position: inside;">DB_PASSWORD → WPDB_PASSWORD</li>
    <li style="list-style-type: disc; list-style-position: inside;">DB_HOST → WPDB_HOST</li>
</ul>
WordPressインストールディレクトリの<strong>wp-includes/load.php</strong>書き換え
<ul style="margin-bottom: 10px;">
    <li style="list-style-type: disc; list-style-position: inside;">$wpdb = new wpdb( WPDB_USER, WPDB_PASSWORD, WPDB_NAME, WPDB_HOST );</li>
</ul>
<p>※DB_NAME、DB_USER、DB_PASSWORDはEC-CUBEで既に使われていますので、上記変更をお願いします。WordPressインストール後の変更で構いません</p>
<p>※コメントの承認はWordPress管理画面の設定&nbsp;&gt;&nbsp;ディスカッション設定の「<strong>コメント表示条件</strong>」で設定してください。<p>
<p>※コメントの管理はWordPress管理画面からお願いします。<p>
</div>
<table border="0" cellspacing="1" cellpadding="8" summary=" " style="width: 100%;">
    <tr>
        <td colspan="2" bgcolor="#f3f3f3" style="width: 30%;">▼WPPost詳細設定</td>
    </tr>
    <tr>
    	<td colspan="2" bgcolor="#f3f3f3" style="text-align: center; font-weight: bold;">共通設定</td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">WordPressインストールディレクトリ</td>
        <td>
            <div>※EC-CUBEのhtmlディレクトリを基準としたWordPressのインストール場所への相対パスを入力<br />
            ※htmlの中にwordpressというディレクトリでインストールした場合 wordpress<br />
            ※<a href="http://gizmo.co.jp/plugins/wppost/config/" target="_blank">WpPost 設定</a>をご確認ください。</div>
            <!--{assign var=key value="wp_install_dir"}-->
            <input type="text" class="box60" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">記事が含まれるカテゴリのタイトル</td>
        <td>
            <div>※デフォルト:記事カテゴリー</div>
            <!--{assign var=key value="wp_incat_text"}-->
            <input type="text" class="box60" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">全体で表示しないカテゴリID</td>
        <td>
            <div>※ここで設定したカテゴリIDは子孫カテゴリ含め全体で非表示<br />
            ※複数カテゴリを指定する場合,（カンマ）区切りで入力</div>
            <!--{assign var=key value="wp_total_excludecat"}-->
            <input type="text" class="box60" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">PC CSS</td>
        <td>
        <!--{assign var=key value="css_data"}-->
        <!--{if $arrErr[$key]}--><div class="red"><!--{$arrErr[$key]}--></div><!--{/if}-->
        <textarea name="<!--{$key}-->" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" ><!--{$arrForm[$key]|h}--></textarea><br />
        <span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
        </td>
    </tr>

    <tr>
    	<td colspan="2" bgcolor="#f3f3f3" style="text-align: center; font-weight: bold;">記事内コメント設定</td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">コメントの表示</td>
        <td>
            <!--{assign var=key value="show_comment"}-->
            <!--{if $arrErr[$key]}--><div class="red"><!--{$arrErr[$key]}--></div><!--{/if}-->
            <input type="radio" name="<!--{$key}-->" value="0" <!--{if $arrForm.show_comment == "0"}-->checked<!--{/if}--> >しない</input>
            <input type="radio" name="<!--{$key}-->" value="1" <!--{if $arrForm.show_comment == "1"}-->checked<!--{/if}--> >する</input>
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">コメントの表示順</td>
        <td>
            <!--{assign var=key value="comment_turn"}-->
            <!--{if $arrErr[$key]}--><div class="red"><!--{$arrErr[$key]}--></div><!--{/if}-->
            <input type="radio" name="<!--{$key}-->" value="0" <!--{if $arrForm.comment_turn == "0"}-->checked<!--{/if}--> >新着順</input>
            <input type="radio" name="<!--{$key}-->" value="1" <!--{if $arrForm.comment_turn == "1"}-->checked<!--{/if}--> >古いものから</input>
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">コメントにログイン必須</td>
        <td>
            <!--{assign var=key value="comment_login"}-->
            <!--{if $arrErr[$key]}--><div class="red"><!--{$arrErr[$key]}--></div><!--{/if}-->
            <input type="radio" name="<!--{$key}-->" value="0" <!--{if $arrForm.comment_login == "0"}-->checked<!--{/if}--> >不要</input>
            <input type="radio" name="<!--{$key}-->" value="1" <!--{if $arrForm.comment_login == "1"}-->checked<!--{/if}--> >必要</input>
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">ログイン方法</td>
        <td>
            <div class="red">※コメントにログイン必要の場合必須</div>
            <!--{if ($arrForm.comment_login == "1")}-->
                <!--{if ($arrForm.comment_login_ec != "1") && ($arrForm.comment_login_fb != "1") && ($arrForm.comment_login_tw != "1")}--><div class="red">ログイン方法が選択されていません</div><!--{/if}-->
            <!--{/if}-->
            <input type="checkbox" name="comment_login_ec" value="1" <!--{if $arrForm.comment_login_ec == "1"}-->checked<!--{/if}--> >EC-CUBE会員</input>
            <input type="checkbox" name="comment_login_fb" value="1" <!--{if $arrForm.comment_login_fb == "1"}-->checked<!--{/if}--> >Facebook認証</input>
            <input type="checkbox" name="comment_login_tw" value="1" <!--{if $arrForm.comment_login_tw == "1"}-->checked<!--{/if}--> >Twitter認証</input>
            <div>Facebook認証、Twitter認証にはそれぞれアプリーケーションの登録が必要です。</div>
            <div>Facebookアプリーケーション登録の<a href="http://gizmo.co.jp/wppost/fbapp" target="_blank">詳細はこちらから</a></div>
            <div>Twitterアプリーケーション登録の<a href="http://gizmo.co.jp/wppost/twapp" target="_blank">詳細はこちらから</a></div>
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">Facebook App IDとApp Secre</span></td>
        <td>
            <div class="red">※ログイン方法でFacebookを選択した場合必須</div>
            <!--{assign var=key value="fb_appid"}-->
            <!--{if ($arrForm.comment_login_fb == "1") && ($arrForm.fb_appid == "0")}--><div class="red">Facebook App IDを設定をしてください</div><!--{/if}-->
            <label for="<!--{$key}-->">Facebook App ID</label><input type="text" class="box60" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
            <!--{assign var=key value="fb_secret"}-->
            <!--{if ($arrForm.comment_login_fb == "1") && ($arrForm.fb_secret == "0")}--><div class="red">Facebook App Secretを設定をしてください</div><!--{/if}-->
            <label for="<!--{$key}-->">Facebook App Secret</label><input type="text" class="box60" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">Twitter Consumer keyとConsumer Secret</td>
        <td>
            <div class="red">※ログイン方法でTwitterを選択した場合必須</div>
            <!--{assign var=key value="tw_consumer_key"}-->
            <!--{if ($arrForm.comment_login_tw == "1") && ($arrForm.tw_consumer_key == "")}-->
                <div>Twitter Consumer keyを設定をしてください</div>
            <!--{/if}-->
            <label for="<!--{$key}-->">Twitter Consumer key</label><input type="text" class="box60" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
            <!--{assign var=key value="tw_consumer_secret"}-->
            <!--{if ($arrForm.comment_login_tw == "1") && ($arrForm.tw_consumer_secret == "")}-->
                <div>Twitter Consumer secretを設定をしてください</div>
            <!--{/if}-->
            <label for="<!--{$key}-->">Twitter Consumer secret</label><input type="text" class="box60" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">コメント表示数<br /></td>
        <td>
            <div>※1ページあたりに表示する親コメント数 0で全て表示</div>
            <!--{assign var=key value="comment_num"}-->
            <span class="red"><!--{$arrErr[$key]}--></span>
            <input type="text" class="box60" name="<!--{$key}-->" value="<!--{$arrForm[$key]|default:0|h}-->" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">アバターサイズ</td>
        <td>
            <div>※<a href="https://ja.gravatar.com/" target="_blank">Gravatar</a>で登録されたアバーターのサイズ 
            Gravatarについては<a href="https://ja.gravatar.com/" target="_blank">こちら→</a><br />
            デフォルト:32 表示なし:0</div>
            <!--{assign var=key value="comment_avatar_size"}-->
            <span class="red"><!--{$arrErr[$key]}--></span>
            <input type="text" class="box60" name="<!--{$key}-->" value="<!--{$arrForm[$key]|default:32|h}-->" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">コメント返信リンク表示テキスト</td>
        <td>
            <!--{assign var=key value="comment_restext"}-->
            <span class="red"><!--{$arrErr[$key]}--></span>
            <input type="text" class="box60" name="<!--{$key}-->" value="<!--{$arrForm[$key]|default:'このコメントに返信'|h}-->" maxlength="<!--{$smarty.const.SMTEXT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
        </td>
    </tr>



    <tr>
        <td colspan="2" bgcolor="#f3f3f3" style="text-align: center; font-weight: bold;">ブロック設定</td>
    </tr>

    <tr>
        <td colspan="2" bgcolor="#f3f3f3" style="text-align: center; font-weight: bold;">記事一覧</td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">タイトル</td>
        <td>
            <!--{assign var=key value="postlist_title"}-->
                <input type="text" class="box60" name="<!--{$key}-->" value="<!--{$arrForm[$key]|default:"最新記事"}-->" maxlength="<!--{$smarty.const.SMTEXT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">表示件数</td>
        <td>
            <div>※新着順 全表示:-1</div>
            <!--{assign var=key value="postlist_num"}-->
            <input type="text" class="box60" name="<!--{$key}-->" value="<!--{$arrForm[$key]|default:5|h}-->" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">表示形式</td>
        <td>
            <div>※下の「表示カテゴリー」を入力した場合、固定ページはカテゴリを持っていないため表示されなくなります。</div>
            <!--{assign var=key value="postlist_format"}-->
            <span class="red"><!--{$arrErr[$key]}--></span>
            <input type="radio" name="<!--{$key}-->" value="1" <!--{if $arrForm[$key] == "1"}-->checked<!--{/if}--> >記事のみ</input><br/>
            <input type="radio" name="<!--{$key}-->" value="2" <!--{if $arrForm[$key] == "2"}-->checked<!--{/if}--> >固定ページのみ</input>
            <input type="radio" name="<!--{$key}-->" value="3" <!--{if $arrForm[$key] == "3" || $arrForm[$key] == ""}-->checked<!--{/if}--> >記事&amp;固定ページ</input>
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">表示カテゴリー</td>
        <td>
            <div>※表示するカテゴリIDを指定 複数を指定する場合,（カンマ）区切りで入力<br />
            ※全表示:ブランク</div>
            <!--{assign var=key value="postlist_include"}-->
            <input type="text" class="box60" name="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">表示除外記事</td>
        <td>
            <div>※表示しない記事IDを指定 複数を指定する場合,（カンマ）区切りで入力<br />
            ※全表示:ブランク</div>
            <!--{assign var=key value="postlist_exclude"}-->
            <input type="text" class="box60" name="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
        </td>
    </tr>

</table>

<div class="btn-area">
    <ul>
        <li>
            <a class="btn-action" href="javascript:;" onclick="document.form1.submit();return false;"><span class="btn-next">この内容で登録する</span></a>
        </li>
    </ul>
</div>

</form>
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->

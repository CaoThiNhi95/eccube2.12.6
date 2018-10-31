
<!--{$arrPeriodicalOrder.order_name01}--> <!--{$arrPeriodicalOrder.order_name02}--> 様

<!--{$tpl_header}-->

************************************************
　定期購入内容
************************************************

定期注文番号: <!--{$arrPeriodicalOrder.periodical_order_id}-->
お支払い方法: <!--{$arrPeriodicalOrder.payment_method}-->
<!--{if $arrPeriodicalOrder.period_type == constant("PeriodicalSale::PERIOD_TYPE_WEEKLY")}-->
お届け周期: <!--{$arrPERIODTYPES[$arrPeriodicalOrder.period_type]|h}--><!--{$arrDAYS[$arrPeriodicalOrder.period_day]}-->曜日
<!--{elseif $arrPeriodicalOrder.period_type == constant("PeriodicalSale::PERIOD_TYPE_BIWEEKLY")}-->
お届け周期: <!--{$arrPERIODTYPES[$arrPeriodicalOrder.period_type]|h}--><!--{$arrDAYS[$arrPeriodicalOrder.period_day]}-->曜日
<!--{elseif $arrPeriodicalOrder.period_type == constant("PeriodicalSale::PERIOD_TYPE_MONTHLY_DAY")}-->
お届け周期: <!--{$arrPERIODTYPES[$arrPeriodicalOrder.period_type]|h}--> 第<!--{$arrWEEKS[$arrPeriodicalOrder.period_week]}--><!--{$arrDAYS[$arrPeriodicalOrder.period_day]}-->曜日
<!--{elseif $arrPeriodicalOrder.period_type == constant("PeriodicalSale::PERIOD_TYPE_MONTHLY_DATE")}-->
お届け周期: <!--{$arrPERIODTYPES[$arrPeriodicalOrder.period_type]|h}--><!--{$arrDATES[$arrPeriodicalOrder.period_date]}-->日
<!--{/if}-->
お届け時間: <!--{$arrDELIVERYTIMES[$arrPeriodicalOrder.period_delivery_time]|default:"指定なし"}-->
<!--{assign var=day value=$arrPeriodicalOrder.next_period|date_format:"%w"}-->
次回予定日: <!--{$arrPeriodicalOrder.next_period|date_format:"%Y/%m/%d"}--> (<!--{$arrDAYS[$day]}-->)

<!--{if count($arrPeriodicalOrder.periodical_shippings) >= 1}-->
************************************************
　配送情報
************************************************

<!--{foreach from=$arrPeriodicalOrder.periodical_shippings item=arrPeriodicalShipping}-->
◎お届け先<!--{if count($arrShipping) > 1}--><!--{$smarty.foreach.shipping.iteration}--><!--{/if}-->

　お名前　: <!--{$arrPeriodicalShipping.shipping_name01}--> <!--{$arrPeriodicalShipping.shipping_name02}-->　様
　郵便番号: 〒<!--{$arrPeriodicalShipping.shipping_zip01}-->-<!--{$arrPeriodicalShipping.shipping_zip02}-->
　住所　　: <!--{$arrPREFS[$arrPeriodicalShipping.shipping_pref]}--><!--{$arrPeriodicalShipping.shipping_addr01}--><!--{$arrPeriodicalShipping.shipping_addr02}-->
　電話番号: <!--{$arrPeriodicalShipping.shipping_tel01}-->-<!--{$arrPeriodicalShipping.shipping_tel02}-->-<!--{$arrPeriodicalShipping.shipping_tel03}-->
　FAX番号: <!--{if $arrPeriodicalShipping.shipping_fax01 > 0}--><!--{$arrPeriodicalShipping.shipping_fax01}-->-<!--{$arrPeriodicalShipping.shipping_fax02}-->-<!--{$arrPeriodicalShipping.shipping_fax03}--><!--{/if}-->

<!--{/foreach}-->
<!--{/if}-->

************************************************
　定期商品明細
************************************************

<!--{foreach from=$arrPeriodicalOrder.periodical_order_details item=arrPeriodicalOrderDetail}-->
商品コード: <!--{$arrPeriodicalOrderDetail.product_code}-->
商品名: <!--{$arrPeriodicalOrderDetail.product_name}--> <!--{$arrPeriodicalOrderDetail.classcategory_name1}--> <!--{$arrPeriodicalOrderDetail.classcategory_name2}-->
単価: ￥ <!--{$arrPeriodicalOrderDetail.price|sfCalcIncTax|number_format}-->
数量: <!--{$arrPeriodicalOrderDetail.quantity}-->
<!--{$smarty.const.HTTP_URL|cat:'products/detail.php?product_id='|cat:$arrPeriodicalOrderDetail.product_id}-->

<!--{/foreach}-->
-------------------------------------------------
小　計 ￥ <!--{$arrPeriodicalOrder.subtotal|number_format|default:0}--> (うち消費税 ￥<!--{$arrPeriodicalOrder.tax|number_format|default:0}-->）
値引き ￥ <!--{$arrPeriodicalOrder.discount|number_format|default:0}-->
送　料 ￥ <!--{$arrPeriodicalOrder.deliv_fee|number_format|default:0}-->
手数料 ￥ <!--{$arrPeriodicalOrder.charge|number_format|default:0}-->
============================================
合　計 ￥ <!--{$arrPeriodicalOrder.payment_total|number_format|default:0}-->
<!--{$tpl_footer}-->
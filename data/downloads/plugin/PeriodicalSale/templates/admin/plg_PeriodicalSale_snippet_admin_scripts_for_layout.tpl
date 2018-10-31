<!--{*
 * PeriodicalSale
 * Copyright(c) 2015 DAISY Inc. All Rights Reserved.
 *
 * http://www.daisy.link/
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
 
 <style>
.plgPeriodicalSaleCommittable,
.plgPeriodicalSaleCommitted,
.plgPeriodicalSaleUncommittable{
    display:inline-block;
    padding:5px;
    font-size:10px;
    /display:inline;
    /zoom:1;
}
    
.plgPeriodicalSaleCommittable{
    background:#bfdfff;
}

.plgPeriodicalSaleUncommittable{
    background:#c9c9c9;
}

.plgPeriodicalSaleCommitted{
    background:#ffd9d9;
}
</style>

<script type="text/javascript">
$(function(){
    
    $('.plgPeriodicalSaleCommittable, .plgPeriodicalSaleCommitted, .plgPeriodicalSaleUncommittable')
        .each(function(){
            if($(this).hasClass('plgPeriodicalSaleCommittable')){
                $(this).text('発送完了');
            }
            else if($(this).hasClass('plgPeriodicalSaleCommitted')){
                $(this).text('発送準備中');
            }
            else if($(this).hasClass('plgPeriodicalSaleUncommittable')){
                $(this).text('休止・解約');
            }
        });
});
</script>
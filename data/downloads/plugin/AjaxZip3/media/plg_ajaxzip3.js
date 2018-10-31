// 郵便番号入力呼び出し.(ajaxzip3を利用)
function fnCallAddress(php_url, tagname1, tagname2, input1, input2, ignoreWarning) {
    if(typeof AjaxZip3 == "undefined") {
        alert('AjaxZip3を読み込めませんでした');
        return false;
    }
    zip1 = document.form1[tagname1].value;
    zip2 = document.form1[tagname2].value;

    if(zip1.length == 3 && zip2.length == 4) {
        AjaxZip3.zip2addr(tagname1, tagname2, input1, input2);
    } else {
        if(ignoreWarning) {
            return;
        }
        alert("郵便番号を正しく入力して下さい。");
    }
}


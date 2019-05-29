function getList(){
    $j.get(splConfig.url,{
        master:splConfig.rid,
        mode:"getList",
    },function (data) {
        $j('.js-product-links').html(data);

        if($j('.js-product-links tr').length>0){
            tableWrap.show();
        }
        else{
            tableWrap.hide();
        }
    })
}

var $j = jQuery;
var splConfig;
var tableWrap;
var $select2;
$j(document).ready(function () {
    tableWrap = $j('#product-links-table-wrap');
    splConfig = {
        rid:simpleTabRid,
        url:'/assets/plugins/simpleproductlinks/ajax.php',
    };

    $select2 = jQuery("#product-search").select2({
        width: 'resolve',
        ajax: {
            url: splConfig.url,
            dataType: 'json'
        }
    });

    //fix кнопок
    var fixTime = setInterval(function () {
        var height = $j('.select2-container').height();
        if(height>0){
            clearInterval(fixTime);
            console.log(height)
            $j('.action-wrap [type="button"]').outerHeight(height)
        }
    },100)


    getList();

});




$j(document)
    .on('click','.js-product-links-update',function (e) {
        getList()
    })
    .on('click','.js-remove-product-link',function (e) {
        e.preventDefault()
        var id = $j(this).data('id');

        $j.get(splConfig.url,{
            master:id,
            mode:"remove",
        },function () {
            getList()
        })

    })
    .on('click', '#add-product-links', function () {
        var value = $select2.val();
        $j.get(splConfig.url,{
            master:splConfig.rid,
            slave:value,
            mode:"create",
        },function () {
            getList()
        })
    })
    .on("click",".on-new-tab",function(e) {


        e.preventDefault();
        var obj = {
            url:$j(this).attr("href"),
            title:$j(this).text(),
        };
        parent.modx.tabs(obj)
    })
;

<div id="SimpleProductLinks" class="tab-page">

    <h2 class="tab" id="spl-tab">[+tabName+]</h2>

    <div class="table-responsive" id="product-links-table-wrap">
        <table class="table data" cellpadding="1" cellspacing="1">
            <thead>
            <tr>
                <td class="tableHeader" width="34">Id</td>
                <td class="tableHeader">Товар</td>
                <td class="tableHeader" width="60">Удалить</td>
            </tr>
            </thead>
            <tbody class="js-product-links">

            </tbody>
        </table>
    </div>

    <div class="action-wrap">

        <div class="select-wrap">
            <select id="product-search" class="product-search" style="width: 100%"></select>
        </div>
        <input type="button" class="btn btn-success" value="Добавить" id="add-product-links">
        <input type="button" value="Обновить" class="js-product-links-update">

    </div>

    <style>
        .tableItem{
            vertical-align: middle !important;
        }
        .select-wrap {
            width: 30%;
          float:left;
            margin-right: 10px;
        }

        .select2-container--default .select2-selection--single{
            border-radius: 0;
        }
        .action-wrap{
            margin-top: 20px;
        }
    </style>

    <script>
        var tableWrap = $('#product-links-table-wrap');
        var splConfig = {
            rid:[+id+],
            url:'/assets/plugins/simpleproductlinks/ajax.php',
        };

        $select2 = jQuery("#product-search").select2({
            width: 'resolve',
            ajax: {
                url: splConfig.url,
                dataType: 'json'
            }
        });
        function getList(){
            $.get(splConfig.url,{
                master:splConfig.rid,
                mode:"getList",
            },function (data) {
                $('.js-product-links').html(data);

                if($('.js-product-links tr').length>0){
                    tableWrap.show();
                }
                else{
                    tableWrap.hide();
                }
            })
        }
        getList();
        $(document)
            .on('click','.js-product-links-update',function (e) {
                getList()
            })
            .on('click','.js-remove-product-link',function (e) {
                e.preventDefault()
                var id = $(this).data('id');

                $.get(splConfig.url,{
                    master:id,
                    mode:"remove",
                },function () {
                    getList()
                })

            })
            .on('click', '#add-product-links', function () {
                var value = $select2.val();
                $.get(splConfig.url,{
                    master:splConfig.rid,
                    slave:value,
                    mode:"create",
                },function () {
                    getList()
                })
            })
        ;

        //fix кнопок

        var fixTime = setInterval(function () {
            var height = $('.select2-container').height();
            if(height>0){
                clearInterval(fixTime);
                console.log(height)
                $('.action-wrap [type="button"]').outerHeight(height)
            }
        },100)


    </script>

</div>

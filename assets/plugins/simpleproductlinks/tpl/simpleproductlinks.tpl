<div id="SimpleProductLinks" class="tab-page">

    <h2 class="tab" id="spl-tab">[+tabName+]</h2>

    <div class="table-responsive js-product-links" id="product-links-table-wrap">


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
        #SimpleProductLinks .btn-cell-header{
            max-width: 100px;
            width: 100px

        }
        #SimpleProductLinks .id-cell-head{
            max-width: 30px;
            width: 30px

        }



    </style>
    <script>
        var simpleTabRid = [+id+];
    </script>

</div>

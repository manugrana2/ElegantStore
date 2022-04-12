<!DOCTYPE html>
<html>
<head>
    <title>Paginationjs example</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="../dist/pagination.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        ul, li {
            list-style: none;
        }

        #wrapper {
            width: 900px;
            margin: 20px auto;
        }

        .data-container {
            margin-top: 20px;
        }

        .data-container ul {
            padding: 0;
            margin: 0;
        }

        .data-container li {
            margin-bottom: 5px;
            padding: 5px 10px;
            background: #eee;
            color: #666;
        }
    </style>
</head>
<body>

<div id="wrapper">
    <section>
        <div class="data-container"></div>
        <div id="pagination-demo1"></div>
        <div class="data-container"></div>
        <div id="pagination-demo2"></div>
    </section>
</div>

<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
<script src="/assets/javascripts/pagination.js"></script>
<script>
$(function() {
  (function(name) {
    var container = $('#pagination-' + name);
    var sources = function () {
      var result = [];

      for (var i = 1; i < 196; i++) {
        result.push(i);
      }

      return result;
    }();

    var options = {
        dataSource: '/api/articles.php',
        locator: 'products',
        totalNumber: 120,
        pageSize: 20,
      callback: function (response, pagination) {
        window.console && console.log(response, pagination);

        var dataHtml = '<ul>';

        $.each(response, function (index, item) {
          console.log(item);
          dataHtml += '<li>' + item.title + '</li>';
        });

        dataHtml += '</ul>';

        container.prev().html(dataHtml);
      }
    };

    //$.pagination(container, options);

    container.addHook('beforeInit', function () {
      window.console && console.log('beforeInit...');
    });
    container.pagination(options);

    container.addHook('beforePageOnClick', function () {
      window.console && console.log('beforePageOnClick...');
      //return false
    });
  })('demo1');
})
</script>
</body>
</html>
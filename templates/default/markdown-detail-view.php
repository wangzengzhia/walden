<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="utf-8" />
    <title><?= $title ?> - <?= Bootstrap::DOC_NAME ?></title>
    <meta name="keywords" content="documentation,dox" />
    <meta name="description" content="Generate your documentation." />
    <script src="/static/prettify.js"></script>
    <script src="/static/jquery-1.8.2.min.js"></script>
    <script src="/static/bootstrap/js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="/static/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/static/base.css" />
</head>
<body>
<nav class="navbar navbar-inverse navbar-static-top top-navbar" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"><?= Bootstrap::DOC_NAME ?></a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <?php foreach (DirectoryIndex::getProjects() as $project) { ?>
                <li><a href="<?= $project['link'] ?>"><?= $project['title'] ?></a></li>
                <?php } ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php if (isset($editUrl)) { ?><li><a href="<?= $editUrl?>">编辑</a></li><?php } ?>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div>
</nav>
<header class="jumbotron subhead">
    <div class="container">
        <h1><?= Bootstrap::DOC_NAME ?> <small>瓦尔登 文档工具</small></h1>
    </div>
</header>

<div class="container content">
    <div class="row">
        <div class="col-md-3" id="accordion" role="tablist" aria-multiselectable="true">
            <ul class="nav nav-list bs-docs-sidenav affix">
                <!--文档的目录 start-->
                <?php foreach ($index as $item) { ?>
                    <?php if ($item['type'] == DirectoryIndex::TYPE_FILE) { ?>
                        <li class="level_1">
                        <a href="<?= $item['link'] ?>"><?= $item['title'] ?><i class="icon-chevron-right"></i></a>
                        </li>
                    <?php } else { ?>
                        <li class="level_1">
                            <a href="<?= $item['type'] == DirectoryIndex::TYPE_FILE ? $item['link'] : '#' . $item['title']; ?>"
                               role="button"
                               data-dir="<?= $item['link'] ?>"
                               data-collapse="<?= $item['title'] ?>"
                               data-toggle="collapse"
                               data-parent="#accordion"
                               aria-expanded="true"
                               aria-controls="collapseOne"
                               class="<?= $item['type'] == DirectoryIndex::TYPE_DIR ? 'list-dir' : ''?> "
                            >
                                <?= $item['title'] ?>
                                <i class="icon-chevron-right glyphicon glyphicon-folder-open"></i>
                            </a>
                            <ul id="<?= $item['title'] ?>" class="bs-docs-left-nav panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne"></ul>
                        </li>
                    <?php } ?>
                <?php } ?>
                <!--文档的目录 end-->
            </ul>

        </div>
        <div class="col-md-9">
            <!--文档中文内容 start-->
            <?= $content ?>
            <!--文档中文内容 end-->
        </div>
    </div>
</div>
<footer class="footer">
    <div class="container">
        <p class="pull-right">
            <a href="#">Back to top</a>
        </p>
        <ul class="footer-links">
            <li><a href="http://walden.huamanshu.com" target="_blank">walden主页</a></li>
            <li><a href="https://github.com/meolu/walden" target="_blank">walden源码</a></li>
            <li><a href="https://github.com/meolu/walden/issues?state=open" target="_blank">提交bug</a></li>
        </ul>
    </div>
</footer>
<script>
    $(function() {
        $('pre').addClass('prettyprint');
        $('td pre').removeClass('prettyprint');
        prettyPrint();
        var $window = $(window);
        var sidenav = $('.bs-docs-sidenav');
        if (sidenav.height() < window.innerHeight) {
            sidenav.affix({
                offset: {
                    top: function () {
                        return $window.width() <= 980 ? 290 : 210
                    },
                    bottom: 200
                }
            });
        } else {
            sidenav.removeClass('affix');
        }
        $(".content").find('h1, h2, h3, h4, h5, h6').each(function () {
            var node = $(this);
            // 总是设置id
            node.attr("id", node.data('id') || "index_" + node.text());
        });

        $('.bs-docs-sidenav .accordion-marker').on('click', function(event) {
            var current = $(event.currentTarget);
            current.find('.glyphicon').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
        });

        var getDir = function($this, e) {
            $.get($this.data('dir'), function(o) {
                var list = '';
                $.each(o.data, function (key, data) {
                    if (data.type == 'd') {
                        var dir = sprintf('<ul id="%s" class="bs-docs-left-nav panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne"></ul>',
                            data.title)
                        list += sprintf('<li class="level_1">' +
                            '<a href="#%s" role="button" data-dir="%s" data-collapse="%s" data-toggle="collapse" ' +
                            'data-parent="#accordion" aria-expanded="true" aria-controls="collapseOne" class="list-dir"' +
                            '>%s<i class="icon-chevron-right glyphicon glyphicon-folder-open"></i></a>' +
                            '%s' +
                            '</li>',
                            data.title, data.link, data.title, data.title, dir)
                    } else {
                        list += sprintf('<li class="level_1">' +
                            '<a href="%s">%s<i class="icon-chevron-right"></i></a>' +
                            '</li>',
                            data.link, data.title)
                    }

                })

                $('#' + $this.data('collapse')).html(list)
                $('.list-dir').on('click', function (e) {
                    getDir($(this), e)
                })
                $('.collapse').collapse()

            })
        }
        $('.list-dir').click(function (e) {
            $this = $(this);
            getDir($(this), e)

        })
        <?php if (isset($_GET['action']) && urldecode($_GET['action']) == Bootstrap::PUSH_GIT_URL) { ?>
        // 是否为编辑后的第一次文档预览，需要推送到git
        $.get('<?= Bootstrap::PUSH_GIT_URL ?>', function (o) {
            console.log(o);
        })
        <?php } ?>
    });
</script>
</body>
</html>
